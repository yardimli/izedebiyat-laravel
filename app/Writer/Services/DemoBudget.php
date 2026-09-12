<?php

namespace App\Writer\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DemoBudget
{
    public static function allowance(): float
    {
        return round(max(0, (float) config('writer.demo_limit', 1)), 8);
    }

    public static function limit(User $user): float
    {
        return (float) ($user->demo_limit ?? self::allowance());
    }

    public static function remaining(User $user): float
    {
        return max(0, self::limit($user) - (float) $user->demo_spent - (float) $user->demo_reserved);
    }

    public static function percentage(User $user): float
    {
        $allowance = (float) ($user->demo_allowance ?? self::allowance());

        return $allowance > 0 ? round(min(100, self::remaining($user) / $allowance * 100), 2) : 0;
    }

    public static function reset(User $user, User $admin): void
    {
        DB::transaction(function () use ($user, $admin) {
            $user = User::lockForUpdate()->findOrFail($user->id);
            $before = self::limit($user);
            $allowance = self::allowance();
            // Preserve settled spending and reservations for in-flight requests.
            $user->demo_limit = round((float) $user->demo_spent + (float) $user->demo_reserved + $allowance, 8);
            $user->demo_allowance = $allowance;
            $user->save();
            DB::table('writer_budget_resets')->insert(['user_id' => $user->id, 'admin_id' => $admin->id, 'previous_limit' => $before, 'new_limit' => $user->demo_limit, 'allowance' => $allowance, 'created_at' => now()]);
        });
    }
}
