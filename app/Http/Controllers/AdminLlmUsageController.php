<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminLlmUsageController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $filters = $request->validate([
            'user' => 'nullable|string|max:200',
            'article' => 'nullable|string|max:200',
            'model' => 'nullable|string|max:255',
            'from' => 'nullable|date_format:Y-m-d',
            'to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('from') ? ['after_or_equal:from'] : [])],
        ]);
        $query = DB::table('writer_ai_calls as calls')
            ->leftJoin('users', 'users.id', '=', 'calls.user_id')
            ->leftJoin('articles', 'articles.id', '=', 'calls.book_id');
        foreach (['user' => ['users.name', 'users.email'], 'article' => ['articles.title', 'articles.slug']] as $filter => $columns) {
            if (filled($filters[$filter] ?? null)) {
                $value = trim($filters[$filter]);
                $pattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $value).'%';
                $query->where(function ($q) use ($columns, $pattern, $filter, $value) {
                    foreach ($columns as $column) {
                        $q->orWhereRaw("{$column} LIKE ? ESCAPE '!'", [$pattern]);
                    }
                    if (ctype_digit($value)) {
                        $q->orWhere($filter === 'user' ? 'calls.user_id' : 'calls.book_id', $value);
                    }
                });
            }
        }
        if (filled($filters['model'] ?? null)) {
            $query->where('calls.model', $filters['model']);
        }
        if (filled($filters['from'] ?? null)) {
            $query->where('calls.created_at', '>=', Carbon::parse($filters['from'])->startOfDay());
        }
        if (filled($filters['to'] ?? null)) {
            $query->where('calls.created_at', '<', Carbon::parse($filters['to'])->addDay()->startOfDay());
        }
        // Only derive a missing total when both component counts are known.
        $total = 'COALESCE(calls.total_tokens, calls.prompt_tokens + calls.completion_tokens)';
        $summary = (clone $query)->selectRaw("COUNT(*) AS calls, COUNT(DISTINCT calls.user_id) AS users,
            SUM(calls.prompt_tokens) AS prompt_tokens, SUM(calls.completion_tokens) AS completion_tokens,
            SUM({$total}) AS total_tokens, COUNT({$total}) AS recorded")->first();
        $calls = $query->select([
            'calls.id', 'calls.user_id', 'calls.book_id', 'calls.model', 'calls.stage', 'calls.status',
            'calls.funding', 'calls.prompt_tokens', 'calls.completion_tokens', 'calls.created_at',
            'users.name as user_name', 'users.email', 'users.slug as user_slug',
            'articles.title as article_title', 'articles.slug as article_slug',
            'articles.is_published', 'articles.approved', 'articles.deleted',
        ])->selectRaw("{$total} AS total_tokens")
            ->orderByDesc('calls.created_at')->orderByDesc('calls.id')->paginate(50)->withQueryString();
        $models = DB::table('writer_ai_calls')->distinct()->orderBy('model')->pluck('model');

        return response()->view('backend.admin_llm_usage', compact('calls', 'summary', 'models', 'filters'))
            ->header('Cache-Control', 'private, no-store');
    }
}
