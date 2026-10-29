@extends('writer.layouts.portal')
@section('title', 'LLM Kullanımı')
@section('portal-content')
<main class="container mt-5" style="min-height: 88vh">
    <h1>LLM Kullanımı</h1>
    <p>Yazı atölyesindeki kullanıcıların model çağrıları. Tarihler: {{ config('app.timezone') }}.</p>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
    @endif
    <form method="GET" action="{{ route('admin.llm-usage.index') }}" class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><label for="usage-user">Kullanıcı (ad, e-posta veya ID)</label><input class="form-control" id="usage-user" name="user" value="{{ $filters['user'] ?? '' }}" maxlength="200"></div>
                <div class="col-md-4"><label for="usage-article">Eser (başlık, slug veya ID)</label><input class="form-control" id="usage-article" name="article" value="{{ $filters['article'] ?? '' }}" maxlength="200"></div>
                <div class="col-md-4"><label for="usage-model">Model</label><select class="form-select" id="usage-model" name="model"><option value="">Tüm modeller</option>@foreach ($models as $model)<option value="{{ $model }}" @selected(($filters['model'] ?? '') === $model)>{{ $model }}</option>@endforeach</select></div>
                <div class="col-md-4"><label for="usage-from">Başlangıç tarihi</label><input class="form-control" type="date" id="usage-from" name="from" value="{{ $filters['from'] ?? '' }}"></div>
                <div class="col-md-4"><label for="usage-to">Bitiş tarihi (dahil)</label><input class="form-control" type="date" id="usage-to" name="to" value="{{ $filters['to'] ?? '' }}"></div>
            </div>
            <div class="mt-3"><button class="btn btn-primary" type="submit">Filtrele</button> <a class="btn btn-secondary" href="{{ route('admin.llm-usage.index') }}">Temizle</a></div>
        </div>
    </form>
    <div class="card mb-4"><div class="card-body">
        <p><strong>Filtrelenen tüm kayıtlar:</strong> {{ number_format($summary->calls) }} çağrı · {{ number_format($summary->users) }} kullanıcı</p>
        <p>Girdi token: {{ $summary->prompt_tokens === null ? '—' : number_format($summary->prompt_tokens) }} · Çıktı token: {{ $summary->completion_tokens === null ? '—' : number_format($summary->completion_tokens) }} · Toplam token: {{ $summary->total_tokens === null ? '—' : number_format($summary->total_tokens) }}</p>
        <p class="small text-muted">Toplam token bilgisi bulunan çağrı: {{ number_format($summary->recorded) }} / {{ number_format($summary->calls) }}. “—” kaydedilmemiş bilgiyi gösterir; toplamlar yalnızca bilinen sayıları içerir.</p>
    </div></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Tarih / Çağrı</th><th>Kullanıcı</th><th>Eser</th><th>Model / İşlem</th><th>Girdi token</th><th>Çıktı token</th><th>Toplam token</th><th>Durum / Kaynak</th></tr></thead>
            <tbody>
                @forelse ($calls as $call)
                    <tr>
                        <td style="white-space: nowrap">{{ \Carbon\Carbon::parse($call->created_at)->format('d.m.Y H:i:s') }}<div class="small text-muted">#{{ $call->id }}</div></td>
                        <td>
                            @if ($call->user_slug)<a href="{{ route('user', $call->user_slug) }}" target="_blank" rel="noopener">{{ $call->user_name }}</a>@else{{ $call->user_name ?? 'Kullanıcı bulunamadı' }}@endif
                            <div class="small">{{ $call->email }}</div><div class="small text-muted">#{{ $call->user_id }}</div>
                        </td>
                        <td>
                            @if ($call->article_title !== null)
                                @if ($call->article_slug && $call->is_published && $call->approved && !$call->deleted)
                                    <a href="{{ route('article', $call->article_slug) }}" target="_blank" rel="noopener">{{ strip_tags($call->article_title) }}</a>
                                @else
                                    {{ strip_tags($call->article_title) }} <span class="small text-muted">({{ $call->deleted ? 'Silinmiş' : 'Yayında değil' }})</span>
                                @endif
                                <div class="small text-muted">#{{ $call->book_id }}</div>
                            @else
                                <span class="text-muted">Eser silinmiş veya ilişkilendirilmemiş</span>
                            @endif
                        </td>
                        <td style="overflow-wrap: anywhere">{{ $call->model }}<div class="small text-muted">{{ $call->stage }}</div></td>
                        @foreach (['prompt_tokens', 'completion_tokens', 'total_tokens'] as $field)<td>{{ $call->$field === null ? '—' : number_format($call->$field) }}</td>@endforeach
                        <td>{{ ['reserved' => 'Ayrıldı', 'pending' => 'Bekliyor', 'settled' => 'Hesaplandı', 'cancelled' => 'İptal edildi'][$call->status] ?? $call->status }}<div class="small text-muted">{{ ['demo' => 'Deneme kredisi', 'personal' => 'Kişisel API anahtarı'][$call->funding] ?? $call->funding }}</div></td>
                    </tr>
                @empty
                    <tr><td colspan="8">Bu filtrelere uygun LLM kullanım kaydı bulunamadı.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $calls->links() }}
</main>
@endsection
