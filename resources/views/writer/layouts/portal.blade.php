@php app()->setLocale(strtolower(substr(app()->getLocale(), 0, 2))); @endphp
@extends('writer.layouts.writer')
@section('portal', 'yes')
@section('content')
    <div class="portal-content">@yield('portal-content')</div>
@endsection
