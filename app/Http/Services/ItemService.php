<?php

namespace App\Http\Services;

use App\Models\Item;

class ItemService
{
    public function index(?array $data = null)
    {
        if ($data !== null) extract($data);

        return Item::when($orderBy, function ($q, $orderBy) use ($dataOrder) {
            return $q->orderBy($orderBy, $dataOrder ?? 'asc');
        }, function ($q) {
            return $q->orderBy('name', 'asc');
        })
            ->when((request()->paginate == "true") ?? false, function ($query) {
                return $query->with('roomItems.room')->paginate(10)->onEachSide(10)->withQueryString();
            }, function ($query) {
                return $query->get();
            });
    }

    public function create($data)
    {
        return Item::create([
            'name' => $data['name'],
            'idle_quantity' => $data['idle_quantity'],
        ]);
    }

    public function update(Item $Item, $data)
    {
        return Item::where('id', $Item->id)->update([
            'name' => $data['name'],
        ]);
    }
}
