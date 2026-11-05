<?php

namespace App\Support;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryVisuals
{
    /**
     * Editorial illustrations, never evidence of an exact manufacturer SKU.
     * Filenames and external references are documented for manual rights review.
     *
     * @return array{url:string,source:string,label:string}
     */
    public function for(Category $category): array
    {
        $name = Str::lower(Str::ascii($category->name.' '.$category->slug));
        foreach (config('visuals.category_rules') as $rule) {
            foreach ($rule['matches'] as $word) {
                if (str_contains($name, $word)) {
                    return $rule['visual'];
                }
            }
        }

        return config('visuals.default_category');
    }
}
