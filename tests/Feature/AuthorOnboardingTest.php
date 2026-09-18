<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Writer\Models\Book;
use App\Writer\Models\AiCall;
use App\Writer\Services\Manuscript;
use App\Writer\Services\OpenRouter;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use Tests\WriterTestCase;

class AuthorOnboardingTest extends WriterTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::table('users', function (Blueprint $table) {
            foreach (['username', 'google_id', 'picture', 'page_title', 'about_me', 'personal_url', 'last_ip', 'background_image'] as $field) {
                $table->string($field)->nullable();
            }
            $table->integer('member_status')->default(1);
        });
    }

    private function book(User $user, array $attributes = []): Book
    {
        return Book::create($attributes + ['user_id' => $user->id, 'title' => 'A waiting story', 'document' => Manuscript::fromText('Words for the reader.')]);
    }

    public function test_profile_can_save_empty_optional_fields_for_new_and_existing_authors(): void
    {
        foreach (['', 'An earlier biography'] as $biography) {
            $user = User::factory()->create(['username' => 'author'.User::count(), 'about_me' => $biography]);
            $this->actingAs($user)->from('/yazi-atolyesi/hesap')->post(route('backend.update'), [
                'name' => $user->name, 'username' => $user->username, 'email' => $user->email,
                'page_title' => '', 'about_me' => '', 'personal_url' => '',
            ])->assertSessionHasNoErrors()->assertRedirect('/yazi-atolyesi/hesap');
            $this->assertSame('', $user->fresh()->about_me);
            $this->assertSame('', $user->fresh()->page_title);
        }
    }

    public function test_google_signup_creates_account_without_a_shared_password_and_reuses_existing_email(): void
    {
        $google = (new GoogleUser)->setRaw(['email_verified' => true])->map([
            'id' => 'google-123', 'name' => 'New Author', 'email' => 'author@example.com', 'avatar' => null,
        ]);
        Socialite::shouldReceive('driver->user')->twice()->andReturn($google);
        $this->get('/login/google/callback')->assertRedirect('/eserlerim');
        $user = User::where('email', 'author@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->email_verified_at);
        $this->assertFalse(Hash::check('123456_gecici_sifre', $user->password));
        auth()->logout();
        $user->update(['google_id' => null]);
        $this->get('/login/google/callback')->assertRedirect('/eserlerim');
        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, User::count());
    }

    public function test_google_does_not_link_an_unverified_email(): void
    {
        $user = User::factory()->create();
        $google = (new GoogleUser)->setRaw(['email_verified' => false])->map(['id' => 'unverified', 'email' => $user->email]);
        Socialite::shouldReceive('driver->user')->once()->andReturn($google);
        $this->get('/login/google/callback')->assertRedirect(route('login'))->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertNull($user->fresh()->google_id);
    }

    public function test_new_story_return_shows_publication_question_once_and_suppresses_draft_reminder(): void
    {
        $user = User::factory()->create();
        $other = $this->book(User::factory()->create(), ['title' => 'Private work']);
        $this->actingAs($user)->post(route('writer.books.store'), ['title' => 'New story'])->assertRedirect();
        $book = Book::where('user_id', $user->id)->firstOrFail();
        $book->update(['document' => Manuscript::fromText('A new story begins.')]);
        $this->get(route('articles.edit', \App\Helpers\IdHasher::encode($book->id)))->assertOk();
        $this->get(route('articles.index'))->assertOk()->assertSee('id="publish-story-dialog"', false)->assertDontSee('id="draft-reminder-dialog"', false);
        $this->get(route('articles.index', ['q' => 'no matching works']))->assertOk()->assertSee('id="draft-reminder-dialog"', false)->assertSee('New story')->assertDontSee($other->title);
    }

    public function test_return_assigns_valid_ai_category_without_publishing_and_preserves_custom_image(): void
    {
        $user = User::factory()->create();
        $user->selected_model = 'test-model';
        $user->save();
        $parent = Category::create(['category_name' => 'Fiction', 'slug' => 'fiction']);
        $category = Category::create(['category_name' => 'Story', 'slug' => 'story', 'parent_category_id' => $parent->id, 'picture' => 'story.jpg']);
        $book = $this->book($user, ['featured_image' => 'existing.jpg']);
        $router = $this->mock(OpenRouter::class);
        $router->shouldReceive('model')->once()->with('test-model')->andReturn(['id' => 'test-model']);
        $router->shouldReceive('reserve')->once()->andReturn(new AiCall);
        $router->shouldReceive('send')->once()->andReturn(['category_id' => $category->id]);
        $this->actingAs($user)->postJson('/yazi-atolyesi/api/books/'.$book->id.'/prepare-return')->assertOk()->assertJson(['category_id' => $category->id, 'revision' => 2, 'is_published' => false]);
        $this->assertSame('existing.jpg', $book->fresh()->featured_image);
        $this->assertSame('Fiction', $book->fresh()->parent_category_name);
        // Revisiting uses the chosen category and does not charge for another AI call.
        $this->postJson('/yazi-atolyesi/api/books/'.$book->id.'/prepare-return')->assertOk()->assertJson(['revision' => 2]);
        $this->patchJson('/yazi-atolyesi/api/books/'.$book->id, ['revision' => 2, 'is_published' => true])->assertOk();
        $this->assertTrue($book->fresh()->is_published);
    }

    public function test_empty_story_and_another_authors_story_do_not_trigger_ai(): void
    {
        $user = User::factory()->create();
        $book = $this->book($user, ['document' => Manuscript::fromText('')]);
        $this->mock(OpenRouter::class)->shouldNotReceive('model');
        $this->actingAs($user)->postJson('/yazi-atolyesi/api/books/'.$book->id.'/prepare-return')->assertOk();
        $this->assertNull($book->fresh()->featured_image);
        $other = $this->book(User::factory()->create());
        $this->postJson('/yazi-atolyesi/api/books/'.$other->id.'/prepare-return')->assertNotFound();
    }
}
