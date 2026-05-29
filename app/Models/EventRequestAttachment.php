<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EventRequestAttachment extends Model
{
    public const LABELS = [
        'brief' => 'Brief / requerimientos',
        'cotizacion' => 'Cotizacion',
        'contrato' => 'Contrato',
        'otro' => 'Otro documento',
    ];

    protected $fillable = [
        'event_request_id',
        'uploaded_by',
        'label',
        'original_name',
        'path',
        'mime_type',
        'size',
        'visible_to_client',
    ];

    protected function casts(): array
    {
        return [
            'visible_to_client' => 'boolean',
        ];
    }

    public function eventRequest(): BelongsTo
    {
        return $this->belongsTo(EventRequest::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function deleteFile(): void
    {
        Storage::disk('local')->delete($this->path);
    }
}
