<?php

namespace App\Http\Actions\Auth;

use App\Models\User;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Google_Client;

class SocialMediaLoginAction {

    /**
     * Handle login based on credentials.
     *
     * @param  array  $validated
     * @param  string  $role
     * @return User
     */
    public function handle($validated){
        switch ($validated['provider']) {
            case 'google':
                return $this->loginWithGoogle($validated['id']);
                break;
            default:
                throw new UnprocessableEntityHttpException('Provider is not supported');
                break;
        }
    }

    private function loginWithGoogle(string $tokenId, string $role = 'parent'){
        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($tokenId);

        $googleId = $payload["sub"];
        $userEmail = $payload["email"];

        $user = User::where('email', $userEmail)->first();

        if (!$user){
            throw new UnprocessableEntityHttpException('User not found, please register first!');
        }

        if (!$user->isRole($role)){
            throw new UnprocessableEntityHttpException('Invalid role to login!');
        }

        if (!$user->google_id){
            $user->update([
                'google_id' => $googleId
            ]);
        }

        return $user;
    }
}
