<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'nisn',
        'name',
        'whatsapp',
        'initiated_at',
        'last_activity_at',
        'initiated_ip',
    ];

    protected $casts = [
        'initiated_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
