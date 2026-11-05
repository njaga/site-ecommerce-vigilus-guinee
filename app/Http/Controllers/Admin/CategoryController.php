<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController
{
    public function index(): View
    {
        return view('admin.categories', [
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'alpha_dash', 'max:120', 'unique:categories,slug'],
            'description' => ['nullable', 'string', 'max:3000'],
            'seo_title' => ['nullable', 'string', 'max:75'],
            'seo_description' => ['nullable', 'string', 'max:170'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['required', 'boolean'],
        ]);

        Category::create($data);

        return back()->with('success', 'Catégorie créée.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:3000'],
            'seo_title' => ['nullable', 'string', 'max:75'],
            'seo_description' => ['nullable', 'string', 'max:170'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['required', 'boolean'],
        ]);

        // URLs déjà indexées : le slug n'est pas modifié depuis ce formulaire.
        $category->update($data);

        return back()->with('success', 'Catégorie et métadonnées SEO mises à jour.');
    }
}
