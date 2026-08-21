<?php

namespace App\Http\Controllers;

use App\Helpers\MyHelper;
use App\Models\LlmSetting;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminQuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
            return $next($request);
        });
    }

    public function index()
    {
        $quotes = Quote::query()->orderByRaw('month is null, month, day')->orderByDesc('id')->paginate(30);
        return view('backend.quotes.index', compact('quotes'));
    }

    public function create()
    {
        return view('backend.quotes.form', ['quote' => new Quote()]);
    }

    public function store(Request $request)
    {
        Quote::create($this->validated($request));
        return redirect()->route('admin.quotes.index')->with('success', 'Alıntı eklendi.');
    }

    public function edit(Quote $quote)
    {
        return view('backend.quotes.form', compact('quote'));
    }

    public function update(Request $request, Quote $quote)
    {
        $quote->update($this->validated($request));
        return redirect()->route('admin.quotes.index')->with('success', 'Alıntı güncellendi.');
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return redirect()->route('admin.quotes.index')->with('success', 'Alıntı silindi.');
    }

    public function generate(Request $request)
    {
        $data = $request->validate(['include_date' => ['nullable', 'boolean']]);
        $includeDate = (bool) ($data['include_date'] ?? false);
        $dateInstruction = $includeDate
            ? 'Alıntının edebiyat açısından anlamlı bir güne ait olması uygunsa day ve month alanlarını sayı olarak doldur; değilse null bırak.'
            : 'day ve month alanlarını mutlaka null bırak.';

        $prompt = 'Dünya edebiyatından kısa bir alıntıyı doğal Türkçe çevirisiyle üret. '
            .'Alıntı 160 karakteri aşmasın, yazar adı güvenilir ve kısa olsun. Uydurma bir atıf yapma. '
            .$dateInstruction.' Yalnızca şu JSON biçiminde yanıt ver: '
            .'{"quote":"...","author":"...","day":null,"month":null}';

        $result = MyHelper::llm_no_tool_call(
            LlmSetting::modelFor('backend'),
            '',
            [['role' => 'user', 'content' => $prompt]],
            true
        );

        if ($result['error'] ?? true) {
            return response()->json(['message' => $result['content'] ?? 'Alıntı üretilemedi.'], 422);
        }

        $day = $includeDate ? ($result['day'] ?? null) : null;
        $month = $includeDate ? ($result['month'] ?? null) : null;
        if (($day || $month) && (!is_numeric($day) || !is_numeric($month) || !checkdate((int) $month, (int) $day, 2024))) {
            $day = $month = null;
        }

        return response()->json([
            'quote' => trim((string) ($result['quote'] ?? '')),
            'author' => trim((string) ($result['author'] ?? '')),
            'day' => $day,
            'month' => $month,
        ]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'quote' => ['required', 'string', 'max:500'],
            'author' => ['required', 'string', 'max:150'],
            'day' => ['nullable', 'integer', 'between:1,31', 'required_with:month'],
            'month' => ['nullable', 'integer', 'between:1,12', 'required_with:day'],
        ]);

        if (($data['day'] ?? null) && !checkdate((int) $data['month'], (int) $data['day'], 2024)) {
            abort(422, 'Geçersiz gün ve ay.');
        }

        return $data;
    }
}
