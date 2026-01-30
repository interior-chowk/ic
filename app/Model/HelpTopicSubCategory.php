<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class HelpTopicSubCategory extends Model
{
    protected $table = 'help_topics_sub_category';
    protected $casts = [

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $fillable = [
        'sub_cat_name',
        'cat_id',
        'link',
        'link_name',
        'link_short_description',
    ];

    public function category()
    {
        return $this->belongsTo(HelpTopicCategory::class, 'cat_id');
    }

    public function faqs()
    {
        return $this->hasMany(HelpTopic::class, 'sub_cat_id', 'id');
    }



}
