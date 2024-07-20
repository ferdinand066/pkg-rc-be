<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RemoveIdFields
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Ensure the response is a JSON response
        if ($response->headers->get('Content-Type') === 'application/json') {
            $data = json_decode($response->getContent(), true);
            error_log($response->getContent());

            // Recursively remove _id fields
            $data = $this->removeIdFields($data);

            $response->setContent(json_encode($data));
        }

        return $response;
    }

    /**
     * Recursively remove fields ending with _id from the array.
     *
     * @param  array  $data
     * @return array
     */
    private function removeIdFields(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->removeIdFields($value);
            }
            if (strpos($key, '_id') !== false || $key === 'id') {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
