<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class ForgotPasswordController extends BaseController
{
    public function forgotPassword(Request $request){
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? $this->sendResponse(Response::HTTP_OK, 'Reset link sent to your email', [])
            : $this->sendError(Response::HTTP_BAD_REQUEST, 'Unable to send reset link');
    }

    public function resetPassword(Request $request){
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? $this->sendResponse(Response::HTTP_OK, 'Password has been reset successfully', [])
            : $this->sendError(Response::HTTP_BAD_REQUEST, 'Unable to reset password');
    }

    public function checkTokenValidity(Request $request, $token)
    {
        $email = $request->query('email');

        // Use Laravel's Password facade to check if the token is valid
        $response = Password::broker()->getRepository()->exists(
            User::where('email', $email)->first(),
            $token
        );

        if ($response) {
            // Token is valid, handle accordingly
            return $this->sendResponse(Response::HTTP_OK, 'Token is valid', ['res' => true]);
        } else {
            // Token is invalid or expired
            return $this->sendError(Response::HTTP_NOT_FOUND, 'Token is invalid or expired', ['res' => false]);
        }
    }
}
