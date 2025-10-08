<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProviderBanner extends Model
{
    protected $casts = [
        'published'  => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'resource_id' => 'integer',
    ];

    public function product(){
        return $this->belongsTo(Product::class,'resource_id');
    }

}
    