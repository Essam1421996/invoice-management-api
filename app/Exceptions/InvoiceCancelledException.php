<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class InvoiceCancelledException extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => 'Cannot record payment on a cancelled invoice.',
        ], 422);
    }
}
