<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->orderBy('id')->chunkById(200, function ($users) {
            foreach ($users as $user) {
                DB::transaction(function () use ($user) {
                    $calls = DB::table('writer_ai_calls')->where('user_id', $user->id)
                        ->where('funding', 'demo')
                        ->whereIn('stage', ['publication-category', 'publication-keywords', 'publication-synopsis', 'image-generation'])
                        ->lockForUpdate()->get();
                    if ($calls->isEmpty()) return;
                    $user = DB::table('users')->where('id', $user->id)->lockForUpdate()->first();
                    $spent = max(0, (float) $user->demo_spent - (float) $calls->sum('cost'));
                    $reserved = max(0, (float) $user->demo_reserved - (float) $calls->whereNull('cost')->whereIn('status', ['reserved', 'pending'])->sum('reserved'));
                    $updates = ['demo_spent' => round($spent, 8), 'demo_reserved' => round($reserved, 8)];
                    $resetAt = DB::table('writer_budget_resets')->where('user_id', $user->id)->max('created_at');
                    $previousResetOffset = $resetAt ? $calls->sum(function ($call) use ($resetAt) {
                        if ($call->created_at > $resetAt) return 0;
                        return $call->updated_at <= $resetAt && $call->cost !== null
                            ? (float) $call->cost : (float) $call->reserved;
                    }) : 0;
                    // Corrections must not grant more than a full trial after earlier budget resets.
                    if ($user->demo_limit !== null && $user->demo_allowance !== null) {
                        $updates['demo_limit'] = round(max(0, min((float) $user->demo_limit - $previousResetOffset, $spent + $reserved + (float) $user->demo_allowance)), 8);
                    }
                    DB::table('users')->where('id', $user->id)->update($updates);
                    // Keep every call and its cost/payload; only its funding classification changes.
                    DB::table('writer_ai_calls')->whereIn('id', $calls->pluck('id'))->update(['funding' => 'site']);
                });
            }
        });
    }

    public function down(): void
    {
        // Preserve corrected credits and usage records rather than re-charge users on rollback.
    }
};
