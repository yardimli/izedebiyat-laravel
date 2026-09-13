@if(auth()->check() && (int) session('admin_impersonation.user_id', 0) === (int) auth()->id())
<style>
.admin-impersonation-banner { display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:10px 20px; flex-shrink:0; padding:10px 16px; background:var(--soft,#fff3cd); color:var(--ink,#382f14); border-bottom:1px solid var(--line,#d6c47c); font:14px/1.5 Georgia,serif; }
.admin-impersonation-banner form { margin:0; }
.admin-impersonation-banner button { width:auto; padding:6px 12px; border:1px solid currentColor; border-radius:4px; background:transparent; color:inherit; font:inherit; cursor:pointer; }
</style>
<aside class="admin-impersonation-banner" aria-label="Yönetici olarak görüntüleme">
    <span><strong>Yönetici olarak görüntülüyorsunuz:</strong> {{ auth()->user()->name }} ({{ auth()->user()->email }})</span>
    <form method="post" action="{{ route('users-stop-impersonating') }}">@csrf<button type="submit">Yönetici hesabıma dön</button></form>
</aside>
@endif
