<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class HelpTopicCategory extends Model
{
    protected $table = 'help_topics_category';
    protected $casts = [

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $fillable = [
        'name',
    ];

    public function helpTopic()
    {
        return $this->hasMany(HelpTopic::class, 'category', 'id');
    }
    public function subcategories()
    {
        return $this->hasMany(HelpTopicSubcategory::class, 'cat_id', 'id');
    }
    public function faqs()
    {
        return $this->hasMany(HelpTopic::class, 'category_id', 'id');
    }
}
