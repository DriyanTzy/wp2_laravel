<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {

            // Kalau request ke /api, selalu balik JSON — bukan HTML
            if ($request->is('api/*')) {

                // 404 — route/data tidak ditemukan
                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'message' => 'Data tidak ditemukan.',
                    ], 404);
                }

                // 401 — belum login / token invalid
                if ($e instanceof AuthenticationException) {
                    return response()->json([
                        'message' => 'Kamu harus login untuk mengakses ini.',
                    ], 401);
                }

                // 422 — validasi gagal
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'message' => 'Data yang dikirim tidak valid.',
                        'errors'  => $e->errors(),
                    ], 422);
                }

                // 500 — error tak terduga
                return response()->json([
                    'message' => 'Terjadi kesalahan di server.',
                    'error'   => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }
        });
    }
}