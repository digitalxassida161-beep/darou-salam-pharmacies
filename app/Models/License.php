<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class License extends Model
{
    protected $fillable = [
        'license_key',
        'pharmacy_name',
        'pharmacy_address',
        'pharmacy_phone',
        'installation_date',
        'maintenance_expires_at',
        'subscription_expires_at',
        'status',
        'logo',
    ];

    protected function casts(): array
    {
        return [
            'installation_date' => 'date',
            'maintenance_expires_at' => 'date',
            'subscription_expires_at' => 'date',
        ];
    }

    public function subscriptionPayments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }
}
