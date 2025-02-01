@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - Yazarlar')

@section('body-class', 'archive')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}

	<main id="content">
		<div class="container-lg">
			<h1 class="text-center mb-4">Yazarlar</h1>
			
			<!-- Filter Buttons -->
			<div class="filter-buttons mb-4 text-center">
				<a href="{{ route('users.letter', ['filter' => 'yeni']) }}"
				   class="btn {{ $filter === 'yeni' ? 'btn-primary' : 'btn-outline-primary' }}">YENİ</a>
				<a href="{{ route('users.letter', ['filter' => 'tumu']) }}"
				   class="btn {{ $filter === 'tumu' ? 'btn-primary' : 'btn-outline-primary' }}">Tümü</a>
				@foreach(['A','B','C','Ç','D','E','F','G','H','I','İ','J','K','L','M','N','O','Ö','P','R','S','Ş','T','U','Ü','V','Y','Z'] as $letter)
					<a href="{{ route('users.letter', ['filter' => $letter]) }}"
					   class="btn {{ $filter === $letter ? 'btn-primary' : 'btn-outline-primary' }}">{{ $letter }}</a>
				@endforeach
			</div>
			
			<!-- users Grid -->
			<div class="row">
				@foreach($users as $user)
					<div class="col-md-4 mb-4">
						<div class="author-card">
							<div class="author-image mb-3 position-relative">
								<a href="{{ url('/yazar/' . $user->slug) }}">{!! \App\Helpers\MyHelper::generateInitialsAvatar($user->avatar, $user->name, 'width: 100px; height: 100px; object-fit: cover;', 'img-fluid rounded-circle', 'ai-image') !!}
								</a>
							</div>
							
							<h4><a href="{{ url('/yazar/' . $user->slug) }}">{{ $user->name }}</a></h4>
							<p>{{ Str::limit($user->page_title, 100) }}</p>
							
							<!-- Recent articles -->
							<div class="recent-articles">
								<h5>Son Yapıtları</h5>
								<ul>
									@foreach($user->articles()->where('approved', 1)->where('deleted', 0)->orderBy('created_at', 'DESC')->limit(3)->get() as $article)
										<li>
											<a href="{{ url('/yapit/' . $article->slug) }}">{{ $article->title }}</a>
											<small class="text-muted">
												({{ \App\Helpers\MyHelper::timeElapsedString($article->created_at) }})
											</small>
										</li>
									@endforeach
								</ul>
							</div>
						</div>
					</div>
				@endforeach
			</div>
			
			<!-- Pagination -->
			@php
				$currentPage = $users->currentPage();
				$lastPage = $users->lastPage();
				$totalItems = $users->total();
				$perPage = $users->perPage();
			@endphp
			
			@if($lastPage > 1)
				<div class="pagination-container">
					<div class="pagination">
						<div class="pagination-info">
							<span>{{ $totalItems }} yazı içinden {{ ($currentPage - 1) * $perPage + 1 }}-{{ min($currentPage * $perPage, $totalItems) }} arası görüntüleniyor</span>
						</div>
						<ul class="page-numbers">
							@if($currentPage > 1)
								<li>
									<a class="prev page-numbers"
									   href="{{ url('/yazarlar/harf/' . $filter .  '/sayfa/' . ($currentPage - 1)) }}">
										<i class="icon-left-open-big"></i>
									</a>
								</li>
							@endif
							
							@for($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++)
								<li>
									<a class="page-numbers {{ $i == $currentPage ? 'current' : '' }}"
									   href="{{ url('/yazarlar/harf/' . $filter .  '/sayfa/' . $i) }}">
										{{ $i }}
									</a>
								</li>
							@endfor
							
							@if($currentPage < $lastPage)
								<li>
									<a class="next page-numbers"
									   href="{{ url('/yazarlar/harf/' . $filter .  '/sayfa/' . ($currentPage + 1)) }}">
										<i class="icon-right-open-big"></i>
									</a>
								</li>
							@endif
						</ul>
					</div>
				</div>
			@endif
			<!-- End Pagination -->
		</div>
	</main>
@endsection

@push('styles')
	<style>
      .ai-image {
          position: absolute;
          bottom: 0;
          right: 40%;
          transform: translateX(50%);
          background-color: rgba(0, 0, 0, 0.8);
          color: #aaa;
          padding: 2px 6px;
          border-radius: 50%;
          font-size: 0.7em;
      }
      .filter-buttons .btn {
          margin: 2px;
          padding: 5px 10px;
      }
      .author-card {
          border: 1px solid #ddd;
          padding: 20px;
          border-radius: 5px;
          height: 100%;
          text-align: center;
      }
      .recent-articles {
          margin-top: 20px;
          font-size: 0.9em;
          text-align: left;
      }
      .recent-articles ul {
          list-style: none;
          padding-left: 0;
      }
      .recent-articles li {
          margin-bottom: 5px;
      }
	</style>
@endpush
