<?php

namespace App\Http\Traits;


trait ApiResponseTrait
{
    protected function apiResponse(bool $success , string $message , $results = [], $errors = [], int $status = 200)
    {
        $responseStructure = [
            'success' => $success,
            'message' => $message ?? null,
            'result' => $results ?? null,
        ];

        if ($success === false) {
            $responseStructure['errors'] = $errors;
        }

        return response()->json($responseStructure);
    }
}
