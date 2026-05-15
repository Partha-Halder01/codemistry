<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminBlogPostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::orderByDesc('created_at')->get();
        return response()->json($posts);
    }

    public function show($id)
    {
        $post = BlogPost::findOrFail($id);
        return response()->json($post);
    }

    public function store(Request $request)
    {
        $this->normalizeArrayFields($request);

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:blog_posts,slug',
            'excerpt'          => 'nullable|string',
            'cover_image'      => 'nullable|image|max:5120',
            'content_html'     => 'required|string',
            'content_css'      => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
            'tags'             => 'nullable|array',
            'tags.*'           => 'string',
            'author_name'      => 'nullable|string|max:120',
            'status'           => 'nullable|in:draft,published',
            'published_at'     => 'nullable|date',
        ]);

        $data = $request->only([
            'title', 'slug', 'excerpt', 'content_html', 'content_css',
            'meta_title', 'meta_description', 'meta_keywords',
            'tags', 'author_name', 'status', 'published_at',
        ]);

        $data['status'] = $data['status'] ?? 'draft';
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('blog_covers', 'public');
        }

        $post = BlogPost::create($data);

        return response()->json([
            'message' => 'Blog post created successfully',
            'post'    => $post,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        $this->normalizeArrayFields($request);

        $validated = $request->validate([
            'title'            => 'sometimes|required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:blog_posts,slug,' . $id,
            'excerpt'          => 'nullable|string',
            'cover_image'      => 'nullable|image|max:5120',
            'content_html'     => 'sometimes|required|string',
            'content_css'      => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
            'tags'             => 'nullable|array',
            'tags.*'           => 'string',
            'author_name'      => 'nullable|string|max:120',
            'status'           => 'nullable|in:draft,published',
            'published_at'     => 'nullable|date',
        ]);

        $data = $request->only([
            'title', 'slug', 'excerpt', 'content_html', 'content_css',
            'meta_title', 'meta_description', 'meta_keywords',
            'tags', 'author_name', 'status', 'published_at',
        ]);

        if (isset($data['status']) && $data['status'] === 'published' && empty($data['published_at']) && empty($post->published_at)) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('cover_image')) {
            if ($post->cover_image_path && \Storage::disk('public')->exists($post->cover_image_path)) {
                \Storage::disk('public')->delete($post->cover_image_path);
            }
            $data['cover_image_path'] = $request->file('cover_image')->store('blog_covers', 'public');
        }

        $post->update($data);

        return response()->json([
            'message' => 'Blog post updated successfully',
            'post'    => $post->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);
        if ($post->cover_image_path && \Storage::disk('public')->exists($post->cover_image_path)) {
            \Storage::disk('public')->delete($post->cover_image_path);
        }
        $post->delete();
        return response()->json(['message' => 'Blog post deleted successfully']);
    }

    public function togglePublish($id)
    {
        $post = BlogPost::findOrFail($id);
        if ($post->status === 'published') {
            $post->status = 'draft';
        } else {
            $post->status = 'published';
            if (!$post->published_at) {
                $post->published_at = now();
            }
        }
        $post->save();
        return response()->json(['message' => 'Status updated', 'post' => $post]);
    }

    private function normalizeArrayFields(Request $request): void
    {
        // tags may arrive as a JSON string from multipart/form-data
        if ($request->has('tags') && is_string($request->tags)) {
            $decoded = json_decode($request->tags, true);
            if (is_array($decoded)) {
                $request->merge(['tags' => $decoded]);
            } else {
                // fallback: comma-separated string
                $request->merge(['tags' => array_values(array_filter(array_map('trim', explode(',', $request->tags))))]);
            }
        }
    }
}
