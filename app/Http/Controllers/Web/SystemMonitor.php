<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SystemMonitor extends Controller
{

    public function dashboard(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('dashboard');
    }

    public function redisHealthCheck(): \Illuminate\Http\JsonResponse
    {
        $redis = app('redis');
        try {
            $redis->ping();
            return response()->json([
                'status' => 'ok',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function databaseHealthCheck(): \Illuminate\Http\JsonResponse
    {
        $db = app('db');
        try {
            $db->select('SELECT 1');
            return response()->json([
                'status' => 'ok'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function servicesHealthCheck(): View
    {
        return \view('services');
    }
}