<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TorreController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index() {
        $torres = \App\Models\Torre::all();
        return view('admin.torres.index', compact('torres'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $request->validate(['nombre' => 'required|string|max:255']);
        \App\Models\Torre::create($request->all());
        return back()->with('success', 'Torre/Bloque creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\App\Models\Torre $torre) {
        $torre->delete();
        return back()->with('success', 'Torre eliminada.');
    }
}