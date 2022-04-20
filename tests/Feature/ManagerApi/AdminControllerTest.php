<?php

namespace Tests\Feature\ManagerApi;

use App\Common\BaseFacade;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    protected string $baseRoutePrefix;
    protected BaseFacade $facade;

    protected function getUrl(string $methodName, array $params = []): string
    {
        $methodKey = $this->baseRoutePrefix . '.' . $methodName;
        return route($methodKey, $params);
    }

    public function index(): void
    {
        $url = $this->getUrl('index');
        $this
            ->get($url)
            ->assertOk();
    }

    public function store(): void
    {
        $url = $this->getUrl('store');
        $this
            ->post($url)
            ->assertOk();
    }

    public function update(): void
    {
        $url = $this->getUrl('store');
        $this
            ->get($url)
            ->assertOk();
    }

    public function destroy(): void
    {
        $url = $this->getUrl('store');
        $this
            ->delete($url)
            ->assertOk();
    }

    public function restore(): void
    {
        $url = $this->getUrl('store');
        $this
            ->delete($url)
            ->assertOk();
    }
}