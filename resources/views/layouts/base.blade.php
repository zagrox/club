<!DOCTYPE html>
<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="layout-{{ ($themeConfig['menu_collapsed'] ?? false) ? 'compact' : 'expanded' }}"
  data-assets-path="{{ $themeConfig['assets_path'] ?? 'assets' }}/"
  data-template="{{ $themeConfig['template'] ?? 'vertical-menu-template-free' }}"
  data-theme="{{ $themeConfig['mode'] ?? 'light' }}"
  @if($themeConfig['is_rtl'] ?? false) dir="rtl" @endif>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | {{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="@yield('meta_description', '')" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <!-- Core CSS Files -->
    <x-theme-css />

    <!-- Page CSS -->
    @yield('page-css')

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}" defer></script>
    <script src="{{ asset('assets/js/config.js') }}" defer></script>
    
    <!-- Fix for 'kl' variable conflict -->
    <script>
      // Clean up any existing variables that might cause conflicts
      if (typeof window.kl !== 'undefined') {
        delete window.kl;
      }
    </script>
    
    <!-- Additional Head Content -->
    @yield('head')
  </head>

  <body>
    @if(View::hasSection('full-content'))
      @yield('full-content')
    @else
      <!-- Layout wrapper -->
      <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
          <!-- Menu -->
          @includeIf('layouts.sidebar')
          <!-- / Menu -->

          <!-- Layout container -->
          <div class="layout-page">
            <!-- Navbar -->
            @includeIf('layouts.navbar')
            <!-- / Navbar -->

            <!-- Content wrapper -->
            <div class="content-wrapper">
              <!-- Content -->
              <div class="container-xxl flex-grow-1 container-p-y">
                @yield('content')
              </div>
              <!-- / Content -->

              <!-- Footer -->
              @includeIf('layouts.footer')
              <!-- / Footer -->

              <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
          </div>
          <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
      </div>
      <!-- / Layout wrapper -->
    @endif

    <!-- Modals -->
    @yield('modals')
    <!-- / Modals -->

    <!-- Core JS Files -->
    <x-theme-js />

    <!-- Page JS -->
    @yield('page-js')
  </body>
</html> 