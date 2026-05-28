<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tool extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'status', 'notes'];

    public function assignments(): HasMany
    {
        return $this->hasMany(ToolAssignment::class);
    }
}
