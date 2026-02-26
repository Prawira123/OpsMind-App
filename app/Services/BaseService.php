<?php

namespace App\Services;

abstract class BaseService
{
    protected function success($data = null, string $message = 'Success'): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];
    }

    protected function error(string $message = 'Error', $data = null): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data'    => $data,
        ];
    }
}