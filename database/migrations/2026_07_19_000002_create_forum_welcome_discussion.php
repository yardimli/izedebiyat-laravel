<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $adminId = DB::table('users')
            ->where('member_type', 1)
            ->orderBy('id')
            ->value('id');

        $tagId = DB::table('forum_tags')->where('slug', 'duyurular')->value('id')
            ?? DB::table('forum_tags')->orderBy('sort_order')->value('id');

        if (! $adminId || ! $tagId || DB::table('forum_discussions')->where('slug', 'yeni-forumumuza-hos-geldiniz')->exists()) {
            return;
        }

        $now = now();
        $discussionId = DB::table('forum_discussions')->insertGetId([
            'user_id' => $adminId,
            'forum_tag_id' => $tagId,
            'title' => 'Yeni Forumumuza Hoş Geldiniz',
            'slug' => 'yeni-forumumuza-hos-geldiniz',
            'views_count' => 0,
            'is_pinned' => true,
            'is_locked' => false,
            'last_post_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('forum_posts')->insert([
            'forum_discussion_id' => $discussionId,
            'user_id' => $adminId,
            'parent_id' => null,
            'body' => '<p>Yeni forumumuza hoş geldiniz! Düşüncelerinizi paylaşmanızı ve güzel sohbetlere katılmanızı bekliyoruz.</p>',
            'edited_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        $discussionId = DB::table('forum_discussions')
            ->where('slug', 'yeni-forumumuza-hos-geldiniz')
            ->value('id');

        if ($discussionId) {
            DB::table('forum_discussions')->where('id', $discussionId)->delete();
        }
    }
};
