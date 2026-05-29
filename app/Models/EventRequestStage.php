<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRequestStage extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'color',
        'sort_order',
        'is_terminal',
        'visible_to_client',
    ];

    protected function casts(): array
    {
        return [
            'is_terminal' => 'boolean',
            'visible_to_client' => 'boolean',
        ];
    }
}
