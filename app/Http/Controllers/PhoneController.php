<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $phones = \App\Models\Phone::with(['body', 'platform', 'camera', 'connectivity', 'battery', 'benchmarks'])
            ->orderBy('overall_score', 'desc')
            ->paginate(12);

        return view('phones.index', compact('phones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\Phone $phone)
    {
        $phone->load(['body', 'platform', 'camera', 'connectivity', 'battery', 'benchmarks']);
        
        return view('phones.show', compact('phone'));
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
    public function destroy(string $id)
    {
        //
    }
}
