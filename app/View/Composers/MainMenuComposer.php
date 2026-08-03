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
    public const CACHE_KEY = 'frontend.mega-menu.v1';

    public function compose(View $view): void
    {
        $menu = Cache::remember(self::CACHE_KEY, now()->addMinutes(15), function (): array {
            $categories = Category::query()->where('parent_category_id', 0)->orderBy('slug')
                ->with(['subCategories' => fn ($query) => $query->orderBy('category_name')])->get();
            $articleQuery = fn () => Article::query()
                ->where('approved', 1)->where('is_published', 1)->where('deleted', 0)->where('moderation_flagged', 0);

            $totalByParent = $articleQuery()->selectRaw('parent_category_id, COUNT(*) as aggregate')
                ->groupBy('parent_category_id')->pluck('aggregate', 'parent_category_id');
            $newByParent = $articleQuery()->where('created_at', '>=', now()->subDays(30))
                ->selectRaw('parent_category_id, COUNT(*) as aggregate')
                ->groupBy('parent_category_id')->pluck('aggregate', 'parent_category_id');
            $totalByCategory = $articleQuery()->selectRaw('category_id, COUNT(*) as aggregate')
                ->groupBy('category_id')->pluck('aggregate', 'category_id');
            $newByCategory = $articleQuery()->where('created_at', '>=', now()->subDays(30))
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
            }

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

            return compact('categories', 'recentArticles', 'topArticles', 'forumTags', 'recentDiscussions', 'recentBookReviews');
        });

        $view->with('mainMenuCategories', $menu['categories']);
        $view->with('megaMenuRecentArticles', $menu['recentArticles']);
        $view->with('megaMenuTopArticles', $menu['topArticles']);
        $view->with('megaMenuForumTags', $menu['forumTags']);
        $view->with('megaMenuRecentDiscussions', $menu['recentDiscussions']);
        $view->with('megaMenuRecentBookReviews', $menu['recentBookReviews']);
    }
}
