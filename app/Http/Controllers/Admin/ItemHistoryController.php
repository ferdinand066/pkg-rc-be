<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManageItemHistoryRequest;
use App\Http\Services\Admin\ItemHistoryService;
use Symfony\Component\HttpFoundation\Response;

class ItemHistoryController extends BaseController
{
    public function index(ItemHistoryService $service)
    {
        $data = $this->getSearchAndSort();
        $itemHistories = $service->index($data);

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Successfully get item histories', compact('itemHistories'));
    }

    public function store(ManageItemHistoryRequest $request, ItemHistoryService $service)
    {
        $validated = $request->validated();
        $itemHistory = $service->create($validated);

        return $this->sendResponse(Response::HTTP_CREATED, 'Item history created successfully', compact('itemHistory'));
    }
}
