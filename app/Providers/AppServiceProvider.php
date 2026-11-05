<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Gate::define('manage-catalog', fn (User $user): bool => (bool) $user->is_admin);

        View::composer('layouts.store', function ($view): void {
            $view->with('navCategories', Category::storefront()->orderBy('sort_order')->limit(8)->get());
        });
    }
}
