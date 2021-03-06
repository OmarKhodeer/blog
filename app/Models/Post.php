<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['category', 'author'];

    // allow us to call Post::newQuery()->filter()
    public function scopeFilter($query, array $filters) // $query parameter passed automatically by laravel.
    {
        // ->when() : execute a callable function when the condition is true.
        // callable function args: ($query, $condition_value)
        // get all posts based on search request [ ?search=search_value ]
        $query->when(
            $filters['search'] ?? false,
            fn ($query, $search) =>  // $search = $filters['search']
            $query->where(
                fn ($query) =>
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('body', 'like', '%' . $search . '%')
            )
        );

        // get all posts that have a category slug [ ?category=this-is-a-slug ]
        $query->when(
            $filters['category'] ?? false,
            fn ($query, $category) =>  // $category = $filters['category']
            $query->whereHas(
                'category',
                fn ($query) => $query->where('slug', $category)
            )
        );

        $query->when(
            $filters['author'] ?? false,
            fn ($query, $author) =>
            $query->whereHas(
                'author',
                fn ($query) => $query->where('username', $author)
            )
        );
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
