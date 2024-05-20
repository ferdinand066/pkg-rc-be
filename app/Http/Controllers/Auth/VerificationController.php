<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends BaseController
{
    public function verify($id, Request $request){
        if (!$request->hasValidSignature()){
            return $this->sendError(Response::HTTP_UNAUTHORIZED, 'Invalid/Expired url provided.');
        }

        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return $this->sendResponse(Response::HTTP_OK, 'Successfully verified', []);
    }

    public function resend(){
        if (Auth::user()->hasVerifiedEmail()){
            return $this->sendError(Response::HTTP_BAD_REQUEST, 'Email already verified.');
        }

        Auth::user()->sendEmailVerificationNotification();

        return $this->sendResponse(200, "Email verification link sent on your email!", []);
    }
}
