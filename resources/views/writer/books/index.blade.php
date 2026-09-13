@extends('writer.layouts.writer')
@section('title', __('Your library · ') . config('app.name'))
@section('content')
    <main class="library">
        <div class="eyebrow">{{ __('THE WRITING LIFE') }}</div>
        <div class="library-heading">
            <div>
                <h1>{{ __('A room for your stories.') }}</h1>
                <p class="muted">{{ __('Return to a world you know. Or begin somewhere new.') }}</p>
            </div><span class="ornament">❧</span>
        </div>
        <form action="{{ route('writer.books.store') }}" method="post" class="new-book">@csrf<label
                for="book-title">{{ __('Your next book') }}</label>
            <div class="row"><input id="book-title" name="title"
                    placeholder="{{ __('Every story begins with a title…') }}" maxlength="200" required><button
                    class="primary">{{ __('Begin a book') }} <span>↗</span></button></div>
        </form>
        <form method="get" action="{{ route('articles.index') }}" class="library-search" role="search">
            <label for="work-search">{{ __('Search your works') }}</label>
            <div class="row"><input id="work-search" type="search" name="q" value="{{ $search }}"
                    maxlength="200" placeholder="{{ __('Search titles and short descriptions…') }}"><button
                    type="submit">{{ __('Search') }}</button>
                @if ($search !== '')
                    <a
                        href="{{ route('articles.index', ['sort' => $sort, 'direction' => $direction]) }}">{{ __('Clear search') }}</a>
                @endif
            </div>
            <div class="library-sort row">
                <label for="work-sort">{{ __('Sort works by') }}<select id="work-sort" name="sort">
                        <option value="updated_at" @selected($sort === 'updated_at')>{{ __('Last modified') }}</option>
                        <option value="created_at" @selected($sort === 'created_at')>{{ __('Publication date') }}</option>
                        <option value="read_count" @selected($sort === 'read_count')>{{ __('Read count') }}</option>
                    </select></label>
                <label for="work-direction">{{ __('Sort direction') }}<select id="work-direction" name="direction">
                        <option value="desc" @selected($direction === 'desc')>{{ __('Descending') }}</option>
                        <option value="asc" @selected($direction === 'asc')>{{ __('Ascending') }}</option>
                    </select></label>
                <button type="submit">{{ __('Apply sorting') }}</button>
            </div>
        </form>
        <h2 class="section-heading">{{ __('On your desk') }} <span>{{ $books->total() }} {{ __('manuscripts') }}</span>
        </h2>
        <div class="book-grid">
            @forelse ($books as $book)
                <article class="book-card">
                    <div class="book-spine"></div>
                    <p class="book-publication-date muted">
                        {{ $book->is_published ? __('Publication date') : __('Created on') }}: @if ($book->created_at)
                            <time
                                datetime="{{ $book->created_at->toIso8601String() }}">{{ $book->created_at->format('d.m.Y H:i') }}</time>
                        @else
                            —
                        @endif
                    </p>
                    <h2>{{ $book->title }}</h2>
                    <p class="muted">{{ $book->metadata['genre'] ?? __('A work in progress') }}</p>
                    <p class="muted">{{ $book->is_published ? __('Published') : __('Draft') }} ·
                        {{ number_format($book->read_count) }} {{ __('reads') }} · {{ $book->comments_count }}
                        {{ __('comments') }}</p>
                    <p class="book-word-count">{{ number_format($book->word_count) }} {{ __('words') }}</p>
                    <div class="book-bottom">
                        <a
                            href="{{ route('articles.edit', \App\Helpers\IdHasher::encode($book->id)) }}">{{ __('Open manuscript ↗') }}</a>
                        @if ($book->is_published && $book->approved)
                            <a class="read-work" href="{{ route('article', $book->slug) }}" target="_blank"
                                rel="noopener noreferrer">{{ __('Read work') }} ↗</a>
                        @endif
                    </div>
                    <div class="book-file-actions" data-book-files="{{ $book->id }}">
                        @if ($book->word_count === 0)
                            <button type="button" data-import-book>{{ __('Import') }}</button>
                        @endif
                        <a href="{{ route('writer.books.export', ['book' => $book->id, 'format' => 'txt']) }}"
                            data-export-book>{{ __('Export ↗') }}</a>
                    </div>
                    <div class="book-manage-actions">
                        <label>{{ __('Publish status') }}<select data-publish-book="{{ $book->id }}"
                                data-revision="{{ $book->revision }}"
                                data-published="{{ $book->is_published ? '1' : '0' }}">
                                <option value="0" @selected(!$book->is_published)>{{ __('Draft') }}</option>
                                <option value="1" @selected($book->is_published)>{{ __('Published') }}</option>
                            </select></label>
                        <form method="post" action="{{ route('writer.books.destroy', $book) }}" data-delete-book>@csrf
                            @method('DELETE')<button type="submit">{{ __('Delete book') }}</button></form>
                    </div>
                </article>
            @empty <div class="empty-library"><span>Ⅰ</span>
                    <h2>{{ $search !== '' ? __('No matching works.') : __('The first page is waiting.') }}</h2>
                    <p>{{ $search !== '' ? __('Try another title or a phrase from the short description.') : __('Give your book a title above. You can always change it later.') }}
                    </p>
                </div>
            @endforelse
        </div>
        {{ $books->onEachSide(1)->links('writer.partials.pagination') }}
    </main>
    <dialog id="library-export-dialog" aria-labelledby="library-export-title">
        <div class="dialog-heading">
            <h2 id="library-export-title">{{ __('Export ↗') }}</h2><button type="button" data-close-dialog
                aria-label="{{ __('Close') }}">×</button>
        </div>
        <form id="library-export-form">
            <label for="library-export-format">{{ __('Export format') }}</label>
            <select id="library-export-format">
                <option value="txt">TXT</option>
                <option value="docx">DOCX</option>
            </select>
            <button type="submit" class="primary">{{ __('Save export') }}</button>
        </form>
    </dialog>
    <dialog id="library-import-dialog">
        <div class="dialog-heading">
            <h2>{{ __('Import your story') }}</h2><button type="button" id="library-cancel-import"
                aria-label="{{ __('Close import') }}">×</button>
        </div>
        <p>{{ __('Preview before replacing this book’s manuscript. A revision preserves the previous text.') }}</p><input
            id="library-import-file" type="file" accept=".txt,.docx">
        <textarea id="library-import-preview" rows="14" aria-label="{{ __('Imported text preview') }}"></textarea>
        <div class="row"><button id="library-confirm-import" class="primary"
                disabled>{{ __('Replace manuscript') }}</button></div>
    </dialog>
@endsection
