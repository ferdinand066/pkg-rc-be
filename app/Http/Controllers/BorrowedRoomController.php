<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\CreateBorrowedRoomRequest;
use App\Http\Requests\Admin\UpdateBorrowedRoomRequest;
use App\Http\Services\BorrowedRoomAgreementService;
use App\Http\Services\BorrowedRoomItemService;
use App\Http\Services\BorrowedRoomService;
use App\Models\BorrowedRoom;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BorrowedRoomController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(BorrowedRoomService $service)
    {
        $data = $this->getSearchAndSort();
        $borrowedRooms = $service->index($data);

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Berhasil mendapatkan proposal pinjam ruang!', compact('borrowedRooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateBorrowedRoomRequest $request, BorrowedRoomService $service, BorrowedRoomItemService $borrowedRoomItemService)
    {
        $validated = $request->validated();

        try {
            $borrowedRoom = $service->create($validated);

            $borrowedRoomItemService->manage($borrowedRoom->id, $validated);

            $borrowedRoom->load('borrowedRoomItems.item');

            return $this->sendResponse(Response::HTTP_CREATED, 'Berhasil membuat proposal pinjam ruang!', compact('borrowedRoom'));
        } catch (HttpException $e) {
            return $this->sendError($e->getStatusCode(), $e->getMessage());
        } catch (Exception $e) {
            // Handle other exceptions
            return $this->sendError(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BorrowedRoom $borrowedRoom)
    {
        $borrowedRoom->load('borrowedRoomItems.item', 'borrowedRoomAgreements.createdBy', 'borrowedBy', 'room');

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Berhasil mendapatkan proposal pinjam ruang!', compact('borrowedRoom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBorrowedRoomRequest $request, BorrowedRoom $borrowedRoom, BorrowedRoomService $service, BorrowedRoomItemService $borrowedRoomItemService)
    {
        $validated = $request->validated();

        $service->update($borrowedRoom, $validated);

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Berhasil mengubah proposal pinjam ruang!', compact('borrowedRoom'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BorrowedRoom $borrowedRoom)
    {
        $borrowedRoom->delete();

        return $this->sendResponse(Response::HTTP_OK, 'Berhasil menghapus proposal pinjam ruang!', []);
    }

    public function accept(BorrowedRoom $borrowedRoom, BorrowedRoomAgreementService $agreementService, BorrowedRoomService $service){
        try {
            $agreementService->create($borrowedRoom, 1);
            $status = $agreementService->ableToAccept($borrowedRoom);
            switch ($status) {
                case 'accepted':
                    $service->updateStatus($borrowedRoom, [
                        'borrowed_status' => 2
                    ]);
                    $service->declineOtherRequest($borrowedRoom);

                    break;
                default:
                    break;
            }
            return $this->sendResponse(Response::HTTP_OK, 'Berhasil menyetujui proposal pinjam ruang!', []);
        } catch (HttpException $e) {
            return $this->sendError($e->getStatusCode(), $e->getMessage());
        } catch (Exception $e) {
            // Handle other exceptions
            return $this->sendError(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function decline(BorrowedRoom $borrowedRoom, BorrowedRoomAgreementService $agreementService, BorrowedRoomService $service){
        try {
            $agreementService->create($borrowedRoom, 0);
            $service->updateStatus($borrowedRoom, [
                'borrowed_status' => 0
            ]);
            return $this->sendResponse(Response::HTTP_OK, 'Berhasil menolak proposal pinjam ruang!', []);
        } catch (HttpException $e) {
            return $this->sendError($e->getStatusCode(), $e->getMessage());
        } catch (Exception $e) {
            // Handle other exceptions
            return $this->sendError(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
