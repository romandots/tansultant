<?php


namespace App\Http\Controllers\Auth;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WelcomeController
{
    public function welcome(): JsonResponse
    {
        $version = config('version.full_version');
        Log::debug('API server is running ok');
        Log::channel('stderr')->debug('Checking stderr log channel');
        Log::channel('gelf')->debug('Checking gelf log channel', ['status_code' => 200]);
        return \json_response([
            'version' => $version,
        ]);
    }
}