<?php

namespace App\Http\Controllers;

use App\Models\ForumDiscussion;
use App\Models\ForumPost;
use App\Models\ForumPostFlag;
use App\Models\ForumPostLike;
use App\Models\ForumTag;
use App\Models\User;
use App\Support\ForumHtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ForumController extends Controller
{
    public function index(Request $request, ?ForumTag $tag = null): View
    {
        $sort = $request->string('sort')->toString();
        if (! in_array($sort, ['latest', 'top', 'newest', 'oldest'], true)) {
            $sort = 'latest';
        }

        $query = ForumDiscussion::query()
            ->with(['user', 'tag', 'latestPost.user'])
            ->withCount('posts');

        if ($tag) {
            abort_unless($tag->is_active, 404);
            $query->whereBelongsTo($tag, 'tag');
        }

        match ($sort) {
            'top' => $query->orderByDesc('posts_count')->orderByDesc('views_count'),
            'newest' => $query->latest(),
            'oldest' => $query->oldest(),
            default => $query->orderByDesc('is_pinned')->orderByDesc('last_post_at'),
        };

        return view('forum.index', [
            'discussions' => $query->paginate(15)->withQueryString(),
            'tags' => ForumTag::where('is_active', true)->orderBy('sort_order')->get(),
            'activeTag' => $tag,
            'sort' => $sort,
        ]);
    }

    public function tags(): View
    {
        $tags = ForumTag::query()
            ->where('is_active', true)
            ->withCount('discussions')
            ->with(['discussions' => fn ($query) => $query->latest('last_post_at')->limit(1)])
            ->orderBy('sort_order')
            ->get();

        return view('forum.tags', compact('tags'));
    }

    public function create(): View
    {
        return view('forum.create', [
            'tags' => ForumTag::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request, ForumHtmlSanitizer $sanitizer): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:4', 'max:180'],
            'forum_tag_id' => ['required', Rule::exists('forum_tags', 'id')->where('is_active', true)],
            'body' => ['required', 'string', 'max:50000'],
        ]);
        $body = $this->validatedBody($validated['body'], $sanitizer);

        $discussion = DB::transaction(function () use ($validated, $body, $request) {
            $baseSlug = Str::slug($validated['title']) ?: 'tartisma';
            $slug = $baseSlug;
            $counter = 2;
            while (ForumDiscussion::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            $discussion = ForumDiscussion::create([
                'user_id' => $request->user()->id,
                'forum_tag_id' => $validated['forum_tag_id'],
                'title' => $validated['title'],
                'slug' => $slug,
                'last_post_at' => now(),
            ]);
            $discussion->posts()->create(['user_id' => $request->user()->id, 'body' => $body]);

            return $discussion;
        });

        return redirect()->route('forum.show', $discussion)->with('success', 'Tartışmanız yayımlandı.');
    }

    public function show(Request $request, ForumDiscussion $discussion): View
    {
        $discussion->increment('views_count');
        $discussion->load(['tag', 'user']);
        $posts = $discussion->posts()->with(['user', 'parent.user'])->withCount('likes')->oldest()->paginate(20);

        $likedPostIds = $request->user()
            ? ForumPostLike::where('user_id', $request->user()->id)
                ->whereIn('forum_post_id', $posts->getCollection()->pluck('id'))
                ->pluck('forum_post_id')->all()
            : [];

        return view('forum.show', compact('discussion', 'posts', 'likedPostIds'));
    }

    public function reply(Request $request, ForumDiscussion $discussion, ForumHtmlSanitizer $sanitizer): RedirectResponse
    {
        abort_if($discussion->is_locked && ! $request->user()->isAdmin(), 403, 'Bu tartışma yanıtlara kapalı.');
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:50000'],
            'parent_id' => ['nullable', Rule::exists('forum_posts', 'id')->where('forum_discussion_id', $discussion->id)],
        ]);

        $post = $discussion->posts()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => $this->validatedBody($validated['body'], $sanitizer),
        ]);
        $discussion->update(['last_post_at' => $post->created_at]);
        $page = (int) ceil($discussion->posts()->count() / 20);

        return redirect()->route('forum.show', [$discussion, 'page' => max(1, $page)])
            ->withFragment('post-' . $post->id)
            ->with('success', 'Yanıtınız yayımlandı.');
    }

    public function like(Request $request, ForumPost $post): RedirectResponse
    {
        $like = ForumPostLike::where('forum_post_id', $post->id)->where('user_id', $request->user()->id)->first();
        $like ? $like->delete() : ForumPostLike::create(['forum_post_id' => $post->id, 'user_id' => $request->user()->id]);

        return back()->withFragment('post-' . $post->id);
    }

    public function flag(Request $request, ForumPost $post): RedirectResponse
    {
        $validated = $request->validate(['reason' => ['required', 'string', 'min:5', 'max:500']]);
        ForumPostFlag::updateOrCreate(
            ['forum_post_id' => $post->id, 'user_id' => $request->user()->id],
            ['reason' => $validated['reason'], 'status' => 'pending']
        );

        return back()->with('success', 'Bildiriminiz inceleme için gönderildi.')->withFragment('post-' . $post->id);
    }

    public function profile(User $user): View
    {
        $posts = ForumPost::whereBelongsTo($user)->with(['discussion.tag'])->latest()->paginate(15);

        return view('forum.profile', [
            'forumUser' => $user,
            'posts' => $posts,
            'discussionCount' => ForumDiscussion::whereBelongsTo($user)->count(),
            'likeCount' => ForumPostLike::whereIn('forum_post_id', ForumPost::whereBelongsTo($user)->select('id'))->count(),
        ]);
    }

    private function validatedBody(string $body, ForumHtmlSanitizer $sanitizer): string
    {
        $body = $sanitizer->clean($body);
        $plainText = trim(html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />'], ' ', $body))));
        abort_if($plainText === '', 422, 'İleti metni boş bırakılamaz.');

        return $body;
    }
}
