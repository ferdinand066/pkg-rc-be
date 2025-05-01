<?php

namespace App\Http\Services;

use App\Models\Item;
use App\Models\User;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ItemService
{
    public function index(array $data = null) {
        if ($data !== null) extract($data);

        return Item::when($orderBy, function ($q, $orderBy) use ($dataOrder) {
            return $q->orderBy($orderBy, $dataOrder ?? 'asc');
        }, function ($q) {
            return $q->orderBy('name', 'asc');
        })
        ->when((request()->paginate == "true") ?? false, function ($query){
            return $query->with('roomItems.room')->paginate(10)->onEachSide(10)->withQueryString();
        }, function($query){
            return $query->get();
        });
    }

    public function create($data){
        return Item::create([
            'name' => $data['name'],
        ]);
    }

    public function update(Item $Item, $data){
        return Item::where('id', $Item->id)->update([
            'name' => $data['name'],
            'floor_id' => $data['floor_id'],
        ]);
    }
}
