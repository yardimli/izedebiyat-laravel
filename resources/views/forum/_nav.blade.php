<div class="forum-nav">
    <a href="{{ route('forum.create') }}" class="forum-button forum-button-primary">
        <i class="bi bi-pencil-square"></i> Tartışma Başlat
    </a>
    <a href="{{ route('forum.index') }}" class="forum-nav-link {{ request()->routeIs('forum.index', 'forum.tag') ? 'active' : '' }}">
        <i class="bi bi-chat-dots"></i> Tüm Tartışmalar
    </a>
    <a href="{{ route('forum.tags') }}" class="forum-nav-link {{ request()->routeIs('forum.tags') ? 'active' : '' }}">
        <i class="bi bi-grid"></i> Etiketler
    </a>
</div>
