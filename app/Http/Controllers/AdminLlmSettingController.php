<?php

namespace App\Http\Controllers;

use App\Helpers\MyHelper;
use App\Models\LlmSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function edit()
    {
        return view('backend.llm-settings', [
            'setting' => LlmSetting::current(),
            'models' => MyHelper::checkLLMsJson(false),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'frontend_model' => ['required', 'string', 'max:255'],
            'backend_model' => ['required', 'string', 'max:255'],
            'cron_model' => ['required', 'string', 'max:255'],
            'allowed_frontend_models' => ['required', 'array', 'min:1'],
            'allowed_frontend_models.*' => ['string', 'max:255', 'distinct'],
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
