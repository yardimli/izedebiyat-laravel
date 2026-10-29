<?php

namespace Tests\Feature;

use App\Models\AccountRecoveryRequest;
use App\Models\User;
use App\Notifications\AccountRecovered;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Tests\WriterTestCase;

class AccountRecoveryAdminTest extends WriterTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (glob(database_path('migrations/*account_recovery*')) as $migration) {
            (require $migration)->up();
        }
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        Notification::fake();
        $this->actingAs(User::factory()->create(['member_type' => 1]));
    }

    private function recovery(bool $delete = false): AccountRecoveryRequest
    {
        return AccountRecoveryRequest::create([
            'real_name' => 'Test Writer', 'remembered_emails' => 'old@example.com',
            'contact_email' => 'contact@example.com', 'profile_url' => 'https://www.izedebiyat.com/yazar/test_writer',
            'id_document_path' => 'test.jpg', 'delete_account' => $delete, 'status' => 'pending',
        ]);
    }

    private function preview(AccountRecoveryRequest $recovery, User $user): array
    {
        $data = ['user_id' => $user->id, 'new_email' => 'contact@example.com'];
        $this->post(route('admin.account-recovery.approve', $recovery), $data)
            ->assertRedirect(route('admin.account-recovery.confirm', $recovery));
        $this->get(route('admin.account-recovery.confirm', $recovery))->assertOk()
            ->assertSee($user->email)->assertSee($user->slug)->assertSee(route('user', $user->slug));
        return $data + ['confirmation_token' => session('account_recovery_confirmation.'.$recovery->id.'.token'), 'confirmed' => '1'];
    }

    public function test_profile_lookup_is_exact_and_works_for_both_request_types(): void
    {
        $user = User::factory()->create(['slug' => 'test_writer']);
        User::factory()->create(['slug' => 'test_writer_extra']);
        foreach ([false, true] as $delete) {
            $recovery = $this->recovery($delete);
            foreach (['https://www.izedebiyat.com/yazar/test_writer/?ref=test#bio', '/yazar/test_writer', 'test_writer'] as $search) {
                $this->get(route('admin.account-recovery.show', ['id' => $recovery->id, 'search' => $search]))
                    ->assertOk()->assertViewHas('possible_users', fn ($users) => $users->pluck('id')->all() === [$user->id]);
            }
            $this->get(route('admin.account-recovery.show', ['id' => $recovery->id, 'search' => 'https://elsewhere.test/yazar/test_writer']))
                ->assertOk()->assertViewHas('possible_users', fn ($users) => $users->isEmpty());
        }
    }

    public function test_review_does_not_mutate_and_final_email_is_editable_and_receives_a_valid_reset_link(): void
    {
        $recovery = $this->recovery();
        $user = User::factory()->create(['slug' => 'test_writer']);
        $oldEmail = $user->email;
        $oldPassword = $user->password;
        $data = $this->preview($recovery, $user);
        $this->assertSame($oldEmail, $user->fresh()->email);
        Notification::assertNothingSent();
        $data['new_email'] = 'edited@example.com';
        $this->post(route('admin.account-recovery.approve', $recovery), $data)->assertRedirect(route('admin.account-recovery.index'));
        $this->assertSame('edited@example.com', $user->fresh()->email);
        $this->assertSame($oldPassword, $user->fresh()->password);
        $this->assertSame('approved', $recovery->fresh()->status);
        Notification::assertSentTo($user, AccountRecovered::class, function ($notification, $channels, $recipient) {
            $this->assertSame('edited@example.com', $recipient->email);
            $this->assertTrue(Password::broker()->tokenExists($recipient, $notification->token));
            $this->assertStringContainsString('email=edited%40example.com', $notification->toMail($recipient)->actionUrl);
            return true;
        });
        $this->post(route('admin.account-recovery.approve', $recovery), $data)->assertStatus(409);
    }

    public function test_duplicate_email_and_stale_account_cannot_be_approved(): void
    {
        $recovery = $this->recovery();
        $user = User::factory()->create(['slug' => 'test_writer']);
        $other = User::factory()->create();
        $data = $this->preview($recovery, $user);
        $this->post(route('admin.account-recovery.approve', $recovery), array_replace($data, ['new_email' => $other->email]))
            ->assertSessionHasErrors('new_email');
        $user->update(['slug' => 'changed']);
        $this->post(route('admin.account-recovery.approve', $recovery), $data)->assertStatus(409);
        $this->assertSame('pending', $recovery->fresh()->status);
        Notification::assertNothingSent();
    }

    public function test_deletion_requires_review_and_deletes_only_the_selected_account(): void
    {
        $recovery = $this->recovery(true);
        $user = User::factory()->create(['slug' => 'test_writer']);
        $other = User::factory()->create();
        $article = \App\Models\Article::create(['user_id' => $user->id, 'title' => 'Test', 'slug' => 'test']);
        $data = $this->preview($recovery, $user);
        $this->assertNotNull($user->fresh());
        $this->post(route('admin.account-recovery.approve', $recovery), array_replace($data, ['user_id' => $other->id]))->assertStatus(409);
        $this->post(route('admin.account-recovery.approve', $recovery), $data)->assertRedirect(route('admin.account-recovery.index'));
        $this->assertNull($user->fresh());
        $this->assertNull($article->fresh());
        $this->assertNotNull($other->fresh());
        $this->assertSame('approved', $recovery->fresh()->status);
        $this->assertTrue((bool) $recovery->fresh()->delete_account);
        Notification::assertNothingSent();
    }

    public function test_non_admin_cannot_search_review_or_approve(): void
    {
        $recovery = $this->recovery();
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.account-recovery.show', $recovery))->assertForbidden();
        $this->get(route('admin.account-recovery.confirm', $recovery))->assertForbidden();
        $this->post(route('admin.account-recovery.approve', $recovery))->assertForbidden();
    }

    public function test_notification_failure_rolls_back_email_and_request_status(): void
    {
        $recovery = $this->recovery();
        $user = User::factory()->create(['slug' => 'test_writer']);
        $data = $this->preview($recovery, $user);
        Notification::shouldReceive('send')->once()->andThrow(new \RuntimeException('Mail unavailable'));
        $this->post(route('admin.account-recovery.approve', $recovery), $data)->assertSessionHas('error');
        $this->assertSame($user->email, $user->fresh()->email);
        $this->assertSame('pending', $recovery->fresh()->status);
    }
}
