<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'plan',
        'status',
        'provider',
        'provider_reference',
        'trial_ends_at',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function webhookEvents()
    {
        return $this->hasMany(SchoolSubscriptionWebhookEvent::class, 'school_subscription_id');
    }
}
