@extends('layouts.app')

@section('title', 'Forum Yönetimi - İzEdebiyat')

@section('content')
<main>
    <div class="container mt-5" style="min-height: 88vh;">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h4 class="mb-1">Forum Yönetimi</h4>
                <div class="text-muted small">Tartışmaları, iletileri ve kullanıcı bildirimlerini yönetin.</div>
            </div>
            <form action="{{ route('admin.forum.index') }}" method="get" class="input-group" style="max-width: 480px;">
                <input class="form-control" name="search" value="{{ $search }}" placeholder="Başlık veya ileti ara">
                <button class="btn btn-primary">Ara</button>
                @if($search)<a class="btn btn-outline-secondary" href="{{ route('admin.forum.index') }}">Temizle</a>@endif
            </form>
        </div>

        @php $section = request('section', request('flags_page') ? 'flags' : (request('posts_page') ? 'posts' : 'discussions')); @endphp
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item"><button class="nav-link {{ $section === 'discussions' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#forum-discussions">Tartışmalar <span class="badge bg-secondary">{{ $discussions->total() }}</span></button></li>
            <li class="nav-item"><button class="nav-link {{ $section === 'posts' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#forum-posts">İletiler <span class="badge bg-secondary">{{ $posts->total() }}</span></button></li>
            <li class="nav-item"><button class="nav-link {{ $section === 'flags' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#forum-flags">Bildirimler <span class="badge bg-danger">{{ $flags->where('status', 'pending')->count() }}</span></button></li>
        </ul>

        <div class="tab-content">
            <section id="forum-discussions" class="tab-pane fade {{ $section === 'discussions' ? 'show active' : '' }}">
                <div class="card"><div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th>Tartışma</th><th>Etiket</th><th>Yazar</th><th>İletiler</th><th>Durum</th><th>Son etkinlik</th><th>İşlemler</th></tr></thead>
                        <tbody>
                        @forelse($discussions as $discussion)
                            <tr>
                                <td><a class="fw-semibold" href="{{ route('forum.show', $discussion) }}" target="_blank">{{ $discussion->title }}</a><div class="small text-muted">{{ $discussion->views_count }} görüntülenme</div></td>
                                <td><span class="badge" style="background: {{ $discussion->tag->color }}">{{ $discussion->tag->name }}</span></td>
                                <td>{{ $discussion->user->name }}</td>
                                <td>{{ $discussion->posts_count }}</td>
                                <td>
                                    @if($discussion->is_pinned)<span class="badge bg-info">Sabit</span>@endif
                                    @if($discussion->is_locked)<span class="badge bg-warning">Kilitli</span>@endif
                                    @unless($discussion->is_pinned || $discussion->is_locked)<span class="text-muted">Açık</span>@endunless
                                </td>
                                <td>{{ optional($discussion->last_post_at)->format('d.m.Y H:i') }}</td>
                                <td><div class="d-flex gap-2">
                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.forum.discussions.edit', $discussion) }}">Düzenle</a>
                                    <form method="post" action="{{ route('admin.forum.discussions.destroy', $discussion) }}" onsubmit="return confirm('Bu tartışma ve tüm iletileri kalıcı olarak silinsin mi?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Sil</button></form>
                                </div></td>
                            </tr>
                        @empty<tr><td colspan="7" class="text-center text-muted py-4">Tartışma bulunamadı.</td></tr>@endforelse
                        </tbody>
                    </table>
                    {{ $discussions->appends(['section' => 'discussions'])->links() }}
                </div></div>
            </section>

            <section id="forum-posts" class="tab-pane fade {{ $section === 'posts' ? 'show active' : '' }}">
                <div class="card"><div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th style="min-width: 380px">İleti</th><th>Tartışma</th><th>Yazar</th><th>Tarih</th><th>İşlemler</th></tr></thead>
                        <tbody>
                        @forelse($posts as $post)
                            <tr>
                                <td>{{ Str::limit(strip_tags($post->body), 180) }} @if($post->edited_at)<span class="badge bg-secondary">Düzenlendi</span>@endif</td>
                                <td><a href="{{ route('forum.show', $post->discussion) }}#post-{{ $post->id }}" target="_blank">{{ Str::limit($post->discussion->title, 55) }}</a></td>
                                <td>{{ $post->user->name }}</td>
                                <td>{{ $post->created_at->format('d.m.Y H:i') }}</td>
                                <td><div class="d-flex gap-2">
                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.forum.posts.edit', $post) }}">Düzenle</a>
                                    <form method="post" action="{{ route('admin.forum.posts.destroy', $post) }}" onsubmit="return confirm('Bu ileti silinsin mi?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Sil</button></form>
                                </div></td>
                            </tr>
                        @empty<tr><td colspan="5" class="text-center text-muted py-4">İleti bulunamadı.</td></tr>@endforelse
                        </tbody>
                    </table>
                    {{ $posts->appends(['section' => 'posts'])->links() }}
                </div></div>
            </section>

            <section id="forum-flags" class="tab-pane fade {{ $section === 'flags' ? 'show active' : '' }}">
                <div class="card"><div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th>Neden</th><th>Bildirilen ileti</th><th>Bildiren</th><th>İleti yazarı</th><th>Durum</th><th>İşlem</th></tr></thead>
                        <tbody>
                        @forelse($flags as $flag)
                            <tr>
                                <td>{{ $flag->reason }}<div class="small text-muted">{{ $flag->created_at->format('d.m.Y H:i') }}</div></td>
                                <td>
                                    @if($flag->post)
                                        <a href="{{ route('forum.show', $flag->post->discussion) }}#post-{{ $flag->post->id }}" target="_blank">{{ Str::limit(strip_tags($flag->post->body), 100) }}</a>
                                    @else<span class="text-muted">İleti silinmiş</span>@endif
                                </td>
                                <td>{{ optional($flag->user)->name ?? 'Silinmiş üye' }}</td>
                                <td>{{ optional(optional($flag->post)->user)->name ?? '-' }}</td>
                                <td><span class="badge bg-{{ $flag->status === 'pending' ? 'danger' : ($flag->status === 'resolved' ? 'success' : 'secondary') }}">{{ ['pending' => 'Bekliyor', 'resolved' => 'Çözüldü', 'dismissed' => 'Reddedildi'][$flag->status] ?? $flag->status }}</span></td>
                                <td>
                                    <form method="post" action="{{ route('admin.forum.flags.update', $flag) }}" class="d-flex gap-2">@csrf @method('PATCH')
                                        <select class="form-select form-select-sm" name="status"><option value="pending" @selected($flag->status === 'pending')>Bekliyor</option><option value="resolved" @selected($flag->status === 'resolved')>Çözüldü</option><option value="dismissed" @selected($flag->status === 'dismissed')>Reddedildi</option></select>
                                        <button class="btn btn-sm btn-primary">Kaydet</button>
                                    </form>
                                </td>
                            </tr>
                        @empty<tr><td colspan="6" class="text-center text-muted py-4">Bildirim bulunamadı.</td></tr>@endforelse
                        </tbody>
                    </table>
                    {{ $flags->appends(['section' => 'flags'])->links() }}
                </div></div>
            </section>
        </div>
    </div>
</main>
@include('layouts.footer')
@endsection
