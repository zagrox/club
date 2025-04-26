@extends('layouts.base')

@section('title', 'Dashboard')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('content')
@yield('content')
@endsection

@section('page-js')
@yield('app-js')
@endsection
