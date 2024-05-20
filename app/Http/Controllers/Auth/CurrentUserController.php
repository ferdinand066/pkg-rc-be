<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class CurrentUserController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/auth/me",
     *     operationId="authMe",
     *     tags={"Auth"},
     *     summary="[Authentication] Current User",
     *     description="[Authentication] Get current user data",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successfully get current user data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *               property="success",
     *               type="boolean",
     *             ),
     *             @OA\Property(
     *               property="message",
     *               type="string",
     *               example="Successfully get current user",
     *             ),
     *             @OA\Property(
     *               property="data",
     *               type="object",
     *               @OA\Property(property="user", ref="#/components/schemas/User"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *               property="success",
     *               type="boolean",
     *               example=false
     *             ),
     *             @OA\Property(property="message", type="string", example="Internal Server Error"),
     *         )
     *     )
     * )
     */
    public function __invoke(Request $request)
    {
        return $this->sendResponse(Response::HTTP_OK, 'Successfully get current user', ['user' => Auth::user()]);
    }
}
