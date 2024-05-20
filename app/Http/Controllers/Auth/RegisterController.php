<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/auth/register",
     *     operationId="authRegister",
     *     tags={"Auth"},
     *     summary="[Authentication] Register",
     *     description="[Authentication] Register to BIA Regina Caeli",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Credential at BIA Regina Caeli",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="New Account"),
     *             @OA\Property(property="email", type="string", example="new_account@gmail.com"),
     *             @OA\Property(property="password", type="string", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", example="password"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully register",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *               property="success",
     *               type="boolean",
     *             ),
     *             @OA\Property(
     *               property="message",
     *               type="string",
     *               example="Register Successfully!",
     *             ),
     *             @OA\Property(
     *               property="data",
     *               type="object",
     *               @OA\Property(property="token", type="string"),
     *               @OA\Property(property="user", ref="#/components/schemas/User"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *               property="success",
     *               type="boolean",
     *               example=false
     *             ),
     *             @OA\Property(property="message", type="string", example="Invalid Credential!"),
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
    public function __invoke(RegisterRequest $request): JsonResponse
    { 
        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = $validated['role'] ?? 1;
        $user = User::create($validated);

        $accessToken = $user->createToken('authToken')->accessToken;

        $user->sendEmailVerificationNotification();

        return $this->sendResponse(Response::HTTP_CREATED, 'Successfully register',
            ['token' => $accessToken, 'user' => $user]);
    }
}
