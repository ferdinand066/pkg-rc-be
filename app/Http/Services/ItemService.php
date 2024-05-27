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
