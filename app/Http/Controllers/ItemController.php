<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\CreateItemRequest;
use App\Http\Requests\Admin\UpdateItemRequest;
use App\Http\Services\ItemService;
use App\Models\Item;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ItemController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::with('roomItems.room')->orderBy('name', 'asc')->get();

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Berhasil mendapatkan data barang', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateItemRequest $request, ItemService $service)
    {
        $validated = $request->validated();
        $item = $service->create($validated);

        return $this->sendResponse(Response::HTTP_CREATED, 'Berhasil membuat data barang baru', compact('item'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Berhasil mendapatkan data barang', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, Item $item, ItemService $service)
    {
        $validated = $request->validated();
        $service->update($item, $validated);

        return $this->sendResponse(Response::HTTP_CREATED, 'Berhasil mengubah data barang!', compact('room'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return $this->sendResponse(Response::HTTP_OK, 'Berhasil menghapus data barang!', []);
    }
}
