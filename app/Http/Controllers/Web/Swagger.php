<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

use function app_path;

class Swagger extends Controller
{

    public function yaml(): \Illuminate\Http\Response
    {
        $openapi = $this->getOpenApiInstance();
        return new \Illuminate\Http\Response(
            $openapi?->toYaml(),
            $openapi ? 200 : 400,
            ['Content-Type' => 'text/x-yaml']
        );
    }

    public function json(): \Illuminate\Http\Response
    {
        $openapi = $this->getOpenApiInstance();
        return new \Illuminate\Http\Response(
            $openapi?->toJson(),
            $openapi ? 200 : 400,
            ['Content-Type' => 'application/json']
        );
    }

    protected function getOpenApiInstance(): ?OA\OpenApi
    {
        return Generator::scan([app_path()]);
    }
}