<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Post extends Model
{
    use SoftDeletes;
    protected $fillable = ['title', 'slug', 'content', 'summary', 'difficulty_level', 'duration', 'thumbnail', 'is_published', 'published_at', 'user_id'];

    public function casts()
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
    const DIFFICULTY_LEVELS = ['Beginner', 'Intermediate', 'Advanced'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * The categories that belong to the post.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'post_categories', 'post_id');
    }

     /**
     * Get the user's first name.
     */
    protected function categoryIds(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->categories()->pluck('categories.id')->toArray(),
        );
    }
}
