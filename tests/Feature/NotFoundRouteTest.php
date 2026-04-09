<?php

namespace Tests\Feature;

use App\Messages\Messages;
use Tests\TestCase;

class NotFoundRouteTest extends TestCase
{
    public function test_unknown_api_route_returns_404_json(): void
    {
        $this->getJson('/api/v1/rota-que-nao-existe')
            ->assertNotFound()
            ->assertJsonPath('message', Messages::NOT_FOUND)
        ;
    }
}
