<?php

namespace App\Http\Controllers\Admin;

use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class BlogPostController
{
    public function index(Request $request): View
    {
        $query = BlogPost::query()->latest();
        $status = (string) $request->query('statut', '');
        if (in_array($status, ['draft', 'published'], true)) {
            $query->where('status', $status);
        }

        return view('admin.blog.index', [
            'posts' => $query->paginate(20)->withQueryString(),
            'selectedStatus' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.blog.form', [
            'post' => new BlogPost(['category' => 'Conseils', 'status' => 'draft']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $slug = Str::slug($data['title']);

        if ($slug === '' || BlogPost::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages([
                'title' => 'Ce titre génère une URL déjà utilisée. Choisissez un titre distinct.',
            ]);
        }

        $data['slug'] = $slug;
        $data['author_id'] = $request->user()->id;
        $newImagePath = $this->uploadImage($request);

        try {
            $data['cover_path'] = $newImagePath;
            $post = DB::transaction(fn () => BlogPost::create($data));
        } catch (Throwable $e) {
            if ($newImagePath) Storage::disk('public')->delete($newImagePath);
            throw $e;
        }

        return redirect()->route('admin.blog.edit', $post)
            ->with('success', $post->status === 'published'
                ? 'Article publié.' : 'Article enregistré en brouillon.');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog.form', ['post' => $blogPost]);
    }

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $data = $this->validatedData($request, $blogPost);
        $oldPath = $blogPost->cover_path;
        $newImagePath = $this->uploadImage($request);

        try {
            if ($newImagePath) $data['cover_path'] = $newImagePath;
            DB::transaction(fn () => $blogPost->update($data));
        } catch (Throwable $e) {
            if ($newImagePath) Storage::disk('public')->delete($newImagePath);
            throw $e;
        }

        if ($newImagePath && $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('admin.blog.edit', $blogPost)
            ->with('success', $blogPost->status === 'published'
                ? 'Article mis à jour.' : 'Brouillon enregistré.');
    }

    public function archive(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->update(['status' => 'draft', 'is_featured' => false]);
        return back()->with('success', 'Article retiré du site public.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        if ($blogPost->status !== 'draft' || $blogPost->published_at !== null) {
            throw ValidationException::withMessages([
                'article' => 'Un article déjà publié doit être conservé pour protéger ses URL et son historique SEO.',
            ]);
        }

        $path = $blogPost->cover_path;
        $blogPost->delete();
        if ($path) Storage::disk('public')->delete($path);

        return redirect()->route('admin.blog.index')->with('success', 'Brouillon supprimé.');
    }

    public function preview(BlogPost $blogPost): View
    {
        return view('store.blog.show', [
            'post' => $blogPost,
            'related' => collect(),
            'preview' => true,
        ]);
    }

    private function validatedData(Request $request, ?BlogPost $post = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:8', 'max:220'],
            'category' => ['required', 'string', 'max:70'],
            'excerpt' => ['required', 'string', 'min:45', 'max:360'],
            'body' => ['required', 'string', 'min:180', 'max:50000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'cover_alt' => ['nullable', 'string', 'max:180'],
            'seo_title' => ['nullable', 'string', 'max:75'],
            'seo_description' => ['nullable', 'string', 'max:165'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'is_featured' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        if ($data['status'] === 'published') {
            if (! $request->hasFile('cover_image') && ! $post?->cover_path) {
                throw ValidationException::withMessages([
                    'cover_image' => 'Ajoutez une image de couverture avant publication.',
                ]);
            }
            if (blank($data['cover_alt'] ?? '')) {
                throw ValidationException::withMessages([
                    'cover_alt' => 'Décrivez brièvement l’image avant publication.',
                ]);
            }
        }

        unset($data['cover_image']);
        $data['is_featured'] = $request->boolean('is_featured');
        if ($data['status'] === 'published') {
            $data['published_at'] = $data['published_at']
                ?? $post?->published_at
                ?? now();
        } elseif ($post && ! $request->filled('published_at')) {
            $data['published_at'] = $post->published_at;
        }

        return $data;
    }

    private function uploadImage(Request $request): ?string
    {
        if (! $request->hasFile('cover_image')) return null;

        $path = $request->file('cover_image')->store('blog', 'public');
        if ($path === false) {
            throw ValidationException::withMessages([
                'cover_image' => 'Impossible de téléverser la photo.',
            ]);
        }

        return $path;
    }
}
