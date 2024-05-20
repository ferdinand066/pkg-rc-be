<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Returns data from the results of a successful request.
     *
     * @param int $code Response code 2xx
     * @param string $message Success message in general
     * @param array $result Returned data as the response
     *
     * @return JsonResponse
     */
    public function sendResponse(int $code = 200, string $message, array $result){
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message
        ];

        return response()->json($response, $code);
    }

    /**
     * Returns data from the results of a failed request.
     *
     * @param int $code Response code 4xx
     * @param string $error Error message in general
     * @param array $errorMessages List of error messages
     *
     * @return JsonResponse
     */
    public function sendError(int $code = 400, string $error, array $errorMessages = []){
        $response = [
            'success' => false,
            'message' => $error
        ];

        if(!empty($errorMessages)){
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    public function getSearchAndSort(){
        return [
            'search' => request()->search,
            'orderBy' => request()->order_by,
            'dataOrder' => request()->data_order
        ];
    }
}
