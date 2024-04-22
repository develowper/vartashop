<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'slug',
        'owner_id',
        'author',
        'title',
        'content',
        'duration',
        'tags',
        'view',
        'status',
        'created_at',
        'updated_at',
    ];


    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public static function categories(string $type = 'parents')
    {
        switch ($type) {
            case    'parents':
                return Category::select('id', 'name')->where('parent_id', null)->get();
            default:
                return [];
        }
    }
}
