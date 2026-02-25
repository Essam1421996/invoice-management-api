<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ContractNotActiveException extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => 'Cannot create invoice for a contract that is not active.',
        ], 422);
    }
}
