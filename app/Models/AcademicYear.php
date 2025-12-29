<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    public function waves()
    {
        return $this->hasMany(\App\Models\Wave::class);
    }
}
