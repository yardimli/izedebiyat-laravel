<?php

namespace Tests\Feature;

use App\Models\User;
use App\Writer\Models\AiCall;
use App\Writer\Services\DemoBudget;
use App\Writer\Services\OpenRouter;
use Tests\WriterTestCase;

class TrialUsageCorrectionTest extends WriterTestCase
{
    public function test_old_generation_costs_are_excluded_without_deleting_records(): void
    {
        $user = User::factory()->create(['demo_limit' => 1, 'demo_allowance' => 1, 'demo_spent' => .5, 'demo_reserved' => .2]);
        $base = ['user_id' => $user->id, 'model' => 'test', 'funding' => 'demo'];
        $chat = AiCall::create($base + ['stage' => 'execution', 'cost' => .3, 'status' => 'settled', 'reserved' => .3]);
        $category = AiCall::create($base + ['stage' => 'publication-category', 'cost' => .2, 'status' => 'settled', 'reserved' => .2, 'request_payload' => ['messages' => ['original']]]);
        $summary = AiCall::create($base + ['stage' => 'publication-synopsis', 'status' => 'pending', 'reserved' => .2]);
        $migration = require database_path('migrations/2026_11_08_000002_exclude_generation_calls_from_trial_usage.php');
        $migration->up();
        $migration->up();
        $this->assertSame(3, AiCall::count());
        $this->assertEquals(.3, $user->fresh()->demo_spent);
        $this->assertEquals(0, $user->fresh()->demo_reserved);
        $this->assertEquals(.7, DemoBudget::remaining($user->fresh()));
        $this->assertSame('demo', $chat->fresh()->funding);
        $this->assertSame('site', $category->fresh()->funding);
        $this->assertEquals(.2, $category->fresh()->cost);
        $this->assertSame(['messages' => ['original']], $category->fresh()->request_payload);
        app(OpenRouter::class)->settle($summary->fresh(), .15);
        $this->assertEquals(.3, $user->fresh()->demo_spent);
        $this->assertEquals(.15, $summary->fresh()->cost);
    }

    public function test_costs_already_covered_by_a_reset_do_not_refund_later_chat_usage(): void
    {
        $user = User::factory()->create(['demo_limit' => 1, 'demo_allowance' => 1, 'demo_spent' => .2]);
        AiCall::create(['user_id' => $user->id, 'model' => 'test', 'funding' => 'demo', 'stage' => 'publication-keywords', 'cost' => .2, 'reserved' => .2, 'status' => 'settled']);
        $this->travel(1)->minutes();
        DemoBudget::reset($user, $user);
        $user->refresh()->forceFill(['demo_spent' => .5])->save();
        $migration = require database_path('migrations/2026_11_08_000002_exclude_generation_calls_from_trial_usage.php');
        $migration->up();
        $this->assertEquals(.3, $user->fresh()->demo_spent);
        $this->assertEquals(1, $user->fresh()->demo_limit);
        $this->assertEquals(.7, DemoBudget::remaining($user->fresh()));
        $this->travelBack();
    }
}
