<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // $this->user = User::factory()->create([
        //     'balance' => 1000
        // ]);

        $this->user = User::factory()->withBalance(1000)->create();

        // Create token and authenticate
        // $token = $this->user->createToken('test-token')->plainTextToken;
        // $this->withHeader('Authorization', 'Bearer ' . $token);

        // Authenticate user for testing
        Sanctum::actingAs(
            $this->user,
            ['*'] // Grant all abilities to the token
        );
    }

    public function test_can_create_credit_transaction()
    {
        $response = $this->postJson('/api/transaction', [
            'amount' => 100,
            'transaction_type' => 'credit'
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'completed']);

        $this->assertEquals(1100, $this->user->balance->fresh()->balance);
    }

    public function test_cannot_debit_more_than_balance()
    {
        $response = $this->postJson('/api/transaction', [
            'amount' => 2000,
            'transaction_type' => 'debit'
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'failed']);

        $this->assertEquals(1000, $this->user->balance->fresh()->balance);
    }

    public function test_can_get_balance()
    {
        $response = $this->getJson('/api/balance');

        $response->assertStatus(200)
            ->assertJson(['balance' => 1000]);
    }

    public function test_concurrent_transactions()
    {
        // Simulate 10 concurrent credit transactions of 100 each
        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/transaction', [
                'amount' => 100,
                'transaction_type' => 'credit'
            ]);
        }

        // Final balance should be initial (1000) + (10 * 100)
        $this->assertEquals(2000, $this->user->balance->fresh()->balance);
    }

    public function test_unauthenticated_access()
    {
        // Create a new instance without authentication
        $this->app->get('auth')->forgetGuards();

        $response = $this->getJson('/api/balance');
        $response->assertStatus(401);
    }

}
