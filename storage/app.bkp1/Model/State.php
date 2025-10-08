<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $table = 'state_city';

    protected $fillable = ['name', 'parent_id'];

    // Relation to get cities of a state
    public function children()
    {
        return $this->hasMany(state_city::class, 'parent_id');
    }

    // Relation to get the parent state of a city
    public function parent()
    {
        return $this->belongsTo(state_city::class, 'parent_id');
    }
}
