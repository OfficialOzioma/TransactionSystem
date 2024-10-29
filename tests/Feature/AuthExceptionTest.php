<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthExceptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_json_response_for_unauthenticated_api_access()
    {
        $response = $this->getJson('/api/v1/balance');

        $response->assertStatus(401)
            ->assertJson([
                "message" => "Unauthenticated."
            ]);
    }

    public function test_returns_json_response_for_invalid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token'
        ])->getJson('/api/v1/balance');

        $response->assertStatus(401)
            ->assertJson([
                "message" => "Unauthenticated."
            ]);
    }

    public function test_returns_json_response_for_expired_token()
    {
        // Create a user and token
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Delete the token to simulate expiration
        $user->tokens()->delete();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/v1/balance');

        $response->assertStatus(401)
            ->assertJson([
                "message" => "Unauthenticated."
            ]);
    }

    public function test_successful_authenticated_request()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/balance');

        $response->assertStatus(200);
    }
}