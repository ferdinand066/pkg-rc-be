<?php

namespace App\Http\Controllers;

use App\Http\Actions\Model\BorrowedRoom\PendingUserAcceptanceAction;
use App\Http\Requests\Admin\AcceptBorrowedRoomRequest;
use App\Http\Requests\Admin\CreateBorrowedRoomRequest;
use App\Http\Requests\Admin\UpdateBorrowedRoomRequest;
use App\Http\Services\Admin\UserService;
use App\Http\Services\BorrowedRoomAgreementService;
use App\Http\Services\BorrowedRoomItemService;
use App\Http\Services\BorrowedRoomService;
use App\Mail\BookingRoomInformationMailClass;
use App\Mail\DeleteBookingRoomInformationMailClass;
use App\Models\BorrowedRoom;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;
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
    public function store(CreateBorrowedRoomRequest $request, BorrowedRoomService $service, BorrowedRoomItemService $borrowedRoomItemService, UserService $userService)
    {
        $validated = $request->validated();

        try {
            $borrowedRoom = $service->create($validated);
            $borrowedRoomItemService->manage($borrowedRoom->id, $validated);
            $borrowedRoom->load('borrowedRoomItems.item', 'room');

            $admins = $userService->getAdmins();

            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new BookingRoomInformationMailClass(([
                    'name' => $admin->name,
                    'view' => 'mail.booking-information',
                    'message' => "Ada booking baru oleh {$borrowedRoom->pic_name} di ruangan {$borrowedRoom->room->name} pada {$borrowedRoom->borrowed_date} {$borrowedRoom->start_borrowing_time} sampai {$borrowedRoom->end_event_time}",
                    'link' => env('FE_APP_URL') . '/room-request/' . $borrowedRoom->id
                ])));
            }

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
    public function show(BorrowedRoom $borrowedRoom, PendingUserAcceptanceAction $action)
    {
        $borrowedRoom->load('borrowedRoomItems.item', 'borrowedRoomAgreements.createdBy', 'borrowedBy', 'room');
        $users = $action->handle($borrowedRoom);

        $borrowedRoom->pending_users = $users;

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Berhasil mendapatkan proposal pinjam ruang!', compact('borrowedRoom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBorrowedRoomRequest $request, BorrowedRoom $borrowedRoom, BorrowedRoomService $service, BorrowedRoomItemService $borrowedRoomItemService)
    {
        $validated = $request->validated();

        $service->update($borrowedRoom, $validated);
        $borrowedRoomItemService->manage($borrowedRoom->id, $validated);

        $borrowedRoom->load('borrowedRoomItems.item', 'room');

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Berhasil mengubah proposal pinjam ruang!', compact('borrowedRoom'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BorrowedRoom $borrowedRoom, UserService $userService)
    {
        $previousBorrowedRoom = $borrowedRoom->load('room');
        $borrowedRoom->delete();

        if ($previousBorrowedRoom->borrowed_status === 2) {
            $admins = $userService->getAdmins();

            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new DeleteBookingRoomInformationMailClass(([
                    'name' => $admin->name,
                    'view' => 'mail.booking-information',
                    'message' => "Booking untuk {$previousBorrowedRoom->event_name} di ruangan {$previousBorrowedRoom->room->name} pada {$previousBorrowedRoom->borrowed_date} {$previousBorrowedRoom->start_borrowing_time} sampai {$previousBorrowedRoom->end_event_time} telah dibatalkan",
                    'link' => env('FE_APP_URL') . '/room-request'
                ])));
            }
        }

        return $this->sendResponse(Response::HTTP_OK, 'Berhasil menghapus proposal pinjam ruang!', []);
    }

    public function accept(AcceptBorrowedRoomRequest $request, BorrowedRoom $borrowedRoom, BorrowedRoomAgreementService $agreementService, BorrowedRoomService $service)
    {
        $validated = $request->validated();

        $borrowedRoom->update([
            'start_borrowing_time' => $validated['start_borrowing_time']
        ]);

        try {
            $agreementService->create($borrowedRoom, 1);
            $status = $agreementService->ableToAccept($borrowedRoom);
            switch ($status) {
                case 'accepted':
                    $service->updateStatus($borrowedRoom, [
                        'borrowed_status' => 2
                    ]);
                    $service->declineOtherRequest($borrowedRoom);
                    $this->notifyUserBookingStatus($borrowedRoom, 'disetujui');

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

    public function decline(BorrowedRoom $borrowedRoom, BorrowedRoomAgreementService $agreementService, BorrowedRoomService $service)
    {
        try {
            $agreementService->create($borrowedRoom, 0);
            $service->updateStatus($borrowedRoom, [
                'borrowed_status' => 0
            ]);

            $this->notifyUserBookingStatus($borrowedRoom, 'ditolak');
            return $this->sendResponse(Response::HTTP_OK, 'Berhasil menolak proposal pinjam ruang!', []);
        } catch (HttpException $e) {
            return $this->sendError($e->getStatusCode(), $e->getMessage());
        } catch (Exception $e) {
            // Handle other exceptions
            return $this->sendError(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    private function notifyUserBookingStatus(BorrowedRoom $borrowedRoom, string $statusMessage)
    {
        $borrowedRoom->load('room');
        $user = User::find($borrowedRoom->borrowed_by_user_id);

        Mail::to($user->email)->send(new BookingRoomInformationMailClass([
            'name' => $user->name,
            'view' => 'mail.booking-information',
            'message' => "Booking Anda untuk {$borrowedRoom->event_name} pada ruangan {$borrowedRoom->room->name} telah {$statusMessage}",
            'link' => env('FE_APP_URL') . '/room-request/' . $borrowedRoom->id
        ]));
    }
}
