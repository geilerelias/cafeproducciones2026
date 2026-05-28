<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['company_event_id', 'user_id', 'task', 'payment_amount', 'payment_status', 'registered_at', 'notes'];

    protected function casts(): array
    {
        return [
            'payment_amount' => 'decimal:2',
            'registered_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(CompanyEvent::class, 'company_event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
