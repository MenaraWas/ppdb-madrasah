<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_profile_id',
        'wave_id',
        'track_id',
        'status_utama',
        'verification_status',
        'physical_status',
        'physical_file_status',
        'interview_status',
        'ranking_status',
        'final_status',
        'enrollment_status',
        'registered_at',
        'submitted_at',
        'status_token_hash',
        'token_created_at',
        'token_last_sent_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'submitted_at' => 'datetime',
        'token_created_at' => 'datetime',
        'token_last_sent_at' => 'datetime',
    ];

    public function studentProfile()
    {
        return $this->belongsTo(\App\Models\StudentProfile::class);
    }

    public function wave()
    {
        return $this->belongsTo(\App\Models\Wave::class);
    }

    public function track()
    {
        return $this->belongsTo(\App\Models\Track::class);
    }
}
