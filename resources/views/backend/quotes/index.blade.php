@extends('writer.layouts.portal')

@section('portal-content')
<main><div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h1 class="h3 mb-1">Edebiyat Alıntıları</h1><p class="text-muted mb-0">Tarihli alıntılar yalnızca seçilen gün gösterilir.</p></div>
        <a class="btn btn-primary" href="{{ route('admin.quotes.create') }}">Yeni alıntı</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead><tr><th>Alıntı</th><th>Yazar</th><th>Tarih</th><th class="text-end">İşlemler</th></tr></thead>
        <tbody>
        @forelse($quotes as $quote)
            <tr><td>{{ $quote->quote }}</td><td>{{ $quote->author }}</td><td>{{ $quote->day ? sprintf('%02d.%02d', $quote->day, $quote->month) : 'Her gün' }}</td>
                <td class="text-end"><div class="d-inline-flex gap-2"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.quotes.edit', $quote) }}">Düzenle</a>
                <form method="post" action="{{ route('admin.quotes.destroy', $quote) }}" onsubmit="return confirm('Bu alıntı silinsin mi?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Sil</button></form></div></td></tr>
        @empty<tr><td colspan="4" class="text-center text-muted py-4">Henüz alıntı yok.</td></tr>@endforelse
        </tbody>
    </table></div></div>
    <div class="mt-3">{{ $quotes->links() }}</div>
</div></main>
@endsection
