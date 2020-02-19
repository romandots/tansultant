<?php


namespace App\Http\Controllers\Auth;


use Illuminate\Http\JsonResponse;

class WelcomeController
{
    public function welcome(): JsonResponse
    {
        return \json_response([
            'version' => '1.0'
        ]);
    }
}