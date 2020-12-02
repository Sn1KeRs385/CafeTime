<?php

namespace App\Exceptions;

use App\Helpers\JSON;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        $code = null;
        $response = null;

        if($e instanceof CustomException){
            $code = $e->getCode();
            $error = [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'description' => $e->getDescription(),
            ];
            $response = JSON::getJson([], [$error]);
        }

        if($e instanceof AuthenticationException){
            $code = 403;
            $error = [
                'code' => 403,
                'message' => 'AUTHORIZATION_EXCEPTION',
                'description' => __('auth.errors.AUTHORIZATION_EXCEPTION'),
            ];
            $response = JSON::getJson([], [$error]);
        }

        if($e instanceof \TypeError){
            $code = 500;
            $error = [
                'code' => 500,
                'message' => 'REAL_500',
                'description' => config('app.debug')
                    ? $e->getTrace()
                    : $e->getMessage(),
            ];
            $response = JSON::getJson([], [$error]);
        }

        if(!$response){
            $code = $e->getCode();
            $response = JSON::getJson([], [
                [
                    'code' => $code,
                    'message' => $e->getMessage(),
                    'description' => $e->getMessage(),
                ]
            ]);
        }

        return Response::json($response, $code);
    }
}
