<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'outlet_id',
        'business_id',
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

    public function isOwner(): bool
    {
        return $this->role_id === 1;
    }

    public function isCashier(): bool
    {
        return $this->role_id === 2;
    }

    public function hasAccessToOutlet($outletId): bool
    {
        if ($this->isOwner()) {
            return Outlet::where('id', $outletId)
                ->where('business_id', $this->business_id)
                ->exists();
        }
        return $this->outlet_id === $outletId;
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function ownedBusiness()
    {
        return $this->hasOne(Business::class, 'owner_id');
    }

    /**
     * Get the role that owns the user.
     */

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the outlet that owns the user.
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
