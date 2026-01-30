<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    protected $table = 'new_cart';
    protected $casts = [
        'price' => 'float',
        'discount' => 'float',
        'tax' => 'float',
        'seller_id' => 'integer',
        'quantity' => 'integer',
    ];

    // public function cart_shipping(){
    //     return $this->hasOne(CartShipping::class,'cart_group_id','cart_group_id');
    // }

    public function cart_shipping()
    {
        return $this->hasOne(CartShipping::class, 'cart_group_id', 'cart_group_id')
            ->whereRaw("cart_shippings.cart_group_id COLLATE utf8mb4_unicode_ci = new_cart.cart_group_id COLLATE utf8mb4_unicode_ci");
    }


    public function product()
    {
        return $this->belongsTo(Product::class)->where('status', 1);
    }
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
    
   


}