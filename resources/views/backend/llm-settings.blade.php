@extends('writer.layouts.portal')

@section('portal-content')
    @php
        $selectableModels = collect($models)
            ->reject(fn ($model) => preg_match('/free|batch/i', $model['name']))
            ->sortBy([
                fn ($a, $b) => strcasecmp(explode('/', $a['id'], 2)[0], explode('/', $b['id'], 2)[0]),
                fn ($a, $b) => $a['combined_per_million'] <=> $b['combined_per_million'],
                fn ($a, $b) => strcasecmp($a['name'], $b['name']),
            ]);
    @endphp
    <main>
        <div class="container py-4" style="max-width: 1100px">
            <h1 class="h3 mb-2">LLM Ayarları</h1>
            <p class="text-muted">Modeller doğrudan OpenRouter kataloğundan alınır. Fiyatlar bir milyon token için ABD
                dolarıdır.</p>
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="post" action="{{ route('admin.llm-settings.update') }}">@csrf @method('PUT')
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ([
            'frontend_model' => 'Ön yüz varsayılan modeli',
            'backend_model' => 'Yönetim varsayılan modeli',
            'cron_model' => 'Cron varsayılan modeli',
        ] as $field => $label)
                                @php($selectedModel = old($field, $setting->{$field}))
                                <div class="col-lg-4"><label class="form-label"
                                        for="{{ $field }}">{{ $label }}</label>
                                    <select class="form-select model-default-select" id="{{ $field }}"
                                        name="{{ $field }}" required>
                                        @if ($selectedModel && !collect($models)->contains('id', $selectedModel))
                                            <option value="{{ $selectedModel }}" selected>{{ $selectedModel }} (katalogda
                                                bulunamadı)</option>
                                        @endif
                                        @foreach ($selectableModels as $model)
                                            <option value="{{ $model['id'] }}" @selected($selectedModel === $model['id'])>
                                                {{ $model['name'] }} — Girdi
                                                ${{ number_format($model['input_per_million'], 2) }} / Çıktı
                                                ${{ number_format($model['output_per_million'], 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <strong>Kullanıcıların seçebileceği modeller</strong>
                        <input id="model-filter" class="form-control form-control-sm" style="max-width:320px"
                            placeholder="Model ara…">
                    </div>
                    <div class="card-body">
                        <div id="admin-model-list">
                            @php($allowed = old('allowed_frontend_models', $setting->allowed_frontend_models ?? collect($models)->pluck('id')->all()))
                            @forelse($selectableModels as $model)
                                <div class="model-option border-bottom py-1"
                                    data-search="{{ mb_strtolower($model['name'] . ' ' . $model['id']) }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <input class="form-check-input m-0 flex-shrink-0" type="checkbox" name="allowed_frontend_models[]"
                                            value="{{ $model['id'] }}" id="model-{{ $loop->index }}"
                                            @checked(in_array($model['id'], $allowed, true))>
                                        <label class="form-check-label d-flex flex-wrap align-items-baseline justify-content-between gap-1 w-100 small"
                                            style="min-width: 0; overflow-wrap: anywhere" for="model-{{ $loop->index }}">
                                            <span><strong>{{ $model['name'] }}</strong> <small
                                                class="text-muted">{{ $model['id'] }}</small></span>
                                            <span class="text-nowrap">Girdi:
                                                <strong>${{ number_format($model['input_per_million'], 2) }}</strong> ·
                                                Çıktı:
                                                <strong>${{ number_format($model['output_per_million'], 2) }}</strong>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning mb-0">OpenRouter model kataloğu şu anda alınamadı.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary mt-3" @disabled($selectableModels->isEmpty())>Ayarları kaydet</button>
            </form>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.getElementById('model-filter')?.addEventListener('input', function() {
            const query = this.value.toLocaleLowerCase('tr-TR').trim();
            document.querySelectorAll('.model-option').forEach(option => {
                option.classList.toggle('d-none', query && !option.dataset.search.includes(query));
            });
        });
    </script>
@endpush
