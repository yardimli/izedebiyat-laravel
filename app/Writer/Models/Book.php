<?php

namespace App\Writer\Models;

use App\Models\Article;
use App\Writer\Services\Manuscript;
use App\Writer\Services\ManuscriptHtml;
use Illuminate\Database\Eloquent\Builder;

/** The writing workspace uses the original article row and identity. */
class Book extends Article
{
    protected $fillable = [];

    protected $guarded = ['id'];

    protected $hidden = ['writer_original_text'];

    protected $attributes = ['revision' => 1, 'deleted' => 0, 'archived' => 0, 'approved' => 1, 'is_published' => 0];

    protected $casts = ['document' => 'array', 'metadata' => 'array', 'codex_types' => 'array', 'archived' => 'boolean', 'is_published' => 'boolean', 'deleted' => 'boolean', 'revision' => 'integer', 'user_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    protected static function booted(): void
    {
        static::addGlobalScope('writer_visible', fn (Builder $query) => $query->where('articles.deleted', 0));
        static::creating(function (Book $book) {
            if (! $book->slug) {
                $book->slug = (\Illuminate\Support\Str::slug($book->title) ?: 'eser').'-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(10));
            }
        });
        static::saving(function (Book $book) {
            if ($book->isDirty('document') && $book->document !== null) {
                Manuscript::validate($book->document);
                $book->manuscript = Manuscript::text($book->document);
                $book->main_text = (new \League\HTMLToMarkdown\HtmlConverter(['strip_tags' => true, 'hard_break' => true]))->convert(ManuscriptHtml::html($book->document));
                $book->markdown = 1;
                $book->has_changed = 1;
            }
        });
    }

    public function getWordCountAttribute(): int
    {
        return preg_match_all('/\S+/u', $this->manuscript ?? Manuscript::text($this->document ?? []));
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class, 'article_id');
    }

    public function entries()
    {
        return $this->hasMany(CodexEntry::class, 'book_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'book_id');
    }

    public function proposals()
    {
        return $this->hasMany(AiProposal::class, 'book_id');
    }

    public function revisions()
    {
        return $this->hasMany(Revision::class, 'book_id');
    }
}
