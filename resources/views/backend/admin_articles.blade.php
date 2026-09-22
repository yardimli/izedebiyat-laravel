@extends('writer.layouts.portal')
@section('title', 'IzEdebiyat - Admin Articles')
@section('portal-content')
    <main>
        <div class="container mt-5" style="min-height: calc(88vh);">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <h5 class="mb-0">Yeni Yazilar</h5>
                <form action="{{ route('admin.articles.index') }}" method="GET" class="flex-grow-1"
                    style="max-width: 760px;">
                    <div class="input-group">
                        <input name="search" type="text" class="form-control" placeholder="{{ __('Title, subtitle, or text ara') }}"
                            value="{{ request('search') }}">
                        <select class="form-select" name="per_page" style="max-width: 110px;" onchange="this.form.submit()">
                            @foreach ([25, 50, 100, 200] as $size)
                                <option value="{{ $size }}"
                                    {{ (int) request('per_page', 50) === $size ? 'selected' : '' }}>{{ $size }}
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary" type="submit">Search</button>
                        @if (request('search'))
                            <a class="btn btn-outline-secondary" href="{{ route('admin.articles.index') }}">Clear</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-body">
                    @if ($articles->isEmpty())
                        <p class="text-center my-3">{{ __('No articles found.') }}</p>
                    @else
                        <form id="bulk-articles-form" action="{{ route('admin.articles.bulk-update') }}" method="POST"
                            class="d-flex flex-wrap align-items-center gap-2 mb-3"
                            onsubmit="return confirm('Bu işlem seçili eserlere uygulansın mı?')">
                            @csrf
                            <input type="hidden" name="page" value="{{ request('page') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="per_page" value="{{ request('per_page', 50) }}">
                            <select class="form-select form-select-sm" name="bulk_action" style="max-width: 230px;">
                                <option value="">Bulk action</option>
                                <option value="publish">Set published</option>
                                <option value="unpublish">Set draft</option>
                                <option value="approve">Set approved</option>
                                <option value="unapprove">Set not approved</option>
                                <option value="publish_approve">Publish and approve</option>
                                <option value="unpublish_unapprove">Draft and not approved</option>
                                <option value="delete">Delete selected</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 42px;">
                                            <input class="form-check-input" type="checkbox" id="select-all-articles">
                                        </th>
                                        <th style="min-width: 320px;">Article</th>
                                        <th>Author</th>
                                        <th>Created</th>
                                        <th style="min-width: 250px;">Flags</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($articles as $article)
                                        <tr>
                                            <td>
                                                <input class="form-check-input article-checkbox" type="checkbox"
                                                    name="article_ids[]" value="{{ $article->id }}"
                                                    form="bulk-articles-form">
                                            </td>
                                            <td>
                                                <div class="fw-semibold">
                                                    @if ($article->is_published && $article->approved && !$article->deleted)
                                                        <a href="{{ route('article', $article->slug) }}" target="_blank"
                                                            rel="noopener noreferrer">{{ strip_tags($article->title) }}</a>@else{{ strip_tags($article->title) }}
                                                    @endif
                                                </div>
                                                @if ($article->subtitle)
                                                    <div class="small text-muted">{{ strip_tags($article->subtitle) }}
                                                    </div>
                                                @endif
                                                <div class="small text-muted mt-1">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($article->main_text), 140) }}
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    {{ $article->parent_category_name }}@if ($article->category_name)
                                                        / {{ $article->category_name }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if ($article->user)
                                                    <a href="{{ route('user', $article->user->slug) }}"
                                                        target="_blank">{{ $article->user->name }}</a>
                                                @else
                                                    {{ $article->name ?: '-' }}
                                                @endif
                                            </td>
                                            <td>{{ $article->created_at ? $article->created_at->format('d M Y H:i') : '-' }}
                                            </td>

                                            <td>
                                                <form action="{{ route('admin.articles.flags', $article) }}" method="POST"
                                                    class="d-flex flex-column gap-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="page" value="{{ request('page') }}">
                                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                                    <input type="hidden" name="per_page"
                                                        value="{{ request('per_page', 50) }}">
                                                    <div class="d-flex gap-2">
                                                        <select class="form-select form-select-sm" name="is_published">
                                                            <option value="1"
                                                                {{ $article->is_published ? 'selected' : '' }}>Published
                                                            </option>
                                                            <option value="0"
                                                                {{ !$article->is_published ? 'selected' : '' }}>Draft
                                                            </option>
                                                        </select>
                                                        <select class="form-select form-select-sm" name="approved">
                                                            <option value="1"
                                                                {{ $article->approved ? 'selected' : '' }}>Approved
                                                            </option>
                                                            <option value="0"
                                                                {{ !$article->approved ? 'selected' : '' }}>Not Approved
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <button type="submit"
                                                        class="btn btn-sm btn-primary align-self-start">Save</button>
                                                </form>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">
                            <div class="small text-muted">
                                Viewing {{ $articles->firstItem() }} - {{ $articles->lastItem() }} out of
                                {{ $articles->total() }}
                            </div>
                            {{ $articles->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all-articles');
            const checkboxes = document.querySelectorAll('.article-checkbox');

            if (!selectAll) {
                return;
            }

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = selectAll.checked;
                });
            });

            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    selectAll.checked = Array.from(checkboxes).every(function(item) {
                        return item.checked;
                    });
                });
            });
        });
    </script>
@endpush
