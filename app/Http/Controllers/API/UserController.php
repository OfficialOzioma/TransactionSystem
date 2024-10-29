<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function getBalance()
    {
        $balance = $this->transactionService->getBalance(Auth::user());
        
        return response()->json([
            'balance' => $balance
        ]);
    }
}
