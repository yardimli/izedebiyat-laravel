@extends('writer.layouts.portal')

@section('portal-content')
<main><div class="container py-4" style="max-width: 1100px">
    <h1 class="h3 mb-2">LLM Ayarları</h1>
    <p class="text-muted">Modeller doğrudan OpenRouter kataloğundan alınır. Fiyatlar bir milyon token için ABD dolarıdır.</p>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form method="post" action="{{ route('admin.llm-settings.update') }}">@csrf @method('PUT')
        <div class="card mb-4"><div class="card-body"><div class="row g-3">
            @foreach([
                'frontend_model' => 'Ön yüz varsayılan modeli',
                'backend_model' => 'Yönetim varsayılan modeli',
                'cron_model' => 'Cron varsayılan modeli',
            ] as $field => $label)
                @php($selectedModel = old($field, $setting->{$field}))
                <div class="col-lg-4"><label class="form-label" for="{{ $field }}">{{ $label }}</label>
                    <select class="form-select model-default-select" id="{{ $field }}" name="{{ $field }}" required>
                        @if($selectedModel && !collect($models)->contains('id', $selectedModel))
                            <option value="{{ $selectedModel }}" selected>{{ $selectedModel }} (katalogda bulunamadı)</option>
                        @endif
                        @foreach($models as $model)
                            <option value="{{ $model['id'] }}" @selected($selectedModel === $model['id'])>
                                {{ $model['name'] }} — Girdi ${{ number_format($model['input_per_million'], 2) }} / Çıktı ${{ number_format($model['output_per_million'], 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endforeach
        </div></div></div>

        <div class="card"><div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <strong>Kullanıcıların seçebileceği modeller</strong>
            <input id="model-filter" class="form-control form-control-sm" style="max-width:320px" placeholder="Model ara…">
        </div><div class="card-body"><div class="row g-2" id="model-list">
            @php($allowed = old('allowed_frontend_models', $setting->allowed_frontend_models ?? collect($models)->pluck('id')->all()))
            @forelse($models as $model)
                <div class="col-lg-6 model-option" data-search="{{ mb_strtolower($model['name'].' '.$model['id']) }}">
                    <div class="form-check border rounded p-3 ps-5 h-100">
                        <input class="form-check-input" type="checkbox" name="allowed_frontend_models[]" value="{{ $model['id'] }}" id="model-{{ $loop->index }}" @checked(in_array($model['id'], $allowed, true))>
                        <label class="form-check-label w-100" for="model-{{ $loop->index }}">
                            <strong>{{ $model['name'] }}</strong><br><small class="text-muted">{{ $model['id'] }}</small>
                            <div class="small mt-1">Girdi: <strong>${{ number_format($model['input_per_million'], 2) }}</strong> · Çıktı: <strong>${{ number_format($model['output_per_million'], 2) }}</strong></div>
                        </label>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="alert alert-warning mb-0">OpenRouter model kataloğu şu anda alınamadı.</div></div>
            @endforelse
        </div></div></div>
        <button class="btn btn-primary mt-3" @disabled(empty($models))>Ayarları kaydet</button>
    </form>
</div></main>
@endsection

@push('scripts')
<script>
document.getElementById('model-filter')?.addEventListener('input', function () {
    const query = this.value.toLocaleLowerCase('tr-TR').trim();
    document.querySelectorAll('.model-option').forEach(option => {
        option.classList.toggle('d-none', query && !option.dataset.search.includes(query));
    });
});
</script>
@endpush
