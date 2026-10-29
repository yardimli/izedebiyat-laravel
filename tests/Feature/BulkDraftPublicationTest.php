<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Writer\Models\Book;
use App\Writer\Services\Manuscript;
use Tests\WriterTestCase;

class BulkDraftPublicationTest extends WriterTestCase
{
    private function draft(User $user, array $attributes = []): Book
    {
        $parent = Category::create(['category_name' => 'Poetry', 'slug' => 'poetry', 'parent_category_id' => 0]);
        $category = Category::create(['category_name' => 'Poems', 'slug' => 'poems', 'parent_category_id' => $parent->id]);
        return Book::create(array_replace(['user_id' => $user->id, 'title' => 'Draft', 'category_id' => $category->id,
            'document' => Manuscript::fromText('A complete poem.'), 'revision' => 1], $attributes));
    }

    public function test_confirmation_and_ownership_are_required_before_any_publication(): void
    {
        $user = User::factory()->create();
        $own = $this->draft($user);
        $other = $this->draft(User::factory()->create());
        $url = route('writer.books.publish-drafts');
        $this->postJson($url)->assertUnauthorized();
        $this->actingAs($user)->postJson($url, ['drafts' => [['id' => $own->id, 'revision' => 1]]])->assertUnprocessable();
        $this->postJson($url, ['confirmed' => true, 'drafts' => [['id' => $own->id, 'revision' => 1], ['id' => $other->id, 'revision' => 1]]])->assertNotFound();
        $this->assertFalse($own->fresh()->is_published);
        $this->assertFalse($other->fresh()->is_published);
    }

    public function test_publishes_listed_eligible_drafts_and_reports_every_skipped_work(): void
    {
        $user = User::factory()->create();
        $ready = $this->draft($user);
        $missingCategory = $this->draft($user, ['category_id' => null]);
        $empty = $this->draft($user, ['document' => Manuscript::fromText('')]);
        $unapproved = $this->draft($user, ['approved' => 0]);
        $changed = $this->draft($user, ['revision' => 2]);
        $unlisted = $this->draft($user);
        $drafts = collect([$ready, $missingCategory, $empty, $unapproved, $changed])->map(fn ($b) => ['id' => $b->id, 'revision' => 1])->all();
        $this->actingAs($user)->postJson(route('writer.books.publish-drafts'), ['confirmed' => true, 'drafts' => $drafts])
            ->assertOk()->assertJsonPath('published', 1)->assertJsonCount(4, 'skipped');
        $this->assertTrue($ready->fresh()->is_published);
        $this->assertSame(2, $ready->fresh()->revision);
        $this->assertSame(1, $ready->revisions()->count());
        foreach ([$missingCategory, $empty, $unapproved, $changed, $unlisted] as $draft) $this->assertFalse($draft->fresh()->is_published);
        $this->postJson(route('writer.books.publish-drafts'), ['confirmed' => true, 'drafts' => $drafts])
            ->assertOk()->assertJsonPath('published', 0)->assertJsonCount(5, 'skipped');
        $this->assertSame(1, $ready->revisions()->count());
    }

    public function test_dialog_contains_count_confirmation_and_standard_labels(): void
    {
        $user = User::factory()->create();
        $this->draft($user);
        $this->actingAs($user)->withSession(['locale' => 'tr_TR'])->get(route('articles.index'))->assertOk()
            ->assertSee('Tüm Taslakları Yayımla (1)')->assertSee('1 taslak yayımlansın mı?')
            ->assertSee('İptal')->assertSee('Oluştur')->assertSee('Düzenle')->assertDontSee('Masama dön');
    }
}
