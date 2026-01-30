<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Model\Product;

class BulkPurchaseList extends Model
{
    use HasFactory;

    protected $table = 'bulk_purchase_list';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'product_id', // use product_id for relationship
        'quantity',
        'remark',
    ];

    public $timestamps = true;

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
