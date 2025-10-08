<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class HomeProduct extends Model
{
    protected $casts = [

        'product_id'    => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class)->where('request_status', 1)->where('status', 1)->active()->temporary();
    }
   
}
