<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSubscriptionWebhookEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_subscription_id',
        'provider',
        'event_type',
        'provider_reference',
        'payload',
        'signature_valid',
        'status_after',
        'processed_at',
    ];

    protected $casts = [
        'signature_valid' => 'boolean',
        'processed_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(SchoolSubscription::class, 'school_subscription_id');
    }
}
