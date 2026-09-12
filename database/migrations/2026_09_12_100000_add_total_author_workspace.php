<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        \App\Writer\Services\LegacyDates::run(fn () => $this->addWorkspace());
    }

    private function addWorkspace(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->json('document')->nullable();
            $table->longText('manuscript')->nullable();
            $table->json('metadata')->nullable();
            $table->json('codex_types')->nullable();
            $table->unsignedInteger('revision')->default(1);
            $table->boolean('archived')->default(false)->index();
            $table->longText('writer_original_text')->nullable();
            $table->boolean('writer_original_markdown')->nullable();
            $table->timestamp('writer_migrated_at')->nullable();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->text('openrouter_key')->nullable();
            $table->string('selected_model')->nullable();
            $table->json('favorite_models')->nullable();
            $table->string('theme')->default('paper');
            $table->decimal('demo_spent', 16, 8)->default(0);
            $table->decimal('demo_reserved', 16, 8)->default(0);
            // Nullable permits new users to inherit the current environment allowance.
            $table->decimal('demo_limit', 16, 8)->nullable();
            $table->decimal('demo_allowance', 16, 8)->nullable();
            $table->boolean('favorites_only')->default(true);
            $table->timestamp('favorites_initialized_at')->nullable();
            $table->timestamp('model_selected_at')->nullable();
        });
        $allowance = \App\Writer\Services\DemoBudget::allowance();
        DB::table('users')->update(['demo_limit' => $allowance, 'demo_allowance' => $allowance]);
        Schema::create('writer_codex_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('articles')->cascadeOnDelete();
            $table->string('name');
            $table->string('type');
            $table->longText('content')->nullable();
            $table->json('aliases');
            $table->unsignedInteger('revision')->default(1);
            $table->timestamps();
        });
        Schema::create('writer_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('articles')->cascadeOnDelete();
            $table->string('label');
            $table->json('snapshot');
            $table->timestamps();
        });
        Schema::create('writer_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('articles')->cascadeOnDelete();
            $table->string('role');
            $table->longText('content');
            $table->uuid('request_id')->nullable()->unique();
            $table->json('suggestions')->nullable();
            $table->string('status', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('writer_ai_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('articles')->cascadeOnDelete();
            $table->unsignedInteger('base_revision');
            $table->json('changes');
            $table->json('decisions')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('chat_message_id')->nullable()->constrained('writer_chat_messages')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('writer_ai_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('articles')->cascadeOnDelete();
            $table->string('model');
            $table->string('stage');
            $table->string('funding');
            $table->string('status')->default('reserved');
            $table->string('provider_id')->nullable();
            $table->decimal('reserved', 16, 8)->default(0);
            $table->decimal('cost', 16, 8)->nullable();
            $table->longText('request_payload')->nullable();
            $table->longText('response_body')->nullable();
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->string('error')->nullable();
            foreach (['prompt_tokens', 'completion_tokens', 'total_tokens'] as $name) {
                $table->unsignedBigInteger($name)->nullable();
            }
            $table->timestamps();
            $table->index(['user_id', 'funding', 'status']);
        });
        Schema::create('writer_budget_resets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('previous_limit', 16, 8);
            $table->decimal('new_limit', 16, 8);
            $table->decimal('allowance', 16, 8);
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        \App\Writer\Services\LegacyDates::run(fn () => $this->removeWorkspace());
    }

    private function removeWorkspace(): void
    {
        foreach (['writer_budget_resets', 'writer_ai_calls', 'writer_ai_proposals', 'writer_chat_messages', 'writer_revisions', 'writer_codex_entries'] as $name) {
            Schema::dropIfExists($name);
        }
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['openrouter_key', 'selected_model', 'favorite_models', 'theme', 'demo_spent', 'demo_reserved', 'demo_limit', 'demo_allowance', 'favorites_only', 'favorites_initialized_at', 'model_selected_at']));
        Schema::table('articles', fn (Blueprint $table) => $table->dropIndex('articles_archived_index'));
        Schema::table('articles', fn (Blueprint $table) => $table->dropColumn(['document', 'manuscript', 'metadata', 'codex_types', 'revision', 'archived', 'writer_original_text', 'writer_original_markdown', 'writer_migrated_at']));
    }
};
