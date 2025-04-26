<!doctype html>
<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="layout-menu-fixed layout-compact"
  data-theme="light"
  data-assets-path="{{ asset('assets/') }}"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | {{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Theme Switcher CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/theme-switcher.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Page CSS -->
    @yield('page-css')

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        @include('layouts.sidebar')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          @include('layouts.navbar')
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              @yield('content')
            </div>
            <!-- / Content -->

            <!-- Footer -->
            @include('layouts.footer')
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

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Dark Mode Toggle JS -->
    <script>
      (function() {
        // Get the theme switcher elements
        const themeSwitchers = document.querySelectorAll('.dropdown-styles a[data-theme]');
        
        // Theme icons
        const themeIcons = {
          light: 'bx-sun',
          dark: 'bx-moon',
          system: 'bx-desktop'
        };

        // Function to update the icon in the navbar
        function updateNavbarIcon(theme) {
          const iconElement = document.querySelector('.dropdown-style-switcher i.bx');
          // Remove all theme icons
          iconElement.classList.remove('bx-sun', 'bx-moon', 'bx-desktop');
          // Add the relevant icon
          iconElement.classList.add(themeIcons[theme]);
        }

        // Function to update the active theme in the dropdown
        function updateActiveTheme(theme) {
          // Remove active class from all items
          themeSwitchers.forEach(item => {
            item.classList.remove('active');
          });
          
          // Add active class to selected theme
          const activeTheme = document.querySelector(`.dropdown-styles a[data-theme="${theme}"]`);
          if (activeTheme) {
            activeTheme.classList.add('active');
          }
        }

        // Function to set the theme
        function setTheme(theme) {
          // Save to localStorage
          localStorage.setItem('theme', theme);
          
          // Update the navbar icon
          updateNavbarIcon(theme);
          
          // Update active class in dropdown
          updateActiveTheme(theme);
          
          const html = document.querySelector('html');
          
          if (theme === 'system') {
            // Check system preference
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            html.setAttribute('data-theme', systemTheme);
            // Update body class for dark mode
            if (systemTheme === 'dark') {
              document.body.classList.add('dark-style');
            } else {
              document.body.classList.remove('dark-style');
            }
          } else {
            // Set theme directly
            html.setAttribute('data-theme', theme);
            // Update body class for dark mode
            if (theme === 'dark') {
              document.body.classList.add('dark-style');
            } else {
              document.body.classList.remove('dark-style');
            }
          }
        }
        
        // Add click handlers to all theme switchers
        themeSwitchers.forEach(function(switcher) {
          switcher.addEventListener('click', function(e) {
            e.preventDefault();
            const theme = this.getAttribute('data-theme');
            setTheme(theme);
          });
        });
        
        // Set the initial theme from localStorage or default to system
        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme);
        
        // Listen for system theme changes if using system theme
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
          if (localStorage.getItem('theme') === 'system') {
            setTheme('system');
          }
        });
      })();
    </script>

    <!-- Page JS -->
    @yield('page-js')
  </body>
</html>
