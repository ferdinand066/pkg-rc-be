<?php

namespace App\Http\Controllers\Auth;

use App\Http\Actions\Auth\LoginAction;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/auth/login",
     *     operationId="authLogin",
     *     tags={"Auth"},
     *     summary="[Authentication] Login",
     *     description="[Authentication] Login to BIA Regina Caeli to get Bearer Token",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Credential at BIA Regina Caeli",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", example="ferdinandg066@gmail.com"),
     *             @OA\Property(property="password", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully login",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *               property="success",
     *               type="boolean",
     *             ),
     *             @OA\Property(
     *               property="message",
     *               type="string",
     *               example="Login Successfully!",
     *             ),
     *             @OA\Property(
     *               property="data",
     *               type="object",
     *               @OA\Property(property="token", type="string"),
     *               @OA\Property(property="expires_in", type="datetime"),
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
    public function __invoke(LoginRequest $request, LoginAction $action): JsonResponse
    {
        $validated = $request->validated();
        try {
            $user = $action->handle($validated);
            $result = $action->createToken($user);

            return $this->sendResponse(Response::HTTP_OK, 'Login Successfully!', $result);
        } catch (Exception $e) {
            return $this->sendError(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }
}
