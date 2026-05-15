<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::published()
            ->select(['id', 'slug', 'title', 'excerpt', 'cover_image_path', 'tags', 'author_name', 'published_at', 'view_count']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($tag = $request->query('tag')) {
            // tags column is JSON; whereJsonContains works on MySQL
            $query->whereJsonContains('tags', $tag);
        }

        $posts = $query->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        return response()->json($posts);
    }

    public function latest()
    {
        $posts = BlogPost::published()
            ->select(['id', 'slug', 'title', 'excerpt', 'cover_image_path', 'tags', 'author_name', 'published_at'])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return response()->json(['data' => $posts]);
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()->where('slug', $slug)->first();

        if (!$post) {
            return response()->json(['message' => 'Post not found.'], 404);
        }

        $post->increment('view_count');

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->select(['id', 'slug', 'title', 'excerpt', 'cover_image_path', 'published_at'])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return response()->json([
            'post'    => $post,
            'related' => $related,
        ]);
    }
}
