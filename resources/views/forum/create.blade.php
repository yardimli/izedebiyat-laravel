@extends('layouts.app-frontend')

@section('title', 'Yeni Tartışma - Forum')
@section('body-class', 'forum-page')

@push('styles')
    @vite('resources/js/forum.js')
@endpush

@section('content')
<div class="forum-shell">
    <div class="forum-container">
        @include('forum._nav')
        <div class="forum-composer-card forum-create-card">
            <div class="forum-composer-heading">
                @include('forum._avatar', ['user' => auth()->user(), 'class' => 'forum-avatar-large'])
                <div><span>Yeni bir sohbet açın</span><h1>Tartışma Başlat</h1></div>
            </div>

            @if($errors->any())
                <div class="forum-errors"><strong>Lütfen aşağıdakileri düzeltin:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <form method="post" action="{{ route('forum.store') }}" data-quill-form>
                @csrf
                <div class="forum-create-fields">
                    <label>
                        <span>Etiket</span>
                        <select name="forum_tag_id" required>
                            <option value="">Bir etiket seçin</option>
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" @selected(old('forum_tag_id') == $tag->id)>{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="forum-title-field">
                        <span>Başlık</span>
                        <input type="text" name="title" value="{{ old('title') }}" maxlength="180" placeholder="Neyi konuşmak istersiniz?" required>
                    </label>
                </div>
                <div class="forum-editor" data-quill-editor data-placeholder="İletinizi yazın...">{!! old('body') !!}</div>
                <input type="hidden" name="body" data-quill-input value="{{ old('body') }}">
                <div class="forum-composer-footer">
                    <button class="forum-button forum-button-primary" type="submit"><i class="bi bi-send-fill"></i> Tartışmayı Yayımla</button>
                    <span>Saygılı, yapıcı ve konuya uygun bir dil kullanın.</span>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
