@extends('layouts.app-frontend')

@section('title', $discussion->title . ' - Forum')
@section('body-class', 'forum-page')

@push('styles')
    @vite('resources/js/forum.js')
@endpush

@section('content')
<div class="forum-shell">
    <header class="forum-discussion-hero" style="--tag-color: {{ $discussion->tag->color }}">
        <a href="{{ route('forum.tag', $discussion->tag) }}" class="forum-tag-pill">{{ $discussion->tag->name }}</a>
        <h1>{{ $discussion->title }}</h1>
        <div>{{ number_format($discussion->views_count) }} görüntülenme · {{ $discussion->posts->count() }} ileti</div>
    </header>

    <div class="forum-container forum-thread-container">
        <div class="forum-thread-topbar">
            <a href="{{ route('forum.index') }}"><i class="bi bi-arrow-left"></i> Tüm tartışmalar</a>
            @auth
                @unless($discussion->is_locked)
                    <button class="forum-button forum-button-primary" type="button" data-reply-open><i class="bi bi-reply-fill"></i> Yanıtla</button>
                @endunless
            @else
                <a class="forum-button forum-button-primary" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right"></i> Yanıtlamak için giriş yapın</a>
            @endauth
        </div>

        @if(session('success'))<div class="forum-alert">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="forum-errors">{{ $errors->first() }}</div>@endif

        <section class="forum-post-list">
            @foreach($posts as $post)
                <article class="forum-post" id="post-{{ $post->id }}">
                    <aside class="forum-post-author">
                        <a href="{{ route('forum.profile', $post->user) }}">@include('forum._avatar', ['user' => $post->user, 'class' => 'forum-avatar-large'])</a>
                    </aside>
                    <div class="forum-post-main">
                        <header>
                            <a href="{{ route('forum.profile', $post->user) }}" class="forum-author-name">{{ $post->user->name }}</a>
                            <time datetime="{{ $post->created_at->toIso8601String() }}">{{ $post->created_at->diffForHumans() }}</time>
                            <a class="forum-post-number" href="#post-{{ $post->id }}">#{{ ($posts->firstItem() ?? 1) + $loop->index }}</a>
                        </header>
                        @if($post->parent)
                            <a class="forum-reply-context" href="#post-{{ $post->parent_id }}"><i class="bi bi-reply-fill"></i> {{ $post->parent->user->name }} kullanıcısına yanıt</a>
                        @endif
                        <div class="forum-post-body">{!! $post->body !!}</div>
                        <footer class="forum-post-actions">
                            @if($post->likes_count)
                                <span class="forum-like-summary"><i class="bi bi-hand-thumbs-up"></i> {{ $post->likes_count }}</span>
                            @endif
                            <div>
                                @auth
                                    @unless($discussion->is_locked)
                                        <button type="button" class="forum-text-button" data-reply-to="{{ $post->id }}" data-reply-name="{{ $post->user->name }}">Yanıtla</button>
                                    @endunless
                                    <form action="{{ route('forum.like', $post) }}" method="post" class="forum-inline-form">@csrf
                                        <button type="submit" class="forum-text-button {{ in_array($post->id, $likedPostIds) ? 'active' : '' }}">{{ in_array($post->id, $likedPostIds) ? 'Beğenmekten vazgeç' : 'Beğen' }}</button>
                                    </form>
                                    <details class="forum-flag-menu">
                                        <summary aria-label="Diğer işlemler"><i class="bi bi-three-dots"></i></summary>
                                        <form action="{{ route('forum.flag', $post) }}" method="post">@csrf
                                            <label><i class="bi bi-flag-fill"></i> İletiyi bildir</label>
                                            <textarea name="reason" rows="3" maxlength="500" placeholder="Bildirim nedeniniz" required></textarea>
                                            <button type="submit">Gönder</button>
                                        </form>
                                    </details>
                                @endauth
                            </div>
                        </footer>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="forum-pagination">{{ $posts->links() }}</div>

        @auth
            @unless($discussion->is_locked)
                <section class="forum-composer-card forum-reply-composer {{ $errors->any() ? 'is-open' : '' }}" data-reply-composer>
                    <form method="post" action="{{ route('forum.reply', $discussion) }}" data-quill-form>
                        @csrf
                        <input type="hidden" name="parent_id" data-parent-input value="{{ old('parent_id') }}">
                        <div class="forum-composer-heading forum-reply-heading">
                            @include('forum._avatar', ['user' => auth()->user(), 'class' => 'forum-avatar-large'])
                            <div><small data-reply-context>Yanıt yazıyorsunuz</small><h2>{{ $discussion->title }}</h2></div>
                            <button type="button" class="forum-close-composer" data-reply-close aria-label="Kapat"><i class="bi bi-x-lg"></i></button>
                        </div>
                        <div class="forum-editor" data-quill-editor data-placeholder="Yanıtınızı yazın...">{!! old('body') !!}</div>
                        <input type="hidden" name="body" data-quill-input value="{{ old('body') }}">
                        <div class="forum-composer-footer">
                            <button class="forum-button forum-button-primary" type="submit"><i class="bi bi-send-fill"></i> Yanıtı Yayımla</button>
                        </div>
                    </form>
                </section>
            @else
                <div class="forum-locked"><i class="bi bi-lock-fill"></i> Bu tartışma yeni yanıtlara kapalı.</div>
            @endunless
        @endauth
    </div>
</div>
@endsection
