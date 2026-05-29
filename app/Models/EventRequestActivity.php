<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRequestActivity extends Model
{
    protected $fillable = [
        'event_request_id',
        'user_id',
        'type',
        'body',
        'meta',
        'visible_to_client',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'visible_to_client' => 'boolean',
        ];
    }

    public function eventRequest(): BelongsTo
    {
        return $this->belongsTo(EventRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
