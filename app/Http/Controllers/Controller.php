<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * @param mixed       $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function response($data = null, int $code = 200, $headers = []) : JsonResponse
    {
        if (!empty($data) && isset($data->resource) && $data->resource instanceof LengthAwarePaginator) {
            $headers['Total'] = $data->resource->total();
        }
        
        return JsonResponse::create([
            'message' => '',
            'data'    => $data,
        ], $code, $headers);
    }

    /**
     * @param string   $message
     * @param int|null $statusCode
     * @return JsonResponse
     */
    protected function badResponse(string $message, int $statusCode = null) : JsonResponse
    {
        return JsonResponse::create([
            'message' => $message ?? 'Something went wrong :(',
        ], $statusCode ?? Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
