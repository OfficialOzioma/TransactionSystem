<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use App\Http\Requests\TransactionRequest;
use Exception;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function store(TransactionRequest $request)
    {
        try {
            $transaction = $this->transactionService->processTransaction(
                $request->user(),
                $request->amount,
                $request->transaction_type
            );
    
        
            return response()->json([
                'status' => $transaction->status,
                'reference' => $transaction->reference,
                'message' => $transaction->status === 'completed' 
                    ? 'Transaction processed successfully' 
                    : 'Transaction failed, '.$transaction->message
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ]);
        }
       
    }


}
