<?php

namespace App\Http\Controllers;

use App\Models\ForumDiscussion;
use App\Models\ForumPost;
use App\Models\ForumPostFlag;
use App\Models\ForumTag;
use App\Support\ForumHtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminForumController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin();
        $search = trim((string) $request->input('search'));

        $discussions = ForumDiscussion::query()
            ->with(['user', 'tag'])
            ->withCount('posts')
            ->when($search, fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->latest('last_post_at')
            ->paginate(20, ['*'], 'discussions_page')
            ->withQueryString();

        $posts = ForumPost::query()
            ->with(['user', 'discussion'])
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('body', 'like', "%{$search}%")
                    ->orWhereHas('discussion', fn ($discussion) => $discussion->where('title', 'like', "%{$search}%"));
            }))
            ->latest()
            ->paginate(25, ['*'], 'posts_page')
            ->withQueryString();

        $flags = ForumPostFlag::query()
            ->with(['user', 'post.user', 'post.discussion'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(25, ['*'], 'flags_page')
            ->withQueryString();

        return view('backend.forum.index', compact('discussions', 'posts', 'flags', 'search'));
    }

    public function editDiscussion(ForumDiscussion $discussion): View
    {
        $this->ensureAdmin();

        return view('backend.forum.edit-discussion', [
            'discussion' => $discussion,
            'tags' => ForumTag::orderBy('sort_order')->get(),
        ]);
    }

    public function updateDiscussion(Request $request, ForumDiscussion $discussion): RedirectResponse
    {
        $this->ensureAdmin();
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:4', 'max:180'],
            'forum_tag_id' => ['required', Rule::exists('forum_tags', 'id')],
            'is_pinned' => ['nullable', 'boolean'],
            'is_locked' => ['nullable', 'boolean'],
        ]);

        $discussion->update([
            'title' => $validated['title'],
            'forum_tag_id' => $validated['forum_tag_id'],
            'is_pinned' => $request->boolean('is_pinned'),
            'is_locked' => $request->boolean('is_locked'),
        ]);

        return redirect()->route('admin.forum.index')->with('success', 'Tartışma güncellendi.');
    }

    public function destroyDiscussion(ForumDiscussion $discussion): RedirectResponse
    {
        $this->ensureAdmin();
        $discussion->delete();

        return back()->with('success', 'Tartışma ve tüm iletileri silindi.');
    }

    public function editPost(ForumPost $post): View
    {
        $this->ensureAdmin();
        $post->load(['user', 'discussion']);

        return view('backend.forum.edit-post', compact('post'));
    }

    public function updatePost(Request $request, ForumPost $post, ForumHtmlSanitizer $sanitizer): RedirectResponse
    {
        $this->ensureAdmin();
        $validated = $request->validate(['body' => ['required', 'string', 'max:50000']]);
        $body = $sanitizer->clean($validated['body']);

        if (trim(strip_tags($body)) === '') {
            return back()->withErrors(['body' => 'İleti boş bırakılamaz.'])->withInput();
        }

        $post->update(['body' => $body, 'edited_at' => now()]);

        return redirect()->route('admin.forum.index', ['section' => 'posts'])->with('success', 'İleti güncellendi.');
    }

    public function destroyPost(ForumPost $post): RedirectResponse
    {
        $this->ensureAdmin();
        $discussion = $post->discussion;
        $post->delete();
        $discussion->update(['last_post_at' => $discussion->posts()->max('created_at') ?? $discussion->created_at]);

        return back()->with('success', 'İleti silindi.');
    }

    public function updateFlag(Request $request, ForumPostFlag $flag): RedirectResponse
    {
        $this->ensureAdmin();
        $validated = $request->validate(['status' => ['required', Rule::in(['pending', 'resolved', 'dismissed'])]]);
        $flag->update($validated);

        return back()->with('success', 'Bildirim durumu güncellendi.');
    }

    private function ensureAdmin(): void
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
