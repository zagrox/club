<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('home') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <span class="text-primary">
          <svg
            width="25"
            viewBox="0 0 25 42"
            version="1.1"
            xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink">
            <defs>
              <path
                d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                id="path-1"></path>
              <path
                d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
                id="path-3"></path>
              <path
                d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
                id="path-4"></path>
              <path
                d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
                id="path-5"></path>
            </defs>
            <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                <g id="Icon" transform="translate(27.000000, 15.000000)">
                  <g id="Mask" transform="translate(0.000000, 8.000000)">
                    <mask id="mask-2" fill="white">
                      <use xlink:href="#path-1"></use>
                    </mask>
                    <use fill="currentColor" xlink:href="#path-1"></use>
                    <g id="Path-3" mask="url(#mask-2)">
                      <use fill="currentColor" xlink:href="#path-3"></use>
                      <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                    </g>
                    <g id="Path-4" mask="url(#mask-2)">
                      <use fill="currentColor" xlink:href="#path-4"></use>
                      <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                    </g>
                  </g>
                  <g
                    id="Triangle"
                    transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                    <use fill="currentColor" xlink:href="#path-5"></use>
                    <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                  </g>
                </g>
              </g>
            </g>
          </svg>
        </span>
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-2">{{ config('app.name', 'Mailzila') }}</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
    </a>
  </div>

  <div class="menu-divider mt-0"></div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
      <a href="{{ route('home') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Dashboard">Dashboard</div>
      </a>
    </li>

    <!-- Tools -->
    <li class="menu-item {{ request()->routeIs('tools') ? 'active' : '' }}">
      <a href="{{ route('tools') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-wrench"></i>
        <div data-i18n="Tools">Tools</div>
      </a>
    </li>

    
    <!-- Components -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Components</span>
    </li>
    
    <!-- Users List -->
    <li class="menu-item {{ request()->routeIs('users.list') ? 'active' : '' }}">
      <a href="{{ route('users.list') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user-check"></i>
        <div data-i18n="Users List">Users List</div>
      </a>
    </li>
    
    <!-- Orders List -->
    <li class="menu-item {{ request()->routeIs('orders.*') ? 'active open' : '' }}">
      <a href="{{ route('orders.list') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-package"></i>
        <div data-i18n="Orders List">Orders List</div>
      </a>
    </li>
    
    <!-- Notification Center -->
    <li class="menu-item {{ request()->routeIs('notification-center.*') ? 'active open' : '' }}">
      <a href="{{ route('notification-center.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-bell"></i>
        <div data-i18n="Notification Center">Notify Center</div>
      </a>
    </li>
    
    <!-- Change Logs -->
    <li class="menu-item {{ request()->routeIs('change-logs') ? 'active' : '' }}">
      <a href="{{ route('change-logs') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-git-branch"></i>
        <div data-i18n="Change Logs">Change Logs</div>
      </a>
    </li>
    
    <!-- API Resources -->
    <li class="menu-item {{ request()->routeIs('api.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-code-block"></i>
        <div data-i18n="API Resources">API Resources</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('api.docs') ? 'active' : '' }}">
          <a href="{{ route('api.docs') }}" class="menu-link">
            <div data-i18n="API Documentation">API Documentation</div>
          </a>
        </li>
      </ul>
    </li>
    
    <!-- Help Center -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Help Center</span>
    </li>
    
    <!-- FAQ -->
    <li class="menu-item {{ request()->routeIs('faq') ? 'active' : '' }}">
      <a href="{{ route('faq') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-help-circle"></i>
        <div data-i18n="FAQ">FAQ</div>
      </a>
    </li>
    
    <!-- Cards -->
    <li class="menu-item {{ request()->routeIs('cards.*') ? 'active' : '' }}">
      <a href="{{ route('cards.basic') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-collection"></i>
        <div data-i18n="Basic">Pricing Cards</div>
      </a>
    </li>
    
    <!-- Authentication -->
    <li class="menu-item {{ request()->routeIs('auth.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-lock-open-alt"></i>
        <div data-i18n="Authentications">Authentications</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('auth.login') ? 'active' : '' }}">
          <a href="{{ route('login') }}" class="menu-link">
            <div data-i18n="Login">Login</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('auth.register') ? 'active' : '' }}">
          <a href="{{ route('register') }}" class="menu-link">
            <div data-i18n="Register">Register</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('auth.forgot-password') ? 'active' : '' }}">
          <a href="{{ route('password.request') }}" class="menu-link">
            <div data-i18n="Forgot Password">Forgot Password</div>
          </a>
        </li>
      </ul>
    </li>


    <!-- UI Elements -->
    <li class="menu-item {{ request()->routeIs('ui.*') ? 'active open' : '' }}">
      <a href="javascript:void(0)" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-box"></i>
        <div data-i18n="User interface">User Interface</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('ui.accordion') ? 'active' : '' }}">
          <a href="{{ route('ui.accordion') }}" class="menu-link">
            <div data-i18n="Accordion">Accordion</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('ui.alerts') ? 'active' : '' }}">
          <a href="{{ route('ui.alerts') }}" class="menu-link">
            <div data-i18n="Alerts">Alerts</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('ui.buttons') ? 'active' : '' }}">
          <a href="{{ route('ui.buttons') }}" class="menu-link">
            <div data-i18n="Buttons">Buttons</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('ui.carousel') ? 'active' : '' }}">
          <a href="{{ route('ui.carousel') }}" class="menu-link">
            <div data-i18n="Carousel">Carousel</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('ui.collapse') ? 'active' : '' }}">
          <a href="{{ route('ui.collapse') }}" class="menu-link">
            <div data-i18n="Collapse">Collapse</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Layouts -->
    <li class="menu-item {{ request()->routeIs('layouts.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-layout"></i>
        <div data-i18n="Layouts">Layouts</div>
      </a>

      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('layouts.fluid') ? 'active' : '' }}">
          <a href="{{ route('layouts.fluid') }}" class="menu-link">
            <div data-i18n="Fluid">Fluid</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('layouts.container') ? 'active' : '' }}">
          <a href="{{ route('layouts.container') }}" class="menu-link">
            <div data-i18n="Container">Templates</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('layouts.without-menu') ? 'active' : '' }}">
          <a href="{{ route('layouts.without-menu') }}" class="menu-link">
            <div data-i18n="Without menu">Campaigns</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('layouts.without-navbar') ? 'active' : '' }}">
          <a href="{{ route('layouts.without-navbar') }}" class="menu-link">
            <div data-i18n="Without navbar">Activities</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('layouts.blank') ? 'active' : '' }}">
          <a href="{{ route('layouts.blank') }}" class="menu-link">
            <div data-i18n="Blank">Forms</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Forms -->
    <li class="menu-item {{ request()->routeIs('forms.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-detail"></i>
        <div data-i18n="Form Elements">Tutorials</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('forms.basic-inputs') ? 'active' : '' }}">
          <a href="{{ route('forms.basic-inputs') }}" class="menu-link">
            <div data-i18n="Basic Inputs">Documentation</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('forms.input-groups') ? 'active' : '' }}">
          <a href="{{ route('forms.input-groups') }}" class="menu-link">
            <div data-i18n="Input groups">Media Library</div>
          </a>
        </li>
      </ul>
    </li>
    
    <!-- Form Layouts -->
    <li class="menu-item {{ request()->routeIs('form-layouts.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-cube-alt"></i>
        <div data-i18n="Form Layouts">Support</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('form-layouts.vertical') ? 'active' : '' }}">
          <a href="{{ route('form-layouts.vertical') }}" class="menu-link">
            <div data-i18n="Vertical Form">Tickets</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('form-layouts.horizontal') ? 'active' : '' }}">
          <a href="{{ route('form-layouts.horizontal') }}" class="menu-link">
            <div data-i18n="Horizontal Form">Live Chat</div>
          </a>
        </li>
      </ul>
    </li>
    
    <!-- Tables -->
    <li class="menu-item {{ request()->routeIs('tables.*') ? 'active' : '' }}">
      <a href="{{ route('tables.basic') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-table"></i>
        <div data-i18n="Tables">Tables</div>
      </a>
    </li>
    
    <!-- Customization -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">User Options</span>
    </li>
    
    <!-- Account Settings -->
    <li class="menu-item {{ request()->routeIs('settings.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-user"></i>
        <div data-i18n="Account Settings">Account Settings</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('settings.account') ? 'active' : '' }}">
          <a href="{{ route('settings.account') }}" class="menu-link">
            <div data-i18n="Account">Account</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('settings.security') ? 'active' : '' }}">
          <a href="{{ route('settings.security') }}" class="menu-link">
            <div data-i18n="Security">Security</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('settings.notifications') ? 'active' : '' }}">
          <a href="{{ route('settings.notifications') }}" class="menu-link">
            <div data-i18n="Notifications">Notifications</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('settings.connections') ? 'active' : '' }}">
          <a href="{{ route('settings.connections') }}" class="menu-link">
            <div data-i18n="Connections">Connections</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Theme Settings -->
    <li class="menu-item {{ request()->routeIs('theme.*') ? 'active' : '' }}">
      <a href="{{ route('theme.settings') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-palette"></i>
        <div data-i18n="Theme Settings">Theme Settings</div>
      </a>
    </li>

 
  </ul>
</aside> 