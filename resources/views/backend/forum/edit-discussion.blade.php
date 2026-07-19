@extends('layouts.app')
@section('title', 'Tartışmayı Düzenle - Forum Yönetimi')
@section('content')
<main><div class="container mt-5" style="max-width: 850px; min-height: 88vh;">
    <div class="d-flex justify-content-between align-items-center mb-4"><h4>Tartışmayı Düzenle</h4><a href="{{ route('admin.forum.index') }}" class="btn btn-outline-secondary">Geri</a></div>
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <div class="card"><div class="card-body">
        <form method="post" action="{{ route('admin.forum.discussions.update', $discussion) }}">@csrf @method('PUT')
            <div class="mb-3"><label class="form-label">Başlık</label><input class="form-control" name="title" value="{{ old('title', $discussion->title) }}" maxlength="180" required></div>
            <div class="mb-3"><label class="form-label">Etiket</label><select class="form-select" name="forum_tag_id" required>@foreach($tags as $tag)<option value="{{ $tag->id }}" @selected(old('forum_tag_id', $discussion->forum_tag_id) == $tag->id)>{{ $tag->name }}</option>@endforeach</select></div>
            <div class="form-check mb-2"><input type="hidden" name="is_pinned" value="0"><input class="form-check-input" type="checkbox" name="is_pinned" value="1" id="is-pinned" @checked(old('is_pinned', $discussion->is_pinned))><label class="form-check-label" for="is-pinned">Tartışmayı sabitle</label></div>
            <div class="form-check mb-4"><input type="hidden" name="is_locked" value="0"><input class="form-check-input" type="checkbox" name="is_locked" value="1" id="is-locked" @checked(old('is_locked', $discussion->is_locked))><label class="form-check-label" for="is-locked">Yeni yanıtlara kilitle</label></div>
            <button class="btn btn-primary">Değişiklikleri Kaydet</button>
        </form>
    </div></div>
</div></main>
@include('layouts.footer')
@endsection
