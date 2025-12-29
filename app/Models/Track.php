<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'wave_id',
        'code',
        'name',
        'requires_interview',
        'quota',
        'reserve_enabled',
        'reserve_count',
        'is_active',
    ];

    protected $casts = [
        'requires_interview' => 'boolean',
        'reserve_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function wave()
    {
        return $this->belongsTo(\App\Models\Wave::class);
    }
}
