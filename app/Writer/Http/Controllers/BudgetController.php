<?php

namespace App\Writer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Writer\Services\DemoBudget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $columns = ['name' => 'users.name', 'email' => 'users.email', 'total_spent' => 'writer_total_spent',
            'demo_spent' => 'users.demo_spent', 'pending' => 'users.demo_reserved',
            'limit' => 'writer_limit', 'remaining' => 'writer_remaining', 'percentage' => 'writer_percentage'];
        $data = $request->validate(['search' => 'nullable|string|max:200',
            'sort' => 'sometimes|in:'.implode(',', array_keys($columns)), 'direction' => 'sometimes|in:asc,desc',
            'per_page' => 'sometimes|integer|in:25,50,100']);
        $search = trim($data['search'] ?? '');
        $sort = $data['sort'] ?? 'name';
        $direction = $data['direction'] ?? 'asc';
        $perPage = (int) ($data['per_page'] ?? 50);
        // Match DemoBudget's fallback, zero floor, and percentage cap before pagination.
        $default = number_format(DemoBudget::allowance(), 8, '.', '');
        $limit = "COALESCE(users.demo_limit, $default)";
        $available = "($limit - COALESCE(users.demo_spent, 0) - COALESCE(users.demo_reserved, 0))";
        $remaining = "(CASE WHEN $available > 0 THEN $available ELSE 0 END)";
        $allowance = "COALESCE(users.demo_allowance, $default)";
        $ratio = "($remaining * 100.0 / NULLIF($allowance, 0))";
        $percentage = "ROUND(CASE WHEN $allowance <= 0 THEN 0 WHEN $ratio > 100 THEN 100 ELSE $ratio END, 2)";
        $users = User::query()->addSelect(['users.*', 'writer_total_spent' => \App\Writer\Models\AiCall::selectRaw('COALESCE(SUM(cost), 0)')->whereColumn('writer_ai_calls.user_id', 'users.id')])
            ->selectRaw("$limit AS writer_limit, $remaining AS writer_remaining, $percentage AS writer_percentage")
            ->when($search !== '', function ($query) use ($search) {
                $pattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search).'%';
                $query->where(fn ($q) => $q->whereRaw("users.name LIKE ? ESCAPE '!'", [$pattern])->orWhereRaw("users.email LIKE ? ESCAPE '!'", [$pattern]));
            })
            ->orderBy($columns[$sort], $direction)->orderBy('users.id')->paginate($perPage)->withQueryString();

        return view('writer.budgets', compact('users', 'search', 'sort', 'direction', 'perPage'));
    }

    public function reset(Request $request, User $user)
    {
        abort_unless($request->user()->isAdmin(), 403);
        DemoBudget::reset($user, $request->user());

        return back()->with('status', 'Yapay zekâ kotası %100 olarak yenilendi.');
    }
}
