<div class="mega-menu-grid mega-menu-grid-{{ $menuType }}">
    @if($menuType === 'home')
        <section class="mega-menu-column mega-menu-intro">
            <span class="mega-menu-eyebrow">İzEdebiyat</span>
            <h3>Edebiyatı keşfet</h3>
            <p>Yeni eserleri okuyun, yazarları tanıyın ve topluluğa katılın.</p>
            <div class="mega-menu-actions">
                <a href="{{ route('frontend.recent-articles') }}">Son Eklenenler</a>
                <a href="{{ route('users') }}">Yazarlar</a>
                <a href="{{ route('frontend.join') }}">Katılım</a>
            </div>
        </section>
        <section class="mega-menu-column">
            <h3>Yeni eserler</h3>
            @forelse($megaMenuRecentArticles as $article)
                <a class="mega-menu-story" href="{{ route('article', $article->slug) }}">
                    <span>{{ $article->title }}</span><small>{{ $article->created_at?->diffForHumans() }}</small>
                </a>
            @empty
                <span class="mega-menu-empty">Henüz yeni eser yok.</span>
            @endforelse
        </section>
        <section class="mega-menu-column">
            <h3>Çok okunanlar</h3>
            @forelse($megaMenuTopArticles as $article)
                <a class="mega-menu-story" href="{{ route('article', $article->slug) }}">
                    <span>{{ $article->title }}</span><small>{{ number_format($article->read_count) }} okuma</small>
                </a>
            @empty
                <span class="mega-menu-empty">Henüz eser yok.</span>
            @endforelse
        </section>
    @elseif($menuType === 'category')
        <section class="mega-menu-column mega-menu-intro">
            <span class="mega-menu-eyebrow">Eser kümesi</span>
            <h3>{!! $menuCategory->category_name !!}</h3>
            <p><strong>{{ number_format($menuCategory->menu_total) }}</strong> eser, son 30 günde <strong>{{ number_format($menuCategory->menu_new) }}</strong> yeni eser.</p>
            <div class="mega-menu-actions">
                <a href="{{ route('frontend.category', $menuCategory->slug) }}">Tümünü gör</a>
                <a href="{{ route('frontend.recent-articles-by-category', $menuCategory->slug) }}">En yeniler</a>
            </div>
        </section>
        <section class="mega-menu-column mega-menu-subcategories">
            <h3>Alt kümeler</h3>
            <div class="mega-menu-subcategory-grid">
                @forelse($menuCategory->subCategories as $subCategory)
                    <a href="{{ route('frontend.subcategory', [$menuCategory->slug, $subCategory->slug]) }}">
                        <span>{!! $subCategory->category_name !!}</span>
                        <small><b>{{ number_format($subCategory->menu_new) }} yeni</b> · {{ number_format($subCategory->menu_total) }} toplam</small>
                    </a>
                @empty
                    <span class="mega-menu-empty">Bu kümede alt küme yok.</span>
                @endforelse
            </div>
        </section>
        <section class="mega-menu-column">
            <h3>Son eklenenler</h3>
            @forelse($menuCategory->menu_recent as $article)
                <a class="mega-menu-story" href="{{ route('article', $article->slug) }}">
                    <span>{{ $article->title }}</span><small>{{ $article->created_at?->diffForHumans() }}</small>
                </a>
            @empty
                <span class="mega-menu-empty">Henüz eser yok.</span>
            @endforelse
        </section>
    @elseif($menuType === 'books')
        <section class="mega-menu-column mega-menu-intro">
            <span class="mega-menu-eyebrow">Kitap İzleri</span>
            <h3>Okuma yolculukları</h3>
            <p>Kitap incelemelerini keşfedin veya incelenmesini istediğiniz kitabı gönderin.</p>
            <div class="mega-menu-actions"><a href="{{ route('frontend.book-reviews.index') }}">Tüm incelemeler</a></div>
        </section>
        <section class="mega-menu-column">
            <h3>Keşfet</h3>
            <a class="mega-menu-link" href="{{ route('frontend.book-reviews.authors') }}"><i class="bi bi-people"></i> Yazarlar</a>
            <a class="mega-menu-link" href="{{ route('frontend.book-reviews.categories') }}"><i class="bi bi-grid"></i> Kümeler</a>
            <a class="mega-menu-link" href="{{ route('frontend.book-reviews.tags') }}"><i class="bi bi-tags"></i> Etiketler</a>
            <a class="mega-menu-link" href="{{ route('frontend.book-reviews.create-submission') }}"><i class="bi bi-book"></i> İnceleme için kitap gönder</a>
        </section>
        <section class="mega-menu-column">
            <h3>Yeni incelemeler</h3>
            @forelse($megaMenuRecentBookReviews as $review)
                <a class="mega-menu-story" href="{{ route('frontend.book-review.show', $review->slug) }}">
                    <span>{{ $review->title }}</span><small>{{ $review->bookAuthor?->name ?? $review->author }}</small>
                </a>
            @empty
                <span class="mega-menu-empty">Henüz inceleme yok.</span>
            @endforelse
        </section>
    @elseif($menuType === 'forum')
        <section class="mega-menu-column mega-menu-intro">
            <span class="mega-menu-eyebrow">Topluluk</span>
            <h3>Forum</h3>
            <p>Edebiyat üzerine konuşun, tartışmalara katılın ve yeni başlıklar açın.</p>
            <div class="mega-menu-actions">
                <a href="{{ route('forum.index') }}">Tüm tartışmalar</a>
                @auth<a href="{{ route('forum.create') }}">Yeni tartışma</a>@endauth
            </div>
        </section>
        <section class="mega-menu-column">
            <h3>Kanallar</h3>
            <div class="mega-menu-tag-grid">
                @forelse($megaMenuForumTags as $tag)
                    <a href="{{ route('forum.tag', $tag->slug) }}"><i style="--tag-color: {{ $tag->color }}"></i><span>{{ $tag->name }}</span><small>{{ $tag->discussions_count }}</small></a>
                @empty
                    <span class="mega-menu-empty">Henüz kanal yok.</span>
                @endforelse
            </div>
        </section>
        <section class="mega-menu-column">
            <h3>Son tartışmalar</h3>
            @forelse($megaMenuRecentDiscussions as $discussion)
                <a class="mega-menu-story" href="{{ route('forum.show', $discussion->slug) }}">
                    <span>{{ $discussion->title }}</span><small>{{ $discussion->tag?->name }} · {{ $discussion->posts_count }} ileti</small>
                </a>
            @empty
                <span class="mega-menu-empty">Henüz tartışma yok.</span>
            @endforelse
        </section>
    @endif
</div>
