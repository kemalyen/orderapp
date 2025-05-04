<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
   
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;  

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'account_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole([
            'Portal Admin',
            'Portal User',
            'Account Admin',
        ]);
    }

    

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
 

    public function getAccountNameAttribute()
    {
        return $this->account?->name;
    }
    public function getAccountNumberAttribute()
    {
        return $this->account?->account_number;
    }
    public function getAccountStatusAttribute()
    {
        return $this->account?->status;
    }
    public function getAccountStatusNameAttribute()
    {
        return $this->account?->status_name;
    }
 

}
