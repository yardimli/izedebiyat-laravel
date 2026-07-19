@extends('layouts.app-frontend')

@section('title', ($activeTag ? $activeTag->name . ' - ' : '') . 'Forum - İzEdebiyat')
@section('body-class', 'forum-page')

@push('styles')
    @vite('resources/js/forum.js')
@endpush

@section('content')
<div class="forum-shell">
    @if($activeTag)
        <header class="forum-tag-hero" style="--tag-color: {{ $activeTag->color }}">
            <span>{{ $activeTag->name }}</span>
            @if($activeTag->description)<small>{{ $activeTag->description }}</small>@endif
        </header>
    @endif

    <div class="forum-container forum-list-layout">
        <aside class="forum-sidebar">
            @include('forum._nav')
            <nav class="forum-tag-list" aria-label="Forum etiketleri">
                @foreach($tags as $tag)
                    <a href="{{ route('forum.tag', $tag) }}" class="{{ optional($activeTag)->is($tag) ? 'active' : '' }}">
                        <span class="forum-tag-dot" style="--tag-color: {{ $tag->color }}"></span>{{ $tag->name }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <section class="forum-discussion-panel">
            <div class="forum-list-toolbar">
                <div class="dropdown">
                    @php $sortNames = ['latest' => 'Son Etkinlik', 'top' => 'Popüler', 'newest' => 'En Yeni', 'oldest' => 'En Eski']; @endphp
                    <button class="forum-sort-button dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ $sortNames[$sort] }}
                    </button>
                    <ul class="dropdown-menu forum-sort-menu">
                        @foreach($sortNames as $key => $name)
                            <li><a class="dropdown-item {{ $sort === $key ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => $key, 'page' => null]) }}">{{ $name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <a href="{{ request()->fullUrl() }}" class="forum-icon-button" title="Yenile"><i class="bi bi-arrow-clockwise"></i></a>
            </div>

            @if(session('success'))<div class="forum-alert">{{ session('success') }}</div>@endif

            <div class="forum-discussion-list">
                @forelse($discussions as $discussion)
                    @php $activityUser = $discussion->latestPost?->user ?? $discussion->user; @endphp
                    <article class="forum-discussion-row">
                        <a href="{{ route('forum.profile', $activityUser) }}" aria-label="{{ $activityUser->name }} profili">
                            @include('forum._avatar', ['user' => $activityUser])
                        </a>
                        <div class="forum-discussion-summary">
                            <a class="forum-discussion-title" href="{{ route('forum.show', $discussion) }}">
                                @if($discussion->is_pinned)<i class="bi bi-pin-angle-fill" title="Sabitlendi"></i>@endif
                                {{ $discussion->title }}
                            </a>
                            <div class="forum-discussion-meta">
                                @if($discussion->latestPost)
                                    <i class="bi bi-reply-fill"></i> {{ $activityUser->name }} yanıtladı
                                @else
                                    {{ $discussion->user->name }} başlattı
                                @endif
                                <time datetime="{{ optional($discussion->last_post_at)->toIso8601String() }}">{{ optional($discussion->last_post_at)->diffForHumans() }}</time>
                            </div>
                        </div>
                        <div class="forum-discussion-stats">
                            <a class="forum-tag-pill" style="--tag-color: {{ $discussion->tag->color }}" href="{{ route('forum.tag', $discussion->tag) }}">{{ $discussion->tag->name }}</a>
                            <span title="İleti sayısı"><i class="bi bi-chat-fill"></i> {{ number_format($discussion->posts_count) }}</span>
                        </div>
                    </article>
                @empty
                    <div class="forum-empty">
                        <i class="bi bi-chat-square-text"></i>
                        <h2>Henüz tartışma yok</h2>
                        <p>Bu alandaki ilk sohbeti siz başlatabilirsiniz.</p>
                        <a href="{{ route('forum.create') }}" class="forum-button forum-button-primary">Tartışma Başlat</a>
                    </div>
                @endforelse
            </div>
            <div class="forum-pagination">{{ $discussions->links() }}</div>
        </section>
    </div>
</div>
@endsection
