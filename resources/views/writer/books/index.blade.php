@extends('writer.layouts.writer')
@section('title', __('Your library · ').config('app.name'))
@section('content')
<main class="library">
    @if(auth()->user()->isAdmin())<a class="history-button" href="{{ route('writer.budgets.index') }}">{{ __('AI quotas') }}</a>@endif
    <div class="eyebrow">{{ __('THE WRITING LIFE') }}</div>
    <div class="library-heading"><div><h1>{{ __('A room for your stories.') }}</h1><p class="muted">{{ __('Return to a world you know. Or begin somewhere new.') }}</p></div><span class="ornament">❧</span></div>
    <form action="{{ route('writer.books.store') }}" method="post" class="new-book">@csrf<label for="book-title">{{ __('Your next book') }}</label><div class="row"><input id="book-title" name="title" placeholder="{{ __('Every story begins with a title…') }}" maxlength="200" required><button class="primary">{{ __('Begin a book') }} <span>↗</span></button></div></form>
    <h2 class="section-heading">{{ __('On your desk') }} <span>{{ $books->total() }} {{ __('manuscripts') }}</span></h2>
    <div class="book-grid">
    @forelse ($books as $book)
        <article class="book-card">
            <div class="book-spine"></div><div class="eyebrow">{{ __('MANUSCRIPT') }}</div>
            <h2>{{ $book->title }}</h2><p class="muted">{{ $book->metadata['genre'] ?? __('A work in progress') }}</p>
            <p class="muted">{{ $book->is_published ? __('Published') : __('Draft') }} · {{ number_format($book->read_count) }} {{ __('reads') }} · {{ $book->comments_count }} {{ __('comments') }}</p><p class="book-word-count">{{ number_format($book->word_count) }} {{ __('words') }}</p>
            <div class="book-bottom"><small>{{ __('Last opened') }} {{ $book->updated_at->diffForHumans() }}</small>
            <a href="{{ route('articles.edit', \App\Helpers\IdHasher::encode($book->id)) }}">{{ __('Open manuscript ↗') }}</a></div>
            <div class="book-file-actions" data-book-files="{{ $book->id }}">
                <button type="button" data-import-book>{{ __('Import') }}</button>
                <select aria-label="{{ __('Export format for :book', ['book' => $book->title]) }}"><option value="txt">TXT</option><option value="docx">DOCX</option></select>
                <button type="button" data-export-book>{{ __('Export ↗') }}</button>
            </div>
            <div class="book-manage-actions">
                <label>{{ __('Publish status') }}<select data-publish-book="{{ $book->id }}" data-revision="{{ $book->revision }}" data-published="{{ $book->is_published ? '1' : '0' }}"><option value="0" @selected(!$book->is_published)>{{ __('Draft') }}</option><option value="1" @selected($book->is_published)>{{ __('Published') }}</option></select></label>
                <form method="post" action="{{ route('writer.books.destroy', $book) }}" data-delete-book>@csrf @method('DELETE')<button type="submit">{{ __('Delete book') }}</button></form>
            </div>
        </article>
    @empty <div class="empty-library"><span>Ⅰ</span><h2>{{ __('The first page is waiting.') }}</h2><p>{{ __('Give your book a title above. You can always change it later.') }}</p></div> @endforelse
    </div>
{{ $books->links('pagination::simple-default') }}
</main>
<dialog id="library-import-dialog"><div class="dialog-heading"><h2>{{ __('Import your story') }}</h2><button type="button" id="library-cancel-import" aria-label="{{ __('Close import') }}">×</button></div><p>{{ __('Preview before replacing this book’s manuscript. A revision preserves the previous text.') }}</p><input id="library-import-file" type="file" accept=".txt,.docx"><textarea id="library-import-preview" rows="14" aria-label="{{ __('Imported text preview') }}"></textarea><div class="row"><button id="library-confirm-import" class="primary" disabled>{{ __('Replace manuscript') }}</button></div></dialog>
@endsection
