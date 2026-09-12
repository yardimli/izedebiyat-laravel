<?php

namespace App\Writer\Http\Controllers;

use App\Models\Category;
use App\Writer\Models\Book;
use App\Writer\Services\OpenRouter;
use Illuminate\Http\Request;

class PublicationAiController extends \App\Http\Controllers\Controller
{
    public function suggest(Request $request, Book $book, string $kind, OpenRouter $router)
    {
        app(BookController::class)->owned($request, $book);
        $data = $request->validate(['text' => 'required|string|max:60000', 'model' => 'required|string|max:200']);
        $model = $router->model($data['model']);
        $categories = Category::with('parentCategory')->where('parent_category_id', '>', 0)->get();
        $instruction = $kind === 'category'
            ? 'Choose exactly one category from this list. Return JSON {"category_id": integer}: '. $categories->map(fn ($c) => ['id' => $c->id, 'name' => $c->parentCategory?->category_name.' / '.$c->category_name])->toJson(JSON_UNESCAPED_UNICODE)
            : 'Suggest 5 to 10 Turkish tags, each at most 16 characters. Return JSON {"keywords": ["tag", ...]}.';
        $messages = [['role' => 'system', 'content' => $instruction], ['role' => 'user', 'content' => $data['text']]];
        $call = $router->reserve($request->user(), $book, $model, $messages, 'publication-'.$kind);
        $result = $router->send($request->user(), $call, $model, $messages);
        if ($kind === 'category') {
            abort_unless($categories->contains('id', $result['category_id'] ?? null), 422, __('AI returned an invalid category.'));
            return ['category_id' => (int) $result['category_id']];
        }
        $tags = collect(is_array($result['keywords'] ?? null) ? $result['keywords'] : [])
            ->filter(fn ($tag) => is_string($tag) && trim($tag) !== '' && mb_strlen(trim($tag)) <= 16 && ! str_contains($tag, ','))
            ->map(fn ($tag) => trim($tag))->unique()->take(10)->values();
        abort_if($tags->isEmpty(), 422, __('AI returned no valid tags.'));
        return ['keywords_string' => $tags->implode(', ')];
    }
}
