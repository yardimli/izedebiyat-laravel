@extends('layouts.app')
@section('title', 'İletiyi Düzenle - Forum Yönetimi')
@push('styles')@vite('resources/js/forum.js')@endpush
@section('content')
<main><div class="container mt-5" style="max-width: 960px; min-height: 88vh;">
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h4>İletiyi Düzenle</h4><div class="text-muted small">{{ $post->discussion->title }} · {{ $post->user->name }}</div></div><a href="{{ route('admin.forum.index', ['section' => 'posts']) }}" class="btn btn-outline-secondary">Geri</a></div>
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <div class="forum-composer-card"><form method="post" action="{{ route('admin.forum.posts.update', $post) }}" data-quill-form>@csrf @method('PUT')
        <div class="forum-editor" data-quill-editor data-placeholder="İleti metni">{!! old('body', $post->body) !!}</div>
        <input type="hidden" name="body" data-quill-input value="{{ old('body', $post->body) }}">
        <div class="forum-composer-footer"><button class="forum-button forum-button-primary">İletiyi Güncelle</button></div>
    </form></div>
</div></main>
@include('layouts.footer')
@endsection
