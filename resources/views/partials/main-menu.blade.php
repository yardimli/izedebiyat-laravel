<nav id="main-menu" class="stick d-lg-block d-none" aria-label="Ana menü">
    <div class="container">
        <div class="menu-primary">
            <ul class="mega-menu-root">
                <li class="mega-menu-item current-menu-item">
                    <a href="{{ route('frontend.index') }}">ANA SAYFA</a>
                    <button class="mega-menu-toggle" type="button" aria-expanded="false" aria-label="Ana Sayfa menüsünü aç"><i class="bi bi-chevron-down"></i></button>
                    <div class="mega-menu-panel">@include('partials.mega-menu-content', ['menuType' => 'home'])</div>
                </li>
                @foreach($mainMenuCategories as $category)
                    <li class="mega-menu-item">
                        <a href="{{ route('frontend.category', $category->slug) }}">
                            <span>{!! $category->category_name !!}</span>
                            <span class="mega-menu-nav-stats">@if($category->menu_new > 0)<b>{{ number_format($category->menu_new) }}</b><small>/{{ number_format($category->menu_total) }}</small>@else<small>{{ number_format($category->menu_total) }}</small>@endif</span>
                        </a>
                        <button class="mega-menu-toggle" type="button" aria-expanded="false" aria-label="{{ strip_tags($category->category_name) }} menüsünü aç"><i class="bi bi-chevron-down"></i></button>
                        <div class="mega-menu-panel">@include('partials.mega-menu-content', ['menuType' => 'category', 'menuCategory' => $category])</div>
                    </li>
                @endforeach
                @if(config('features.kitap_izleri_visible'))
                    <li class="mega-menu-item">
                        <a href="{{ route('frontend.book-reviews.index') }}">KİTAP İZLERİ</a>
                        <button class="mega-menu-toggle" type="button" aria-expanded="false" aria-label="Kitap İzleri menüsünü aç"><i class="bi bi-chevron-down"></i></button>
                        <div class="mega-menu-panel">@include('partials.mega-menu-content', ['menuType' => 'books'])</div>
                    </li>
                @endif
                <li class="mega-menu-item">
                    <a href="{{ route('forum.index') }}">FORUM</a>
                    <button class="mega-menu-toggle" type="button" aria-expanded="false" aria-label="Forum menüsünü aç"><i class="bi bi-chevron-down"></i></button>
                    <div class="mega-menu-panel">@include('partials.mega-menu-content', ['menuType' => 'forum'])</div>
                </li>
            </ul>
        </div>
    </div>
</nav>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menu = document.getElementById('main-menu');
        if (!menu) return;
        const items = menu.querySelectorAll('.mega-menu-item');
        const closeMenus = (except) => items.forEach(item => {
            if (item !== except) item.classList.remove('is-open');
            const button = item.querySelector('.mega-menu-toggle');
            if (button && item !== except) button.setAttribute('aria-expanded', 'false');
        });
        items.forEach(item => {
            const button = item.querySelector('.mega-menu-toggle');
            button?.addEventListener('click', event => {
                event.stopPropagation();
                const willOpen = !item.classList.contains('is-open');
                closeMenus(item);
                item.classList.toggle('is-open', willOpen);
                button.setAttribute('aria-expanded', String(willOpen));
            });
        });
        document.addEventListener('click', event => {
            if (!menu.contains(event.target)) closeMenus();
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeMenus();
        });
    });
</script>
@endpush
