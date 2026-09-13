<details class="admin-menu"><summary title="Yönetim" aria-label="Yönetim"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6Z"/><path d="m8 12 3 3 5-6"/></svg><span>Yönetim</span></summary>
<div class="admin-menu-items">
@foreach(['admin-users-index'=>'Kullanıcılar','admin.articles.index'=>'Eserler','admin.read-cleanup.index'=>'Okuma kayıtları','book-reviews.index'=>'Kitap incelemeleri','book-authors.index'=>'Kitap yazarları','admin.forum.index'=>'Forum','admin.quotes.index'=>'Edebiyat alıntıları','admin.llm-settings.edit'=>'Yapay zeka ayarları','admin.account-recovery.index'=>'Hesap kurtarma talepleri'] as $route=>$label)
<a href="{{ route($route) }}">{{ $label }}</a>
@endforeach
</div></details>
