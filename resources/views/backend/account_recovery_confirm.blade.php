@extends('writer.layouts.portal')
@section('title', 'Hesap İşlemini Onayla')
@section('portal-content')
<main class="container mt-5" style="min-height: 88vh">
    <h1>{{ $request->delete_account ? 'Kalıcı Hesap Silme Onayı' : 'Hesap Kurtarma Onayı' }}</h1>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
    @endif
    <div class="card"><div class="card-body">
        <p><strong>Hesap:</strong> {{ $user->name }} (#{{ $user->id }})</p>
        <p><strong>Profil adresi:</strong> <a href="{{ route('user', $user->slug) }}" target="_blank" rel="noopener">{{ route('user', $user->slug) }}</a></p>
        <p><strong>Slug:</strong> {{ $user->slug }}</p>
        <p><strong>Mevcut e-posta:</strong> {{ $user->email }}</p>
        <form method="POST" action="{{ route('admin.account-recovery.approve', $request->id) }}">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <input type="hidden" name="confirmation_token" value="{{ $token }}">
            @if ($request->delete_account)
                <p class="alert alert-danger">Bu hesap ve tüm yazıları kalıcı olarak silinecek. Bu işlem geri alınamaz.</p>
            @else
                <label for="new_email">Kaydedilecek yeni e-posta (değiştirebilirsiniz)</label>
                <input class="form-control mb-3" type="email" id="new_email" name="new_email" value="{{ old('new_email', $newEmail) }}" required maxlength="255">
                <p>Onaydan sonra bu adrese şifre belirleme bağlantısı, profil adresi ve giriş talimatları gönderilecek.</p>
            @endif
            <label for="notes">Notlar (İsteğe Bağlı)</label>
            <textarea class="form-control mb-3" id="notes" name="notes" maxlength="5000">{{ old('notes', $notes) }}</textarea>
            <button class="btn btn-{{ $request->delete_account ? 'danger' : 'success' }}" type="submit" name="confirmed" value="1">{{ $request->delete_account ? 'Onaylıyorum, Hesabı Kalıcı Olarak Sil' : 'Onaylıyorum, E-postayı Kaydet ve Şifre Bağlantısı Gönder' }}</button>
            <a class="btn btn-secondary" href="{{ route('admin.account-recovery.show', ['id' => $request->id, 'search' => $user->slug]) }}">Geri Dön / Hesabı Değiştir</a>
        </form>
    </div></div>
</main>
@endsection
