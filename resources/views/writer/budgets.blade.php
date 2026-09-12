@extends('writer.layouts.writer')
@section('title', 'Yapay zekâ kotaları')
@section('content')
<main class="settings-page"><h1>Yapay zekâ kotaları</h1><p>Yenileme tutarı: ${{ \App\Writer\Support\Money::display(\App\Writer\Services\DemoBudget::allowance()) }}. Yenileme, geçmiş harcamayı korur ve kullanılabilir kotayı %100'e getirir.</p>
<form method="get" class="row"><label>Üye ara<input name="search" value="{{ $search }}"></label><button>Ara</button></form>
<div style="overflow-x:auto"><table class="budget-table"><thead><tr><th>Üye</th><th>Toplam AI (USD)</th><th>Harcanan demo (USD)</th><th>Bekleyen (USD)</th><th>Toplam limit (USD)</th><th>Kalan (USD)</th><th>Kota</th><th></th></tr></thead><tbody>
@foreach($users as $user)<tr><td>{{ $user->name }}<br><small>{{ $user->email }}</small></td><td>${{ \App\Writer\Support\Money::display($user->writer_total_spent) }}</td><td>${{ \App\Writer\Support\Money::display($user->demo_spent) }}</td><td>${{ \App\Writer\Support\Money::display($user->demo_reserved) }}</td><td>${{ \App\Writer\Support\Money::display(\App\Writer\Services\DemoBudget::limit($user)) }}</td><td>${{ \App\Writer\Support\Money::display(\App\Writer\Services\DemoBudget::remaining($user)) }}</td><td>{{ \App\Writer\Services\DemoBudget::percentage($user) }}%</td><td><form method="post" action="{{ route('writer.budgets.reset',$user) }}">@csrf<button>%100'e yenile</button></form></td></tr>@endforeach
</tbody></table></div>{{ $users->links('pagination::simple-default') }}</main>
@endsection
