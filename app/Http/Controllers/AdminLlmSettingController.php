<?php

namespace App\Http\Controllers;

use App\Models\LlmSetting;
use App\Services\OpenRouterModelCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminLlmSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
            return $next($request);
        });
    }

    public function edit(OpenRouterModelCatalog $catalog)
    {
        $result = $catalog->load();
        return view('backend.llm-settings', [
            'setting' => LlmSetting::current(),
            'models' => $result['models'],
            'catalogStatus' => $result,
        ]);
    }

    public function update(Request $request, OpenRouterModelCatalog $catalog)
    {
        $modelIds = array_column($catalog->all(), 'id');
        $data = $request->validate([
            'frontend_model' => ['required', 'string', Rule::in($modelIds)],
            'frontend_model_fallback_1' => ['nullable', 'required_with:frontend_model_fallback_2', 'string', 'different:frontend_model', Rule::in($modelIds)],
            'frontend_model_fallback_2' => ['nullable', 'string', 'different:frontend_model', 'different:frontend_model_fallback_1', Rule::in($modelIds)],
            'backend_model' => ['required', 'string', Rule::in($modelIds)],
            'cron_model' => ['required', 'string', Rule::in($modelIds)],
            'allowed_frontend_models' => ['sometimes', 'array'],
            'allowed_frontend_models.*' => ['string', 'distinct', Rule::in($modelIds)],
        ]);

        $data['frontend_model_fallback_1'] = $data['frontend_model_fallback_1'] ?? null;
        $data['frontend_model_fallback_2'] = $data['frontend_model_fallback_2'] ?? null;
        $defaults = array_filter([$data['frontend_model'], $data['frontend_model_fallback_1'], $data['frontend_model_fallback_2']]);
        $data['allowed_frontend_models'] = array_values(array_unique([...($data['allowed_frontend_models'] ?? []), ...$defaults]));

        LlmSetting::query()->updateOrCreate(['id' => 1], $data);

        return back()->with('success', 'LLM ayarları güncellendi.');
    }
}
