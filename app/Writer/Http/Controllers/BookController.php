<?php

namespace App\Writer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Writer\Models\AiCall;
use App\Writer\Models\Book;
use App\Writer\Models\CodexEntry;
use App\Writer\Services\Manuscript;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function edit(Request $request, string $hashedId)
    {
        $id = \App\Helpers\IdHasher::decode($hashedId);
        abort_unless($id, 404);

        return $this->show($request, Book::findOrFail($id));
    }

    public function create()
    {
        return redirect()->route('articles.index');
    }

    public function updateArticle(Request $request, string $hashedId)
    {
        $id = \App\Helpers\IdHasher::decode($hashedId);
        abort_unless($id, 404);

        return $this->update($request, Book::findOrFail($id));
    }

    public function destroyArticle(Request $request, string $hashedId)
    {
        $id = \App\Helpers\IdHasher::decode($hashedId);
        abort_unless($id, 404);

        return $this->destroy($request, Book::findOrFail($id));
    }

    private function applyPublicationDetails(Book $book, array &$data): void
    {
        if (array_key_exists('category_id', $data)) {
            $category = $data['category_id'] ? \App\Models\Category::with('parentCategory')->findOrFail($data['category_id']) : null;
            if ($category && ! $category->parentCategory) {
                throw \Illuminate\Validation\ValidationException::withMessages(['category_id' => 'Bir alt kategori seçin.']);
            }
            $data += ['category_name' => $category?->category_name, 'category_slug' => $category?->slug, 'parent_category_id' => $category?->parent_category_id ?? 0, 'parent_category_name' => $category?->parentCategory?->category_name, 'parent_category_slug' => $category?->parentCategory?->slug];
        }
        if (($data['is_published'] ?? $book->is_published) && ! ($data['category_id'] ?? (array_key_exists('category_id', $data) ? null : $book->category_id))) {
            throw \Illuminate\Validation\ValidationException::withMessages(['category_id' => 'Yayımlamadan önce bir kategori seçin.']);
        }
        if (array_key_exists('featured_image', $data) && $data['featured_image'] && $data['featured_image'] !== $book->featured_image) {
            abort_unless(\App\Models\Image::where('user_id', $book->user_id)->where('image_original_filename', $data['featured_image'])->exists(), 422, 'Kendi görsellerinizden birini seçin.');
        }
        if (array_key_exists('keywords_string', $data)) {
            $tags = array_values(array_unique(array_filter(array_map('trim', explode(',', $data['keywords_string'] ?? '')))));
            $ids = [];
            foreach ($tags as $tag) {
                if (mb_strlen($tag) > 16) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['keywords_string' => 'Etiketler en fazla 16 karakter olabilir.']);
                }
                $ids[] = \App\Models\Keyword::firstOrCreate(['keyword' => $tag], ['keyword_slug' => \Illuminate\Support\Str::slug($tag)])->id;
            }
            $book->keywords()->sync($ids);
            $data['keywords_string'] = implode(', ', $tags);
        }
        if (array_intersect(array_keys($data), ['title', 'subtitle', 'subheading', 'document', 'category_id', 'keywords_string', 'featured_image'])) {
            $data['has_changed'] = 1;
        }
    }

    public function uploadImage(Request $request, Book $book)
    {
        $this->owned($request, $book);
        $request->validate(['image' => 'required|image|mimes:jpg,jpeg,png,gif|max:5120|dimensions:max_width=10000,max_height=10000']);
        $file = $request->file('image');
        $guid = (string) \Illuminate\Support\Str::uuid();
        $name = $guid.'.'.$file->extension();
        foreach (['original', 'large', 'medium', 'small'] as $size) {
            $file->storeAs('upload-images/'.$size, $name, 'public');
        }
        \App\Models\Image::create(['user_id' => $request->user()->id, 'image_type' => 'upload', 'image_guid' => $guid, 'image_alt' => $book->title, 'image_original_filename' => $name, 'image_large_filename' => $name, 'image_medium_filename' => $name, 'image_small_filename' => $name]);

        return ['filename' => $name, 'url' => asset('storage/upload-images/original/'.$name)];
    }

    public function export(Request $request, Book $book, string $format)
    {
        $this->owned($request, $book);
        abort_unless(in_array($format, ['txt', 'docx']), 404);
        $content = $format === 'txt' ? Manuscript::text($book->document) : app(\App\Writer\Services\ManuscriptExport::class)->docx($book->document);
        $filename = (\Illuminate\Support\Str::slug($book->title) ?: 'manuscript').'.'.$format;

        return response()->streamDownload(fn () => print ($content), $filename, ['Content-Type' => $format === 'txt' ? 'text/plain; charset=UTF-8' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'Cache-Control' => 'private, no-store']);
    }

    public function owned(Request $request, Book $book): void
    {
        abort_unless((int) $book->user_id === (int) $request->user()->id, 404);
    }

    public function index(Request $request)
    {
        $filter = $request->query('filter', 'active');
        abort_unless(in_array($filter, ['active', 'archived', 'deleted']), 422);
        $books = Book::where('user_id', $request->user()->id);
        if ($filter === 'deleted') {
            $books->onlyTrashed();
        } else {
            $books->where('archived', $filter === 'archived');
        }

        return view('writer.books.index', ['books' => $books->select(['id', 'title', 'user_id', 'metadata', 'manuscript', 'archived', 'deleted', 'revision', 'updated_at', 'read_count', 'is_published', 'approved', 'category_name', 'subtitle'])->withCount('comments')->latest('updated_at')->paginate(30)->withQueryString(), 'filter' => $filter]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['title' => 'required|string|max:200']);
        $book = Book::create($data + ['user_id' => $request->user()->id, 'slug' => (\Illuminate\Support\Str::slug($data['title']) ?: 'eser').'-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(10)), 'approved' => 1, 'is_published' => 0, 'deleted' => 0, 'name' => $request->user()->name, 'name_slug' => $request->user()->slug ?: \Illuminate\Support\Str::slug($request->user()->name), 'document' => Manuscript::fromText(''), 'codex_types' => ['People', 'Places', 'Items', 'Organizations', 'Events', 'Lore']]);

        return redirect()->route('articles.edit', \App\Helpers\IdHasher::encode($book->id));
    }

    public function show(Request $request, Book $book)
    {
        $this->owned($request, $book);

        app(\App\Writer\Services\DefaultFavorites::class)->initialize($request, app(\App\Writer\Services\ModelCatalog::class));

        abort_if($book->document === null, 503, 'Run php artisan writer:migrate-articles before opening the workspace.');

        return view('writer.books.show', ['book' => $book, 'categories' => \App\Models\Category::with('parentCategory')->where('parent_category_id', '>', 0)->orderBy('category_name')->get()]);
    }

    public function state(Request $request, Book $book)
    {
        $this->owned($request, $book);

        // A pending request with no process holding the book lock ended unexpectedly.
        $lock = new \App\Writer\Services\BookChatLock;
        if ($lock->acquire($book->id)) {
            try {
                $book->messages()->where('role', 'user')->where('status', 'pending')->update(['status' => 'failed']);
            } finally {
                $lock->release();
            }
        }

        return response()->json(['book' => $book, 'entries' => $book->entries()->orderBy('name')->get(),
            'messages' => $book->messages()->orderBy('id')->get(), 'proposals' => $book->proposals()->orderBy('id')->get(),
            'revisions' => $book->revisions()->latest('id')->limit(100)->get(['id', 'label', 'created_at']),
            'usage' => ['book' => AiCall::where('book_id', $book->id)->sum('cost'), 'account' => AiCall::where('user_id', $request->user()->id)->sum('cost'),
                'demo_percentage' => \App\Writer\Services\DemoBudget::percentage($request->user()), 'personal_key' => (bool) $request->user()->openrouter_key, 'pending' => AiCall::where('user_id', $request->user()->id)->whereNull('cost')->sum('reserved')]]);
    }

    public function update(Request $request, Book $book)
    {
        $this->owned($request, $book);
        $data = $request->validate(['revision' => 'required|integer', 'title' => 'sometimes|required|string|max:200', 'document' => 'sometimes|required|array',
            'metadata' => 'sometimes|array', 'metadata.synopsis' => 'nullable|string|max:20000', 'metadata.genre' => 'nullable|string|max:200',
            'metadata.point_of_view' => 'nullable|string|max:200', 'metadata.tense' => 'nullable|string|max:200', 'metadata.style_notes' => 'nullable|string|max:20000',
            'archived' => 'sometimes|boolean', 'codex_types' => 'sometimes|array|min:1|max:50', 'codex_types.*' => 'string|max:80|distinct',
            'subtitle' => 'sometimes|nullable|string|max:255', 'subheading' => 'sometimes|nullable|string|max:500', 'category_id' => 'sometimes|nullable|integer|exists:categories,id', 'keywords_string' => 'sometimes|nullable|string|max:255', 'featured_image' => 'sometimes|nullable|string|max:255', 'is_published' => 'sometimes|boolean']);
        if (isset($data['document'])) {
            Manuscript::validate($data['document']);
        }

        return DB::transaction(function () use ($book, $data) {
            $book = Book::lockForUpdate()->findOrFail($book->id);
            Manuscript::checkRevision($book, (int) $data['revision']);
            Manuscript::snapshot($book, isset($data['document']) ? 'Manuscript save' : 'Book settings');
            if (isset($data['codex_types'])) {
                abort_if($book->entries()->whereNotIn('type', $data['codex_types'])->exists(), 422, __('Move entries to another type before removing their type.'));
            }
            $this->applyPublicationDetails($book, $data);
            $book->fill($data);
            if (isset($data['document'])) {
                $book->manuscript = Manuscript::text($data['document']);
            }
            $book->revision++;
            $book->save();

            return ['revision' => $book->revision];
        });
    }

    public function destroy(Request $request, Book $book)
    {
        $this->owned($request, $book);
        $book->delete();

        return redirect()->route('articles.index');
    }

    public function recover(Request $request, int $id)
    {
        $book = Book::withTrashed()->where('user_id', $request->user()->id)->findOrFail($id);
        $book->restore();

        return redirect()->route('articles.index');
    }

    public function llmLog(Request $request, Book $book)
    {
        $this->owned($request, $book);

        return response()->json(AiCall::where('book_id', $book->id)->latest('id')->paginate(30, ['id', 'model', 'stage', 'status', 'cost', 'reserved', 'response_status', 'error', 'created_at']))->header('Cache-Control', 'private, no-store');
    }

    public function llmLogPage(Request $request, Book $book)
    {
        $this->owned($request, $book);
        $filters = $request->validate(['stage' => 'nullable|string|max:100', 'status' => 'nullable|in:reserved,pending,settled,cancelled', 'funding' => 'nullable|in:demo,personal']);
        $query = AiCall::where('book_id', $book->id);
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $query->where($field, $value);
            }
        }
        $summary = (clone $query)->selectRaw('COUNT(*) AS actions, SUM(prompt_tokens) AS prompt_tokens, SUM(completion_tokens) AS completion_tokens, SUM(cost) AS cost, COUNT(prompt_tokens) AS recorded')->first();
        $stages = AiCall::where('book_id', $book->id)->distinct()->pluck('stage');
        $calls = $query->latest('id')
            ->select(['id', 'model', 'stage', 'status', 'funding', 'cost', 'reserved', 'prompt_tokens', 'completion_tokens', 'total_tokens', 'response_status', 'error', 'created_at'])
            ->selectRaw('request_payload IS NOT NULL AS has_request, response_body IS NOT NULL AS has_response')
            ->paginate(30)->withQueryString();

        return response()->view('writer.books.llm-log', compact('book', 'calls', 'summary', 'stages', 'filters'))->header('Cache-Control', 'private, no-store');
    }

    public function llmCallPage(Request $request, Book $book, int $id)
    {
        $this->owned($request, $book);
        $call = AiCall::where('book_id', $book->id)->findOrFail($id);
        $payload = \App\Writer\Support\JsonDisplay::format($call->getRawOriginal('request_payload'));
        $response = \App\Writer\Support\JsonDisplay::format($call->getRawOriginal('response_body'));

        return response()->view('writer.books.llm-call', compact('book', 'call', 'payload', 'response'))->header('Cache-Control', 'private, no-store');
    }

    public function llmCall(Request $request, Book $book, int $id)
    {
        $this->owned($request, $book);

        return response()->json(AiCall::where('book_id', $book->id)->findOrFail($id))->header('Cache-Control', 'private, no-store');
    }

    public function deleteMessage(Request $request, Book $book, int $id)
    {
        $this->owned($request, $book);
        $book->messages()->findOrFail($id)->delete();

        return response()->json(['deleted' => true]);
    }

    public function revision(Request $request, Book $book, int $id)
    {
        $this->owned($request, $book);

        return response()->json($book->revisions()->findOrFail($id))->header('Cache-Control', 'private, no-store');
    }

    public function restore(Request $request, Book $book, int $id)
    {
        $this->owned($request, $book);
        $request->validate(['revision' => 'required|integer']);

        return DB::transaction(function () use ($book, $request, $id) {
            $book = Book::lockForUpdate()->findOrFail($book->id);
            Manuscript::checkRevision($book, (int) $request->revision);
            $snapshot = $book->revisions()->findOrFail($id)->snapshot;
            Manuscript::snapshot($book, 'Before revision restore');
            $book->fill(collect($snapshot)->only(['document', 'metadata', 'title', 'codex_types'])->all());
            $book->manuscript = Manuscript::text($book->document);
            $book->revision++;
            $book->save();
            $book->entries()->delete();
            foreach ($snapshot['entries'] as $entry) {
                $book->entries()->create(collect($entry)->only(['id', 'name', 'type', 'content', 'aliases', 'revision'])->all());
            }

            return ['revision' => $book->revision];
        });
    }

    public function entry(Request $request, Book $book, ?int $id = null)
    {
        $this->owned($request, $book);
        $data = $request->validate(['revision' => 'required|integer', 'name' => 'required|string|max:200', 'type' => 'required|string|max:80', 'content' => 'nullable|string|max:100000', 'aliases' => 'nullable|string|max:4000']);

        return DB::transaction(function () use ($book, $data, $id) {
            $book = Book::lockForUpdate()->findOrFail($book->id);
            Manuscript::checkRevision($book, (int) $data['revision']);
            abort_unless(in_array($data['type'], $book->codex_types), 422, __('Add the codex type first.'));
            Manuscript::snapshot($book, 'Codex edit');
            $entry = $id ? $book->entries()->findOrFail($id) : new CodexEntry(['book_id' => $book->id]);
            $entry->fill(collect($data)->except('revision')->all());
            $entry->aliases = array_values(array_unique(array_filter(array_map('trim', explode(',', $data['aliases'] ?? '')))));
            $entry->revision = ($entry->revision ?? 0) + 1;
            $entry->save();
            $book->increment('revision');

            return ['entry' => $entry, 'revision' => $book->revision];
        });
    }

    public function deleteEntry(Request $request, Book $book, int $id)
    {
        $this->owned($request, $book);
        $request->validate(['revision' => 'required|integer']);

        return DB::transaction(function () use ($book, $request, $id) {
            $book = Book::lockForUpdate()->findOrFail($book->id);
            Manuscript::checkRevision($book, (int) $request->revision);
            Manuscript::snapshot($book, 'Before codex deletion');
            $book->entries()->findOrFail($id)->delete();
            $book->increment('revision');

            return ['revision' => $book->revision];
        });
    }
}
