<?php

namespace App\Models;

use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory;

    protected $fillable = ['name', 'account_number', 'status'];

    protected $casts = [
        'status' => AccountStatus::class,
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'account_id', 'id');
    }
}
