<?php

namespace Tests\Unit;

use Exception;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserBalance;
use Illuminate\Support\Facades\DB;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $transactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionService = new TransactionService();
    }

    public function testProcessDeposit()
    {
        $user = User::factory()->create();
        $userBalance = UserBalance::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000,
            'last_updated_at' => Carbon::now()
        ]);

        $this->transactionService->processTransaction($user, 500, 'credit');


        // Assert: Check if balance updated correctly
        $this->assertEquals(1500.00, UserBalance::find($userBalance->id)->balance);
        $this->assertDatabaseHas('user_balances', [
            'user_id' => $userBalance->user_id,
            'balance' => 1500
        ]);
    }

    public function testProcessWithdrawal()
    {
       
        $user = User::factory()->create();

        $userBalance = UserBalance::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000,
            'last_updated_at' => Carbon::now()
        ]);

        // Process a withdrawal
        $this->transactionService->processTransaction($user, 500, 'debit');

        // Assert: Check if balance updated correctly
        $this->assertEquals(500, UserBalance::find($userBalance->id)->balance);
        $this->assertDatabaseHas('user_balances', [
            'user_id' => $userBalance->user_id,
            'balance' => 500
        ]);
    }

    public function testInsufficientBalanceForWithdrawal()
    {
       
        $user = User::factory()->create();
        // Create a user balance with a lower balance than withdrawal amount
        UserBalance::factory()->create([
            'user_id' => $user->id,
            'balance' => 400,
            'last_updated_at' => Carbon::now()
        ]);

        // Act: Attempt to process a withdrawal greater than the current balance
      $transaction =  $this->transactionService->processTransaction($user, 500, 'debit');

        // Assert: Check if transaction failed  
        $this->assertEquals('failed', $transaction->status);
        $this->assertEquals('Insufficient funds', $transaction->message);
        $this->assertDatabaseHas('user_balances', [
            'user_id' => $user->id,
            'balance' => 400
        ]);

    }
}
