<?php

namespace App\Model;
use App\Model\Career;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $fillable = [
        'career_id',
        'full_name',
        'city',
        'phone',
        'email',
        'experience',
        'portfolio_links',
        'resume',
    ];

    public function career()
    {
        return $this->belongsTo(Career::class, 'career_id');
    }

}
