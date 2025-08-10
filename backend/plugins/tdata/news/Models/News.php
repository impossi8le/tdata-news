<?php namespace TData\News\Models;

use Model;

/**
 * News Model
 */
class News extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'tdata_news_news'; // убедитесь, что совпадает с миграцией

    public $fillable = [
        'title',
        'slug',
        'content',
        'published_at',
        'is_published',
    ];

    public $rules = [
        'title' => 'required',
        'slug'  => 'required|unique:tdata_news_news',
        'content' => 'required',
    ];
}
