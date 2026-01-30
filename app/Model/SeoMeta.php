<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    protected $table = 'seo_meta';

    protected $fillable = [
        'page',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical',
        'content',
        'og_tags',
    ];
}
