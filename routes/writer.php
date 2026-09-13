<?php

use App\Writer\Http\Controllers\BookController;
use App\Writer\Http\Controllers\BudgetController;
use App\Writer\Http\Controllers\ChatController;
use App\Writer\Http\Controllers\NameController;
use App\Writer\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', \App\Writer\Http\Middleware\WriterLocale::class])->prefix('yazi-atolyesi')->group(function () {
    Route::get('/', fn () => redirect()->route('articles.index'))->name('writer.dashboard');
    Route::post('/eserler', [BookController::class, 'store'])->name('writer.books.store');
    Route::get('/eserler/{book}', function (\App\Writer\Models\Book $book) {
        abort_unless((int) $book->user_id === (int) auth()->id(), 404);

        return redirect()->route('articles.edit', \App\Helpers\IdHasher::encode($book->id));
    })->name('writer.books.show');
    Route::get('/eserler/{book}/yapay-zeka-gunlugu', [BookController::class, 'llmLogPage'])->name('writer.books.llm-log');
    Route::get('/eserler/{book}/yapay-zeka-gunlugu/{id}', [BookController::class, 'llmCallPage'])->name('writer.books.llm-call');
    Route::get('/eserler/{book}/disari-aktar/{format}', [BookController::class, 'export'])->name('writer.books.export');
    Route::delete('/eserler/{book}', [BookController::class, 'destroy'])->name('writer.books.destroy');
    Route::match(['get', 'post'], '/tanitim-tercihi', [SettingsController::class, 'welcomePreference'])->name('writer.welcome-preference');
    Route::get('/hesap', [SettingsController::class, 'edit'])->name('writer.settings');
    Route::patch('/hesap', [SettingsController::class, 'update'])->name('writer.settings.update');
    Route::get('/admin/kotalar', [BudgetController::class, 'index'])->name('writer.budgets.index');
    Route::post('/admin/kotalar/{user}/yenile', [BudgetController::class, 'reset'])->name('writer.budgets.reset');
    Route::prefix('api')->group(function () {
        Route::get('/countries', [NameController::class, 'countries']);
        Route::get('/names', [NameController::class, 'index']);
        Route::get('/models', [SettingsController::class, 'models']);
        Route::post('/models/refresh', [SettingsController::class, 'refresh'])->middleware('throttle:6,1');
        Route::get('/books/{book}', [BookController::class, 'state']);
        Route::patch('/books/{book}', [BookController::class, 'update']);
        Route::post('/books/{book}/publication-ai/{kind}', [\App\Writer\Http\Controllers\PublicationAiController::class, 'suggest'])->whereIn('kind', ['category', 'keywords'])->middleware('throttle:12,1');
        Route::post('/books/{book}/featured-image', [BookController::class, 'uploadImage']);
        Route::post('/books/{book}/entries/{id?}', [BookController::class, 'entry']);
        Route::delete('/books/{book}/entries/{id}', [BookController::class, 'deleteEntry']);
        Route::post('/books/{book}/revisions/{id}/restore', [BookController::class, 'restore']);
        Route::get('/books/{book}/revisions/{id}', [BookController::class, 'revision']);
        Route::get('/books/{book}/llm-log', [BookController::class, 'llmLog']);
        Route::get('/books/{book}/llm-log/{id}', [BookController::class, 'llmCall']);
        Route::delete('/books/{book}/messages/{id}', [BookController::class, 'deleteMessage']);
        Route::post('/books/{book}/chat', [ChatController::class, 'send'])->middleware('throttle:12,1');
        Route::post('/books/{book}/proposals/{id}', [ChatController::class, 'approve']);
    });
});
