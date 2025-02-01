<?php
@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - 404')
@section('body-class', 'home')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<section class="home">
		<main id="content">
			
			
			<div class="container-lg">
				<article class="entry-wrapper mb-5">
					<h1 class="text-center mb-3 mt-5">404</h1>
					
					<p class="text-center">The link you clicked may be broken or the page may have been removed.<br>
						visit the <a href="/index.php">Homepage</a> or <a href="contact.html">Contact us</a> about the problem
					</p>
				</article> <!--entry-content-->
			</div> <!--container-->
		</main>
	</section>
@endsection
