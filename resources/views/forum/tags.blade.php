@extends('layouts.app-frontend')

@section('title', 'Forum Etiketleri - İzEdebiyat')
@section('body-class', 'forum-page')
@push('styles')@vite('resources/js/forum.js')@endpush

@section('content')
<div class="forum-shell">
    <div class="forum-container">
        @include('forum._nav')
        <div class="forum-tags-header"><span>Konulara göz atın</span><h1>Forum Etiketleri</h1></div>
        <div class="forum-tag-grid">
            @foreach($tags as $tag)
                <a href="{{ route('forum.tag', $tag) }}" class="forum-tag-card" style="--tag-color: {{ $tag->color }}">
                    <h2>{{ $tag->name }}</h2>
                    <p>{{ $tag->description }}</p>
                    <div>
                        <span>{{ $tag->discussions_count }} tartışma</span>
                        @if($tag->discussions->first())<small>{{ Str::limit($tag->discussions->first()->title, 48) }}</small>@endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
