<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackPublication extends Model
{
    use HasFactory;

    protected $fillable = [
        'track_id',
        'state',
        'is_published',
        'published_at',
        'ever_published_at',
        'locked_final_at',
        'locked_by_user_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'ever_published_at' => 'datetime',
        'locked_final_at' => 'datetime',
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by_user_id');
    }
}
