@extends('layouts.app-frontend')

@section('title', $forumUser->name . ' - Forum Profili')
@section('body-class', 'forum-page')
@push('styles')@vite('resources/js/forum.js')@endpush

@section('content')
<div class="forum-shell">
    <header class="forum-profile-hero">
        <div class="forum-container">
            @include('forum._avatar', ['user' => $forumUser, 'class' => 'forum-profile-avatar'])
            <div><h1>{{ $forumUser->name }}</h1><span>{{ $forumUser->created_at->format('F Y') }} tarihinde katıldı</span></div>
        </div>
    </header>
    <div class="forum-container forum-profile-layout">
        <aside class="forum-profile-stats">
            <a href="{{ route('forum.index') }}"><i class="bi bi-chat-dots"></i><span>İletiler</span><strong>{{ $posts->total() }}</strong></a>
            <span><i class="bi bi-list-ul"></i><span>Tartışmalar</span><strong>{{ $discussionCount }}</strong></span>
            <span><i class="bi bi-hand-thumbs-up"></i><span>Beğeniler</span><strong>{{ $likeCount }}</strong></span>
        </aside>
        <section class="forum-profile-feed">
            <h2>Son forum iletileri</h2>
            @forelse($posts as $post)
                <article>
                    <div class="forum-profile-post-heading">
                        @include('forum._avatar', ['user' => $forumUser])
                        <div><a href="{{ route('forum.show', $post->discussion) }}#post-{{ $post->id }}">{{ $post->discussion->title }}</a><time>{{ $post->created_at->diffForHumans() }}</time></div>
                        <span class="forum-tag-pill" style="--tag-color: {{ $post->discussion->tag->color }}">{{ $post->discussion->tag->name }}</span>
                    </div>
                    <div class="forum-post-body forum-post-excerpt">{!! $post->body !!}</div>
                </article>
            @empty
                <div class="forum-empty"><p>Bu üyenin henüz forum iletisi yok.</p></div>
            @endforelse
            <div class="forum-pagination">{{ $posts->links() }}</div>
        </section>
    </div>
</div>
@endsection
