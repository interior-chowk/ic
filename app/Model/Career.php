<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
    protected $fillable = [
        'title', 'department', 'employment_type', 'experience', 'salary',
        'location', 'openings', 'applicants',
        'education', 'skills', 'job_description',
    ];

    protected $casts = [
        'education' => 'array',
        'skills' => 'array',
        'job_description' => 'array',
    ];
}
