<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
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
