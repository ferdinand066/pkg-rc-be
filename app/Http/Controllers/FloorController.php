<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FloorController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $floors = Floor::orderBy('created_at', 'asc')->get();

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Berhasil mendapatkan data ruangan!', compact('floors'));
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Floor $floor)
    {
        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Successfully get floors', compact('floor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Floor $floor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Floor $floor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Floor $floor)
    {
        //
    }
}
