<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class InsufficientBalanceException extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => 'Payment amount exceeds the remaining balance of the invoice.',
        ], 422);
    }
}
