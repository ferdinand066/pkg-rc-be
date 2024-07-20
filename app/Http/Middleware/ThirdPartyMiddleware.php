<?php

namespace App\Http\Middleware;

use App\Models\ThirdPartyToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ThirdPartyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $customHeader = $request->header('X-ReginaCaeli-Service', '');
        if (!$customHeader){
            return response()->json(['error' => 'Header Required!'], 401);
        }

        $jsonString = base64_decode($customHeader);

        // Parse the JSON string into an object
        $encryptedData = json_decode($jsonString, true);

        $iv = base64_decode($encryptedData['iv']);
        $content = base64_decode($encryptedData['content']);
        $hash = $encryptedData['hash'];

        $combined = base64_encode($iv) . base64_encode($content);

        // Verify the hash
        $computedHash = hash('sha256', $combined);
        if ($computedHash !== $hash) {
            return response()->json('Hash mismatch! Data may have been tampered with.');
        }

        $cipher = "aes-256-cbc";
        $decrypted = openssl_decrypt($content, $cipher, hash('sha256', env('THIRD_PARTY_KEY'), true), OPENSSL_RAW_DATA, $iv);

        $decryptedData = json_decode($decrypted, true);

        if (!isset($decryptedData['timestamp'])){
            return response()->json(['error' => 'There are no timestamp'], 401);
        }

        if (!isset($decryptedData['value'])){
            return response()->json(['error' => 'There are no value'], 401);
        }

        $currentTimestamp = time();

        // Check if the timestamp is within the acceptable range (5 minutes)
        $timestamp = $decryptedData['timestamp'];
        // if (($currentTimestamp - $timestamp) > 300) { // 300 seconds = 5 minutes
        //     return response()->json(['error' => 'Invalid encryption: Data is older than 5 minutes.'], 401);
        // }

        $userId = ThirdPartyToken::find($decryptedData['value']);
        if (!$userId){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Auth::loginUsingId($userId->user_id);

        return $next($request);
    }
}
