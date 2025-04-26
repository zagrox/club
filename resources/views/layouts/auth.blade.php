@extends('layouts.base')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />
@endsection

@section('full-content')
<!-- Content -->
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">
      @yield('auth-content')
    </div>
  </div>
</div>
<!-- / Content -->
@endsection 