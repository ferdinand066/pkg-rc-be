<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\CreateItemRequest;
use App\Http\Requests\Admin\UpdateItemRequest;
use App\Http\Services\ItemService;
use App\Http\Services\RoomItemService;
use App\Models\Item;
use Symfony\Component\HttpFoundation\Response;

class ItemController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(ItemService $service)
    {
        $data = $this->getSearchAndSort();
        $items = $service->index($data);

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Berhasil mendapatkan data barang', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateItemRequest $request, ItemService $service, RoomItemService $roomItemService)
    {
        $validated = $request->validated();

        $item = $service->create($validated);
        $roomItemService->create($item->id, $validated['room_items']);

        return $this->sendResponse(Response::HTTP_CREATED, 'Berhasil membuat data barang baru', compact('item'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        $item->load([
            'roomItems' => fn ($query) => $query->whereHas('room'),
            'roomItems.room',
        ]);
        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Berhasil mendapatkan data barang', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, Item $item, ItemService $service)
    {
        $validated = $request->validated();
        $service->update($item, $validated);

        return $this->sendResponse(Response::HTTP_CREATED, 'Berhasil mengubah data barang!', compact('item'));
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
