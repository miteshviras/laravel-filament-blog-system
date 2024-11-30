<?php

namespace App\Models;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    protected $fillable = ['post_id', 'category_id'];

    public function casts()
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function posts()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function categories(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
