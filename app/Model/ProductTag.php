<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductTag extends Pivot
{
    use HasFactory;
    protected $fillable = [
        'tag_id',
        'product_id',

    ];

    public function items()
    {
        return $this->belongsTo(Tag::class, 'tag_id'); // or whatever your FK is
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
