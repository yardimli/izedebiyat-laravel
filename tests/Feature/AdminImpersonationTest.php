<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\WriterTestCase;

class AdminImpersonationTest extends WriterTestCase
{
    public function test_admin_can_view_an_account_and_return_using_only_the_saved_identity(): void
    {
        $admin = User::factory()->create(['member_type' => 1]);
        $member = User::factory()->create();
        $otherAdmin = User::factory()->create(['member_type' => 1]);
        $this->actingAs($admin)->post('/login-as', ['user_id' => $member->id])
            ->assertRedirect('/eserlerim')->assertSessionHas('admin_impersonation.admin_id', $admin->id);
        $this->assertAuthenticatedAs($member);
        $this->get('/eserlerim')->assertOk()->assertSee('Yönetici olarak görüntülüyorsunuz:')
            ->assertSee($member->email)->assertSee('action="'.route('users-stop-impersonating').'"', false);
        $this->get('/yazi-atolyesi/hesap')->assertOk()->assertSee('action="'.route('users-stop-impersonating').'"', false);
        $this->get('/admin/kullanicilar')->assertForbidden();
        $this->post('/yoneticiye-don', ['admin_id' => $otherAdmin->id])->assertRedirect('/admin/kullanicilar')
            ->assertSessionMissing('admin_impersonation');
        $this->assertAuthenticatedAs($admin);
        $this->get('/eserlerim')->assertOk()->assertDontSee('action="'.route('users-stop-impersonating').'"', false);
    }

    public function test_non_admins_cannot_start_or_forge_a_return_and_nested_switches_are_rejected(): void
    {
        $member = User::factory()->create();
        $admin = User::factory()->create(['member_type' => 1]);
        $this->actingAs($member)->post('/login-as', ['user_id' => $admin->id])->assertForbidden();
        $this->post('/yoneticiye-don', ['admin_id' => $admin->id])->assertForbidden();
        $this->assertAuthenticatedAs($member);
        $secondAdmin = User::factory()->create(['member_type' => 1]);
        $this->actingAs($admin)->post('/login-as', ['user_id' => $secondAdmin->id])->assertRedirect();
        $this->post('/login-as', ['user_id' => $member->id])->assertStatus(409);
        $this->post('/yoneticiye-don')->assertRedirect();
        $this->assertAuthenticatedAs($admin);
    }

    public function test_return_fails_if_the_original_admin_has_lost_admin_access(): void
    {
        $admin = User::factory()->create(['member_type' => 1]);
        $member = User::factory()->create();
        $this->actingAs($admin)->post('/login-as', ['user_id' => $member->id])->assertRedirect();
        $admin->update(['member_type' => 0]);
        $this->post('/yoneticiye-don')->assertForbidden();
        $this->assertAuthenticatedAs($member);
    }

    public function test_invalid_target_leaves_the_admin_session_unchanged(): void
    {
        $admin = User::factory()->create(['member_type' => 1]);
        $this->actingAs($admin)->postJson('/login-as', ['user_id' => 999999])->assertUnprocessable()
            ->assertSessionMissing('admin_impersonation');
        $this->assertAuthenticatedAs($admin);
        $this->post('/login-as', ['user_id' => $admin->id])->assertRedirect('/admin/kullanicilar')
            ->assertSessionMissing('admin_impersonation');
    }
}
