<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'is_suspended',
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

    /**
     * Get the events for the user (host).
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function availableBalance()
    {
        $totalEarned = \App\Models\Payment::whereHas('event', function($query) {
            $query->where('user_id', $this->id);
        })->where('status', 'successful')->sum('host_payout');

        $totalWithdrawnOrPending = $this->withdrawals()->whereIn('status', ['pending', 'approved', 'processing'])->sum('amount');

        return max(0, $totalEarned - $totalWithdrawnOrPending);
    }

    public function vendorProfile()
    {
        return $this->hasOne(VendorProfile::class);
    }

    public function promoterLinks()
    {
        return $this->hasMany(PromoterLink::class);
    }

    public function cohostedEvents()
    {
        return $this->hasMany(EventCohost::class);
    }
}
