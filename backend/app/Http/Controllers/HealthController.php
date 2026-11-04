<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class HealthController extends Controller
{
    /**
     * Perform deep health check on database and cache services.
     */
    public function check(): JsonResponse
    {
        $status = 'healthy';
        $httpCode = 200;
        $checks = [
            'database' => ['status' => 'unknown'],
            'cache' => ['status' => 'unknown'],
        ];

        // 1. Uji Nyata Koneksi Basis Data PostgreSQL
        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            $checks['database'] = [
                'status' => 'up',
                'message' => 'PostgreSQL connection healthy.',
            ];
        } catch (Throwable $e) {
            $status = 'unhealthy';
            $httpCode = 503;
            $checks['database'] = [
                'status' => 'down',
                'message' => 'Unable to connect to database.',
                'error' => $e->getMessage(),
            ];

            Log::error('Healthcheck: Layanan basis data tidak dapat dihubungi.', [
                'service' => 'database',
                'driver' => config('database.default'),
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ]);
        }

        // 2. Uji Nyata Driver Cache (Read / Write / Delete probe)
        try {
            $testKey = 'healthcheck_probe_' . time();
            Cache::put($testKey, 'ok', 10);
            $value = Cache::get($testKey);
            Cache::forget($testKey);

            if ($value === 'ok') {
                $checks['cache'] = [
                    'status' => 'up',
                    'driver' => config('cache.default'),
                    'message' => 'Cache read/write test passed.',
                ];
            } else {
                throw new \Exception('Cache value mismatch.');
            }
        } catch (Throwable $e) {
            $status = 'unhealthy';
            $httpCode = 503;
            $checks['cache'] = [
                'status' => 'down',
                'driver' => config('cache.default'),
                'message' => 'Cache driver unavailable.',
                'error' => $e->getMessage(),
            ];

            Log::error('Healthcheck: Layanan cache bermasalah.', [
                'service' => 'cache',
                'driver' => config('cache.default'),
                'error_message' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ]);
        }

        return response()->json([
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'system' => [
                'php_version' => PHP_VERSION,
                'app_env' => config('app.env'),
            ],
            'services' => $checks,
        ], $httpCode);
    }
}
