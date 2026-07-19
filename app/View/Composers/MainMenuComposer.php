<?php

namespace App\View\Composers;

use App\Models\Category;
use Illuminate\View\View;

class MainMenuComposer
{
    public function compose(View $view): void
    {
        if (array_key_exists('mainMenuCategories', $view->getData())) {
            return;
        }

        $view->with('mainMenuCategories', Category::query()
            ->where('parent_category_id', 0)
            ->orderBy('slug')
            ->with('subCategories')
            ->get());
    }
}
