<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Throwable;
   
class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof UnauthorizedException) {
            return response()->json([
                'status' => 'error',
                'message' => 'User does not have the right permissions.'
            ], 403);
        }

        return parent::render($request, $exception);
    }
}
    






