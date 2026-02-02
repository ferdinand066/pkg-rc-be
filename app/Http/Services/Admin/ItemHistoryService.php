<?php

namespace App\Http\Services\Admin;

use App\Models\Item;
use App\Models\ItemHistory;
use App\Models\RoomItem;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ItemHistoryService
{
    public function index(?array $data = null)
    {
        if ($data !== null) extract($data);
        $searchFields = ['name', 'email'];

        return ItemHistory::with('item', 'room', 'user')->when($orderBy, function ($q, $orderBy) use ($dataOrder) {
            if ($orderBy === 'item_name') {
                return $q->join('items', 'item_histories.item_id', '=', 'items.id')
                    ->orderBy('items.name', $dataOrder ?? 'asc')
                    ->select('item_histories.*');
            }

            return $q->orderBy($orderBy, $dataOrder ?? 'asc');
        }, function ($q) {
            return $q->orderBy('name', 'asc');
        })
            ->when($search ?? false, function ($q) use ($search, $searchFields) {
                $q->where(function ($query) use ($search, $searchFields) {
                    foreach ($searchFields as $field) {
                        $query->orWhere($field, 'like', '%' . $search . '%');
                    }
                });
            })
            ->paginate(10)
            ->onEachSide(1)
            ->withQueryString();
    }

    public function create($data)
    {
        $this->updateItemQuantity($data);
        return ItemHistory::create([
            ...$data,
            'user_id' => Auth::user()->id,
        ]);
    }

    private function updateItemQuantity(array $data): void
    {
        $isRemoved = $data['type'] === 'removed';
        $isRoomContext = $data['room_id'] !== null;

        if ($isRemoved && $isRoomContext) {
            $this->decreaseRoomItemQuantity($data);
            return;
        }

        if ($isRemoved && !$isRoomContext) {
            $this->decreaseItemIdleQuantity($data);
            return;
        }

        if (!$isRemoved && $isRoomContext) {
            $this->increaseRoomItemQuantity($data);
            return;
        }

        $this->increaseItemIdleQuantity($data);
    }

    private function getRoomItemOrFail(string $roomId, string $itemId): RoomItem
    {
        $roomItem = RoomItem::where('room_id', $roomId)->where('item_id', $itemId)->first();

        if (!$roomItem) {
            throw new UnprocessableEntityHttpException('Item not found in room');
        }

        return $roomItem;
    }

    private function getItemOrFail(string $itemId): Item
    {
        $item = Item::find($itemId);

        if (!$item) {
            throw new UnprocessableEntityHttpException('Item not found');
        }

        return $item;
    }

    private function decreaseRoomItemQuantity(array $data): void
    {
        $roomItem = $this->getRoomItemOrFail($data['room_id'], $data['item_id']);

        if ($roomItem->quantity < $data['quantity']) {
            throw new UnprocessableEntityHttpException('Quantity is greater than available quantity');
        }

        $roomItem->update(['quantity' => $roomItem->quantity - $data['quantity']]);
    }

    private function decreaseItemIdleQuantity(array $data): void
    {
        $item = $this->getItemOrFail($data['item_id']);

        if ($item->idle_quantity < $data['quantity']) {
            throw new UnprocessableEntityHttpException('Quantity is greater than available quantity');
        }

        $item->update(['idle_quantity' => $item->idle_quantity - $data['quantity']]);
    }

    private function increaseRoomItemQuantity(array $data): void
    {
        $roomItem = RoomItem::firstOrNew([
            'room_id' => $data['room_id'],
            'item_id' => $data['item_id'],
        ]);

        $roomItem->quantity = ($roomItem->quantity ?? 0) + $data['quantity'];
        $roomItem->save();
    }

    private function increaseItemIdleQuantity(array $data): void
    {
        $item = $this->getItemOrFail($data['item_id']);
        $item->update(['idle_quantity' => $item->idle_quantity + $data['quantity']]);
    }
}
