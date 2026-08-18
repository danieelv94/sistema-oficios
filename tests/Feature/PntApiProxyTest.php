<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class PntApiProxyTest extends TestCase
{
    /**
     * Test that guest users cannot access the proxy route.
     */
    public function test_guest_cannot_access_proxy_route()
    {
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckAvisosPendientes::class,
        ]);

        $response = $this->get(route('pnt.external-licitaciones'), [
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test that authenticated users can fetch external licitaciones and receive proxy data.
     */
    public function test_authenticated_user_can_access_proxy_route()
    {
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckAvisosPendientes::class,
        ]);

        // Mock the external API response
        Http::fake([
            'licitaciones.ceaa.app/*' => Http::response([
                'data' => [
                    [
                        'id' => 84,
                        'numero_expediente' => 'CEAA-OP-913023990-E88-2026',
                        'titulo' => 'Rehabilitación de Planta de Tratamiento'
                    ]
                ],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 4
                ]
            ], 200)
        ]);

        // Create a mock user
        $user = new User([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'admin'
        ]);

        // Authenticate the user and make the request
        $response = $this->actingAs($user)
            ->get(route('pnt.external-licitaciones') . '?page=1');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'numero_expediente' => 'CEAA-OP-913023990-E88-2026',
            'titulo' => 'Rehabilitación de Planta de Tratamiento'
        ]);
    }
}
