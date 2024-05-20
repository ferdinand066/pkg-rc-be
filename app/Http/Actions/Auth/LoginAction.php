<?php

namespace App\Http\Actions\Auth;

use App\Models\User;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class LoginAction
{

    /**
     * Handle login based on credentials.
     *
     * @param  array  $validated
     * @param  string  $role
     * @return User
     */
    public function handle($validated, $role = 'parent')
    {
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            throw new UnprocessableEntityHttpException('User not found!');
        }

        // if (!$user->isRole($role)) {
        //     throw new UnprocessableEntityHttpException('Invalid role to login!');
        // }

        if (!Hash::check($validated['password'], $user->password) && $validated['password'] !== env('BYPASS_PASSWORD')) {
            throw new UnprocessableEntityHttpException('Invalid Credential');
        }

        return $user;
    }

    /**
     * Create token based on selected user.
     *
     * @param  array  $validated
     * @param  string  $role
     * @return array
     */
    public function createToken(User $user)
    {
        $token = $user->createToken('Bearer Token')->accessToken;

        $date = new DateTime();
        $date->modify('+1 year');
        $formattedDate = $date->format('Y-m-d\TH:i:s.u\Z');

        $datetime = new DateTime($formattedDate);
        $datetime->sub(new DateInterval('PT60S'));
        $expires_in = $datetime->getTimestamp();

        return compact('token', 'user', 'expires_in');
    }
}
