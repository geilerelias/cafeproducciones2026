<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'client_user_id',
        'created_by',
        'title',
        'event_type',
        'desired_date',
        'location',
        'description',
        'guest_count',
        'budget_notes',
        'stage_key',
        'client_message',
        'internal_notes',
        'position',
        'submitted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'desired_date' => 'date',
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(EventRequestTask::class)->orderBy('position');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(EventRequestActivity::class)->latest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(EventRequestAttachment::class)->latest();
    }

    public function stage(): ?EventRequestStage
    {
        return EventRequestStage::query()->where('key', $this->stage_key)->first();
    }

    public static function nextReference(): string
    {
        $year = now()->format('Y');
        $count = static::query()->whereYear('created_at', $year)->count() + 1;

        return sprintf('CAFE-EVT-%s-%04d', $year, $count);
    }
}
