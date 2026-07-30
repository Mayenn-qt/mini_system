<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'subscribed'
    ];

    protected $casts = [
        'subscribed' => 'boolean'
    ];

    public function smsHistories(): HasMany
    {
        return $this->hasMany(SmsHistory::class);
    }
}
