<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRequestTask extends Model
{
    protected $fillable = [
        'event_request_id',
        'title',
        'description',
        'status',
        'visible_to_client',
        'due_date',
        'assigned_to',
        'position',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'visible_to_client' => 'boolean',
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function eventRequest(): BelongsTo
    {
        return $this->belongsTo(EventRequest::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
