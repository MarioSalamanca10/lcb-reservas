<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Espacio;
use App\Models\ReservaFisica;

class EspacioRecursoController extends Controller
{


    public function index()
    {
        // CANDADO MANUAL SEGURO
        if (!in_array(auth()->user()->rol, ['admin', 'admin_espacios'])) {
            abort(403, 'Acceso Denegado. Panel exclusivo para Administración de Espacios.');
        }
        // Traemos todos los espacios, pero le exigimos que incluya la 'torre' a la que pertenecen
        $espacios = \App\Models\Espacio::with('torre')->orderBy('created_at', 'desc')->paginate(10);
        
        return view('espacios.index', compact('espacios'));
    }

    public function create()
    {
        $torres = \App\Models\Torre::all(); // <-- Buscar torres
        return view('espacios.create', compact('torres')); // <-- Enviarlas
    }

   public function store(Request $request)
    {
        // 1. Validamos solo lo que nos pediste
        $request->validate([
            'nombre' => 'required|string|max:255',
            'torre_id' => 'required|exists:torres,id',
            'capacidad_personas' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Máximo 2MB para cuidar tu SiteGround
        ]);

        // 2. Lógica de guardado de imagen
        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            // Guarda la foto en la carpeta 'espacios' y nos devuelve la ruta
            $path = $request->file('imagen')->store('espacios', 'public');
            $rutaImagen = 'storage/' . $path; // Ej: storage/espacios/foto.jpg
        }

        // 3. Crear el espacio en la base de datos
        \App\Models\Espacio::create([
            'nombre' => $request->nombre,
            'torre_id' => $request->torre_id,
            'capacidad_personas' => $request->capacidad_personas,
            'descripcion' => $request->descripcion,
            'imagen_url' => $rutaImagen,
            // Valores por defecto invisibles para que MySQL no se queje
            'tipo' => 'General', 
            'categoria' => 'General',
            'activo' => true,
            'tiene_videobeam' => false,
        ]);

        return redirect()->route('espacios.index')->with('success', '¡Espacio creado exitosamente!');
    }

    // 1. Muestra el formulario con los datos cargados
    public function edit($id)
    {
        $espacio = \App\Models\Espacio::findOrFail($id);
        $torres = \App\Models\Torre::all();
        
        return view('espacios.edit', compact('espacio', 'torres'));
    }

    // 2. Procesa los cambios y guarda la nueva información (y la foto si cambia)
    public function update(Request $request, $id)
    {
        $espacio = \App\Models\Espacio::findOrFail($id);

        // Validamos exactamente los mismos campos del crear
        $request->validate([
            'nombre' => 'required|string|max:255',
            'torre_id' => 'required|exists:torres,id',
            'capacidad_personas' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Mantenemos la ruta de la imagen actual por defecto
        $rutaImagen = $espacio->imagen_url;

        // Si el usuario subió una NUEVA foto...
        if ($request->hasFile('imagen')) {
            // Opcional: Borramos la foto vieja del servidor para no acumular basura en SiteGround
            if ($espacio->imagen_url && file_exists(public_path($espacio->imagen_url))) {
                @unlink(public_path($espacio->imagen_url));
            }
            
            // Guardamos la nueva
            $path = $request->file('imagen')->store('espacios', 'public');
            $rutaImagen = 'storage/' . $path;
        }

        // Actualizamos el registro en la base de datos
        $espacio->update([
            'nombre' => $request->nombre,
            'torre_id' => $request->torre_id,
            'capacidad_personas' => $request->capacidad_personas,
            'descripcion' => $request->descripcion,
            'imagen_url' => $rutaImagen,
        ]);

        return redirect()->route('espacios.index')->with('success', '¡Espacio actualizado correctamente!');
    }

    // Elimina un espacio del sistema
    public function destroy($id)
    {
        $espacio = \App\Models\Espacio::findOrFail($id);
        $espacio->delete();
        return redirect()->route('espacios.index');
    }

    public function dashboard()
    {
        $totalEspacios = \App\Models\Espacio::count();
        $totalReservas = \App\Models\ReservaFisica::count();
        // CAMBIO AQUÍ: 'fecha' por 'fecha_inicio'
        $reservasHoy = \App\Models\ReservaFisica::whereDate('fecha_inicio', now())->count();

        $ultimasReservas = \App\Models\ReservaFisica::with('espacio')
                            ->orderBy('created_at', 'desc')
                            ->take(10)
                            ->get();

        return view('admin.dashboard', compact('totalEspacios', 'totalReservas', 'reservasHoy', 'ultimasReservas'));
    }
    
    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt'
        ]);

        $archivoRuta = $request->file('archivo')->getRealPath();
        $archivo = fopen($archivoRuta, "r");
        
        $primeraLinea = fgets($archivo);
        $delimitador = strpos($primeraLinea, ';') !== false ? ';' : ',';
        rewind($archivo); 

        // Leer y limpiar encabezados
        $encabezadosBrutos = fgetcsv($archivo, 1000, $delimitador);
        $encabezados = array_map(function($h) {
            $limpio = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h); // Quita caracteres basura de Excel
            return trim(strtolower($limpio)); // Todo en minúscula (torre, recurso, capacidad, estado)
        }, $encabezadosBrutos);

        $espaciosCreados = 0;
        $torresCreadas = 0;

        while (($fila = fgetcsv($archivo, 1000, $delimitador)) !== FALSE) {
            if (count($encabezados) != count($fila)) continue; 
            
            $datos = array_combine($encabezados, $fila);

            // 1. Extraer los datos con los nombres EXACTOS de tu archivo
            $nombreTorre = $datos['torre'] ?? 'Bloque General';
            $nombreEspacio = $datos['recurso'] ?? 'Sin Nombre';
            
            // 2. Limpiar la capacidad (Convierte "74 PERSONAS" en el número matemático 74)
            $capacidadTexto = $datos['capacidad'] ?? '1';
            $capacidadNumero = (int) filter_var($capacidadTexto, FILTER_SANITIZE_NUMBER_INT);
            if ($capacidadNumero <= 0) $capacidadNumero = 1; // Seguridad mínima

            // 3. Evaluar el Estado
            $estadoTexto = strtoupper(trim($datos['estado'] ?? 'ACTIVO'));
            $esActivo = ($estadoTexto === 'ACTIVO');

            // 4. Crear la torre si no existe
            $torre = \App\Models\Torre::firstOrCreate(
                ['nombre' => trim($nombreTorre)]
            );

            if ($torre->wasRecentlyCreated) {
                $torresCreadas++;
            }

            // 5. Crear o actualizar el espacio
            \App\Models\Espacio::updateOrCreate(
                [
                    'nombre' => trim($nombreEspacio),
                    'torre_id' => $torre->id,
                ],
                [
                    'capacidad_personas' => $capacidadNumero,
                    'activo' => $esActivo,
                    'tipo' => 'General', // Dejamos tipo por si la BD base lo exige, pero quitamos categoria
                    'descripcion' => 'Importado vía LCB CSV',
                ]
            );
            
            $espaciosCreados++;
        }

        fclose($archivo);

        return back()->with('success', "¡Importación Exitosa! Se registraron o actualizaron $espaciosCreados espacios y se detectaron $torresCreadas bloques.");
    }
}