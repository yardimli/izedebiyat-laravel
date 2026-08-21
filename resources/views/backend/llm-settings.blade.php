@extends('layouts.app')

@section('content')
<main><div class="container py-4" style="max-width: 1000px">
    <h1 class="h3 mb-4">LLM Ayarları</h1>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="post" action="{{ route('admin.llm-settings.update') }}">@csrf @method('PUT')
        <div class="card mb-4"><div class="card-body"><div class="row g-3">
            <div class="col-md-4"><label class="form-label">Ön yüz varsayılan modeli</label><input class="form-control" name="frontend_model" value="{{ old('frontend_model', $setting->frontend_model) }}" required></div>
            <div class="col-md-4"><label class="form-label">Yönetim modeli</label><input class="form-control" name="backend_model" value="{{ old('backend_model', $setting->backend_model) }}" required></div>
            <div class="col-md-4"><label class="form-label">Cron modeli</label><input class="form-control" name="cron_model" value="{{ old('cron_model', $setting->cron_model) }}" required></div>
        </div><p class="small text-muted mt-3 mb-0">Model kimlikleri OpenRouter biçiminde yazılır. Mevcut değerler, önceki sabit varsayılanlardır.</p></div></div>
        <div class="card"><div class="card-header"><strong>Kullanıcıların seçebileceği modeller</strong></div><div class="card-body"><div class="row g-2">
            @php($allowed = old('allowed_frontend_models', $setting->allowed_frontend_models ?? collect($models)->pluck('id')->all()))
            @foreach($models as $model)<div class="col-md-6"><div class="form-check border rounded p-3 ps-5 h-100"><input class="form-check-input" type="checkbox" name="allowed_frontend_models[]" value="{{ $model['id'] }}" id="model-{{ $loop->index }}" @checked(in_array($model['id'], $allowed, true))><label class="form-check-label" for="model-{{ $loop->index }}"><strong>{{ $model['name'] ?? $model['id'] }}</strong><br><small class="text-muted">{{ $model['id'] }}</small></label></div></div>@endforeach
        </div></div></div>
        <button class="btn btn-primary mt-3">Ayarları kaydet</button>
    </form>
</div></main>
@endsection
