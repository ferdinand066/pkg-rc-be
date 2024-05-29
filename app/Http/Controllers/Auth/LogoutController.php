<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;
use Symfony\Component\HttpFoundation\Response;

class LogoutController extends BaseController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        Token::where('user_id', Auth::user()->id)->update(['revoked' => true]);
        return $this->sendResponse(Response::HTTP_OK, 'Berhasil keluar!', []);
    }
}
