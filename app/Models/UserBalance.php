<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'last_updated_at'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'last_updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
