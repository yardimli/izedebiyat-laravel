<?php

namespace App\View\Composers;

use App\Models\Article;
use App\Models\BookReview;
use App\Models\Category;
use App\Models\ForumDiscussion;
use App\Models\ForumTag;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class MainMenuComposer
{
    public const CACHE_KEY = 'frontend.mega-menu.hourly.v3';

    public function compose(View $view): void
    {
        $menu = Cache::get(self::CACHE_KEY);
        if ($menu === null) {
            // A cold cache must never start article aggregation in a web worker.
            $categories = Category::where('parent_category_id', 0)->orderBy('slug')
                ->with('subCategories')->get();
            foreach ($categories as $category) {
                $category->menu_total = (int) $category->total_articles;
                $category->menu_new = 0;
                $category->menu_recent = collect();
                foreach ($category->subCategories as $child) {
                    $child->menu_total = (int) $child->total_articles;
                    $child->menu_new = 0;
                }
            }
            $menu = array_fill_keys(['recentArticles', 'topArticles', 'forumTags', 'recentDiscussions', 'recentBookReviews'], collect());
            $menu['categories'] = $categories;
        }

        $view->with('mainMenuCategories', $menu['categories']);
        $view->with('megaMenuRecentArticles', $menu['recentArticles']);
        $view->with('megaMenuTopArticles', $menu['topArticles']);
        $view->with('megaMenuForumTags', $menu['forumTags']);
        $view->with('megaMenuRecentDiscussions', $menu['recentDiscussions']);
        $view->with('megaMenuRecentBookReviews', $menu['recentBookReviews']);
    }

    public function refresh(): void
    {
        $categories = Category::query()->where('parent_category_id', 0)->orderBy('slug')
            ->with(['subCategories' => fn ($query) => $query->orderBy('category_name')])->get();
        $articleQuery = fn () => Article::query()
            ->where('approved', 1)->where('is_published', 1)->where('deleted', 0)->where('moderation_flagged', 0);

        $totalByParent = $articleQuery()->selectRaw('parent_category_id, COUNT(*) as aggregate')
            ->groupBy('parent_category_id')->pluck('aggregate', 'parent_category_id');
        $newByParent = $articleQuery()->where('created_at', '>=', now()->subMonths(3))
            ->selectRaw('parent_category_id, COUNT(*) as aggregate')
            ->groupBy('parent_category_id')->pluck('aggregate', 'parent_category_id');
        $totalByCategory = $articleQuery()->selectRaw('category_id, COUNT(*) as aggregate')
            ->groupBy('category_id')->pluck('aggregate', 'category_id');
        $newByCategory = $articleQuery()->where('created_at', '>=', now()->subMonths(3))
            ->selectRaw('category_id, COUNT(*) as aggregate')
            ->groupBy('category_id')->pluck('aggregate', 'category_id');

        foreach ($categories as $category) {
            $category->menu_total = (int) ($totalByParent[$category->id] ?? 0);
            $category->menu_new = (int) ($newByParent[$category->id] ?? 0);
            $category->menu_recent = $articleQuery()->where('parent_category_id', $category->id)
                ->latest('created_at')->limit(5)->get(['id', 'title', 'slug', 'created_at']);
            foreach ($category->subCategories as $subCategory) {
                $subCategory->menu_total = (int) ($totalByCategory[$subCategory->id] ?? 0);
                $subCategory->menu_new = (int) ($newByCategory[$subCategory->id] ?? 0);
            }

            $category->setRelation('subCategories', $category->subCategories
                ->sort(fn ($a, $b) => $b->menu_new <=> $a->menu_new
                    ?: strcasecmp(strip_tags($a->category_name), strip_tags($b->category_name)))
                ->values());
        }

        $categories = $categories
            ->sort(fn ($a, $b) => $b->menu_new <=> $a->menu_new
                ?: strcasecmp(strip_tags($a->category_name), strip_tags($b->category_name)))
            ->values();

        $recentArticles = $articleQuery()->latest('created_at')->limit(5)
            ->get(['id', 'title', 'slug', 'created_at']);
        $topArticles = $articleQuery()->orderByDesc('read_count')->limit(5)
            ->get(['id', 'title', 'slug', 'read_count']);
        $forumTags = ForumTag::query()->where('is_active', true)->withCount('discussions')
            ->orderBy('sort_order')->orderBy('name')->limit(10)->get(['id', 'name', 'slug', 'color']);
        $recentDiscussions = ForumDiscussion::query()->with('tag:id,name,slug,color')->withCount('posts')
            ->orderByDesc('last_post_at')->orderByDesc('created_at')->limit(5)
            ->get(['id', 'forum_tag_id', 'title', 'slug', 'last_post_at', 'created_at']);
        $recentBookReviews = collect();
        if (config('features.kitap_izleri_visible')) {
            $recentBookReviews = BookReview::query()->where('is_published', true)
                ->with('bookAuthor:id,name,slug')->orderByDesc('published_at')->orderByDesc('created_at')
                ->limit(5)->get(['id', 'book_author_id', 'title', 'author', 'slug', 'published_at', 'created_at']);
        }

        $menu = compact('categories', 'recentArticles', 'topArticles', 'forumTags', 'recentDiscussions', 'recentBookReviews');

        // Keep the last successful snapshot if a future refresh fails or runs late.
        Cache::forever(self::CACHE_KEY, $menu);
    }
}
