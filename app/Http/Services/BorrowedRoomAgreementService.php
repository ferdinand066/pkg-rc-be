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

        // $requiredEmail = [
        //     'pkg@parokipik.or.id'
        // ];

        // $users = User::whereIn('email', $requiredEmail)->pluck('id')->toArray();

        // $countRequiredUsers = BorrowedRoomAgreement::where([
        //     ['borrowed_room_id', $borrowedRoom->id], 
        //     ['agreement_status', 1]
        // ])->whereIn('created_by_user_id', $users)->count();

        // if ($countRequiredUsers !== count($requiredEmail)) {
        //     return "pending";
        // }

        $count = BorrowedRoomAgreement::where([['borrowed_room_id', $borrowedRoom->id], ['agreement_status', 1]])->count();
        // TODO: set total users to accept a request
        // $userCount = User::where('role', 2)->count();

        // if ($count == $userCount) return "accepted";
        if ($count >= 1) return "accepted";

        return "pending";
    }

}
