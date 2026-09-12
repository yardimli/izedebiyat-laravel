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
        $search = $request->validate(['search' => 'nullable|string|max:200'])['search'] ?? '';
        $users = User::query()->addSelect(['users.*', 'writer_total_spent' => \App\Writer\Models\AiCall::selectRaw('COALESCE(SUM(cost), 0)')->whereColumn('writer_ai_calls.user_id', 'users.id')])->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')))->orderBy('name')->paginate(50)->withQueryString();

        return view('writer.budgets', compact('users', 'search'));
    }

    public function reset(Request $request, User $user)
    {
        abort_unless($request->user()->isAdmin(), 403);
        DemoBudget::reset($user, $request->user());

        return back()->with('status', 'Yapay zekâ kotası %100 olarak yenilendi.');
    }
}
