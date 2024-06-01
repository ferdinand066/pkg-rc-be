<?php

namespace App\Http\Actions\Model\BorrowedRoom;

use App\Models\BorrowedRoom;
use App\Models\BorrowedRoomAgreement;
use App\Models\User;

class PendingUserAcceptanceAction {

    /**
     * Handle fetch list of pending acceptance users
     *
     * @param  BorrowedRoom  $borrowedRoom
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function handle(BorrowedRoom $borrowedRoom){ 
        if ($borrowedRoom->borrowed_status !== 1) return null;
        $borrowedRoomAgreementUserIds = BorrowedRoomAgreement::where('borrowed_room_id', $borrowedRoom->id)->pluck('created_by_user_id')->toArray() ?? [];

        return User::where('role', 2)->whereNotIn('id', $borrowedRoomAgreementUserIds)->get();
    }
}
