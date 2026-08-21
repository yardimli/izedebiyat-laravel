<?php

namespace App\Http\Controllers;

use App\Models\LlmSetting;
use App\Services\OpenRouterModelCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
        return view('backend.llm-settings', [
            'setting' => LlmSetting::current(),
            'models' => $catalog->all(),
        ]);
    }

    public function update(Request $request, OpenRouterModelCatalog $catalog)
    {
        $modelIds = array_column($catalog->all(), 'id');
        $data = $request->validate([
            'frontend_model' => ['required', 'string', Rule::in($modelIds)],
            'backend_model' => ['required', 'string', Rule::in($modelIds)],
            'cron_model' => ['required', 'string', Rule::in($modelIds)],
            'allowed_frontend_models' => ['required', 'array', 'min:1'],
            'allowed_frontend_models.*' => ['string', 'distinct', Rule::in($modelIds)],
        ]);

        if (!in_array($data['frontend_model'], $data['allowed_frontend_models'], true)) {
            throw ValidationException::withMessages([
                'frontend_model' => 'Varsayılan ön yüz modeli, izin verilen modeller arasında olmalıdır.',
            ]);
        }

        LlmSetting::query()->updateOrCreate(['id' => 1], $data);

        return back()->with('success', 'LLM ayarları güncellendi.');
    }
}
