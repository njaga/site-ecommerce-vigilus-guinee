<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController
{
    public function index(Request $request): View
    {
        $posts = BlogPost::published()
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12);

        return view('store.blog.index', [
            'posts' => $posts,
            'primary' => $posts->getCollection()->first(),
            'secondary' => $posts->getCollection()->slice(1, 2),
            'remaining' => $posts->getCollection()->slice(3),
        ]);
    }

    public function show(string $slug): View
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();
        $related = BlogPost::published()->whereKeyNot($post->id)
            ->orderByDesc('published_at')->limit(3)->get();

        return view('store.blog.show', compact('post', 'related'));
    }
}
