@extends('writer.layouts.portal')

@section('portal-content')
    <main>
        <div class="container py-4" style="max-width: 850px">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ $quote->exists ? 'Alıntıyı düzenle' : 'Yeni alıntı' }}</h1><a
                    href="{{ route('admin.quotes.index') }}" class="btn btn-outline-secondary">Listeye dön</a>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <button type="button" id="generate-quote" class="btn btn-outline-primary">LLM ile üret</button>
                        <div class="form-check"><input class="form-check-input" type="checkbox" id="include-date"><label
                                class="form-check-label" for="include-date">Yanıtta anlamlı gün ve ay öner</label></div>
                        <span id="generate-status" class="small text-muted"></span>
                    </div>
                </div>
            </div>
            <form method="post"
                action="{{ $quote->exists ? route('admin.quotes.update', $quote) : route('admin.quotes.store') }}"
                class="card">
                <div class="card-body">
                    @csrf @if ($quote->exists)
                        @method('PUT')
                    @endif
                    <div class="mb-3"><label class="form-label" for="quote">Alıntı</label>
                        <textarea class="form-control" id="quote" name="quote" rows="3" required maxlength="500">{{ old('quote', $quote->quote) }}</textarea>
                    </div>
                    <div class="mb-3"><label class="form-label" for="author">Yazar</label><input class="form-control"
                            id="author" name="author" required maxlength="150"
                            value="{{ old('author', $quote->author) }}"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label" for="day">Gün (isteğe bağlı)</label><input
                                class="form-control" type="number" min="1" max="31" id="day"
                                name="day" value="{{ old('day', $quote->day) }}"></div>
                        <div class="col-md-6 mb-3"><label class="form-label" for="month">Ay (isteğe bağlı)</label><select
                                class="form-select" id="month" name="month">
                                <option value="">Her ay</option>
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}" @selected((string) old('month', $quote->month) === (string) $month)>
                                        {{ \Carbon\Carbon::create(2024, $month)->locale('tr')->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select></div>
                    </div>
                    <button class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.getElementById('generate-quote').addEventListener('click', async function() {
            const button = this,
                status = document.getElementById('generate-status');
            button.disabled = true;
            status.textContent = 'Üretiliyor…';
            try {
                const response = await fetch(@json(route('admin.quotes.generate')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        include_date: document.getElementById('include-date').checked
                    })
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Alıntı üretilemedi.');
                document.getElementById('quote').value = data.quote || '';
                document.getElementById('author').value = data.author || '';
                document.getElementById('day').value = data.day || '';
                document.getElementById('month').value = data.month || '';
                status.textContent = 'Form dolduruldu; kaydetmeden önce kontrol edin.';
            } catch (error) {
                status.textContent = error.message;
            } finally {
                button.disabled = false;
            }
        });
    </script>
@endpush
