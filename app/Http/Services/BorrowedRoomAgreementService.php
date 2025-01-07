<?php

namespace App\Http\Services;

use App\Models\BorrowedRoom;
use App\Models\BorrowedRoomAgreement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class BorrowedRoomAgreementService
{
    public function create(BorrowedRoom $borrowedRoom, int $status)
    {
        $exists = BorrowedRoomAgreement::where([['borrowed_room_id', $borrowedRoom->id], ['created_by_user_id', Auth::user()->id]])->exists();
        if ($exists) {
            throw new UnprocessableEntityHttpException('You have create agreements before');
        }

        return BorrowedRoomAgreement::create(
            [
                'borrowed_room_id' => $borrowedRoom->id,
                'agreement_status' => $status,
                'created_by_user_id' => Auth::user()->id,
            ]
        );
    }

    public function ableToAccept(BorrowedRoom $borrowedRoom): string {
        $declineExists = BorrowedRoomAgreement::where([['borrowed_room_id', $borrowedRoom->id], ['agreement_status', 0]])->exists();
        if ($declineExists){
            return "declined";
        }

        $count = BorrowedRoomAgreement::where([['borrowed_room_id', $borrowedRoom->id], ['agreement_status', 1]])->count();
        // TODO: set total users to accept a request
        $userCount = User::where('role', 2)->count();

        // if ($count == $userCount) return "accepted";
        if ($count === 2) return "accepted";

        return "pending";
    }

}
