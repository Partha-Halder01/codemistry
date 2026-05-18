<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'excerpt',
        'cover_image_path',
        'content_html',
        'content_css',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'tags',
        'author_name',
        'status',
        'published_at',
        'view_count',
    ];

    protected $casts = [
        'tags'         => 'array',
        'published_at' => 'datetime',
        'view_count'   => 'integer',
    ];

    protected $appends = ['cover_image_url'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = self::makeUniqueSlug($post->title);
            }
        });

        static::updating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = self::makeUniqueSlug($post->title, $post->id);
            }
        });
    }

    public static function makeUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $i = 2;
        while (
            self::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image_path) return null;
        // Allow absolute URLs (e.g. Unsplash CDN) to pass through unchanged.
        if (preg_match('#^https?://#i', $this->cover_image_path)) {
            return $this->cover_image_path;
        }
        return Storage::disk('public')->url($this->cover_image_path);
    }
}
