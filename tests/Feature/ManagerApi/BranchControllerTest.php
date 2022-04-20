<?php

namespace Tests\Feature\ManagerApi;

class BranchControllerTest extends AdminControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->baseRoutePrefix = 'admin.branch';
    }

    public function indexTest(): void
    {
        $url = $this->getUrl('index');
        $this
            ->get($url)
            ->assertOk();
    }

    public function storeTest(): void
    {
        $url = $this->getUrl('store');
        $this
            ->post($url)
            ->assertOk();
    }

    public function updateTest(): void
    {
        $url = $this->getUrl('store');
        $this
            ->get($url)
            ->assertOk();
    }
}