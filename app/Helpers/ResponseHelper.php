<?php

namespace App\Helpers;

trait ResponseHelper
{
    public function success($message, $data = null, $isPaginated = false)
    {
        $response = [
            'success' => (bool)true,
            'message' => $message,
            'data' => $data
        ];

        if ($isPaginated) {
            $response['data'] = $data->items();
            $response['meta'] = [
                'currentPage' => $data->currentPage(),
                'lastPage' => $data->lastPage(),
                'perPage' => $data->perPage(),
                'total' => $data->total(),
            ];
        }

        return response()->json($response, 200);
    }

    public function error($message, $data = null)
    {
        $response = [
            'success' => (bool)false,
            'message' => $message,
            'data' => $data
        ];

        return response()->json($response, 400);
    }

    public function exception($message)
    {
        # check if local exception mode is disabled or not
        if (!config('custom.local_exception_mode')) {
            $message = 'Something went wrong';
        }

        $response = [
            'success' => (bool)false,
            'message' => $message,
        ];

        return response()->json($response, 400);
    }
}
