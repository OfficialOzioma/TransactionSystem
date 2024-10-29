<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Transaction;
use App\Models\UserBalance;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function processTransaction(User $user, float $amount, string $type)
    {
        $reference = Str::uuid();
        
        try {
            return DB::transaction(function () use ($user, $amount, $type, $reference) {
                // Lock the balance record for update
                $userBalance = UserBalance::lockForUpdate()
                    ->firstOrCreate(
                        ['user_id' => $user->id],
                        ['balance' => 0, 'last_updated_at' => now()]
                    );
                
                if ($type === 'debit' && $userBalance->balance < $amount) {
                    throw new Exception('Insufficient funds');
                }
                
                // Create transaction record
                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'transaction_type' => $type,
                    'status' => 'pending',
                    'reference' => $reference
                ]);
                
                // Update user balance
                $newBalance = $type === 'credit' 
                    ? $userBalance->balance + $amount 
                    : $userBalance->balance - $amount;
                    
                $userBalance->update([
                    'balance' => $newBalance,
                    'last_updated_at' => Carbon::now()
                ]);
                
                // Mark transaction as completed
                $transaction->update(['status' => 'completed']);
                
                return $transaction;
            }, 5); // 5 retries for deadlock
        } catch (Exception $e) {
            Log::error('Transaction failed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            
            // Mark transaction as failed
            $filedTransaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'transaction_type' => $type,
                'status' => 'failed',
                'reference' => $reference
            ]);
            
            $filedTransaction['message'] = $e->getMessage();

            return $filedTransaction;

        }
    }

    public function getBalance(User $user)
    {
        return $user->balance->balance ?? 0;
    }
}
