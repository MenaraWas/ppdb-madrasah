<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    public function applications()
    {
        return $this->hasMany(\App\Models\Application::class);
    }
}
