@php
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Auth;
@endphp
<nav
  class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
  id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center">
      <div class="nav-item d-flex align-items-center">
        <i class="bx bx-search fs-4 lh-0"></i>
        <input
          type="text"
          class="form-control border-0 shadow-none ps-1"
          placeholder="{{ __('messages.search') }}..."
          aria-label="{{ __('messages.search') }}..." />
      </div>
    </div>
    <!-- /Search -->

    <ul class="navbar-nav flex-row align-items-center ms-auto">

      <!-- Theme Settings -->
      <li class="nav-item me-2 me-xl-0">
        <a class="nav-link nav-icon" href="{{ route('theme.settings') }}" aria-expanded="false">
          <i class="bx bx-cog bx-sm"></i>
        </a>
      </li>
      <!-- /Theme Settings -->
      
      <!-- Language Selector -->
      <li class="nav-item dropdown me-2 me-xl-0">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bx bx-globe bx-sm"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            <li>
              <a class="dropdown-item {{ App::getLocale() == $localeCode ? 'active' : '' }}" 
                href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" 
                rel="alternate" 
                hreflang="{{ $localeCode }}">
                {{ $properties['native'] }}
              </a>
            </li>
          @endforeach
        </ul>
      </li>
      <!-- /Language Selector -->

      <!-- Notification -->
      <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
          <i class="bx bx-bell bx-sm"></i>
          @php
            $standardUnreadCount = Auth::check() ? Auth::user()->unreadNotifications->count() : 0;
            $walletUnreadCount = Auth::check() ? Auth::user()->walletNotifications()->whereNull('read_at')->count() : 0;
            $unreadCount = $standardUnreadCount + $walletUnreadCount;
          @endphp
          @if($unreadCount > 0)
            <span class="badge bg-danger rounded-pill badge-notifications">{{ $unreadCount }}</span>
          @endif
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0">
          <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
              <h5 class="text-body mb-0 me-auto">{{ __('messages.notifications') }}</h5>
              @if($unreadCount > 0)
                <a href="{{ route('inbox.markAllAsRead') }}" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('messages.view_all_notifications') }}">
                  <i class="bx fs-4 bx-envelope-open"></i>
                </a>
              @endif
            </div>
          </li>
          <li class="dropdown-notifications-list scrollable-container">
            <ul class="list-group list-group-flush">
              @if(Auth::check() && ($standardNotifications = Auth::user()->notifications->take(3))->isNotEmpty() || 
                  Auth::check() && ($walletNotifications = Auth::user()->walletNotifications()->limit(3)->get())->isNotEmpty())
                
                {{-- Standard notifications --}}
                @foreach($standardNotifications as $notification)
                  @php
                    $notificationData = $notification->data;
                    $isRead = $notification->read_at !== null;
                    $icon = $notificationData['icon'] ?? 'bx-bell';
                    $color = $notificationData['color'] ?? 'primary';
                  @endphp
                  <li class="list-group-item list-group-item-action dropdown-notifications-item {{ $isRead ? '' : 'marked-as-unread' }}">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-{{ $color }}">
                            <i class="bx {{ $icon }}"></i>
                          </span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $notificationData['title'] ?? 'اعلان جدید' }}</h6>
                        <p class="mb-0">{{ $notificationData['message'] ?? '' }}</p>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="{{ route('inbox.readAndRedirect', $notification->id) }}" class="dropdown-notifications-read">
                          <span class="badge badge-dot"></span>
                        </a>
                      </div>
                    </div>
                  </li>
                @endforeach
                
                {{-- Wallet notifications --}}
                @foreach($walletNotifications as $notification)
                  @php
                    $notificationData = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                    $isRead = $notification->read_at !== null;
                    $icon = $notificationData['icon'] ?? 'bx-wallet';
                    $color = $notificationData['color'] ?? 'primary';
                  @endphp
                  <li class="list-group-item list-group-item-action dropdown-notifications-item {{ $isRead ? '' : 'marked-as-unread' }}">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-{{ $color }}">
                            <i class="bx {{ $icon }}"></i>
                          </span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $notificationData['title'] ?? 'اعلان کیف پول' }}</h6>
                        <p class="mb-0">{{ $notificationData['message'] ?? '' }}</p>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="{{ route('inbox.readAndRedirect', ['id' => $notification->id, 'type' => 'wallet']) }}" class="dropdown-notifications-read">
                          <span class="badge badge-dot"></span>
                        </a>
                      </div>
                    </div>
                  </li>
                @endforeach
              @else
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                  <div class="d-flex">
                    <div class="flex-grow-1 text-center py-2">
                      <p class="mb-0">{{ __('messages.no_notifications') }}</p>
                    </div>
                  </div>
                </li>
              @endif
            </ul>
          </li>
          <li class="dropdown-menu-footer border-top">
            <a href="{{ route('inbox.index') }}" class="dropdown-item d-flex justify-content-center p-3">
              {{ __('messages.view_all_notifications') }}
            </a>
          </li>
        </ul>
      </li>
      <!-- /Notification -->

      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="#">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-medium d-block">@auth {{ Auth::user()->name }} @else John Doe @endauth</span>
                  <small class="text-muted">@auth {{ Auth::user()->email }} @else admin@example.com @endauth</small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('settings.account') }}">
              <i class="bx bx-user me-2"></i>
              <span class="align-middle">{{ __('messages.profile') }}</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('settings.account') }}">
              <i class="bx bx-cog me-2"></i>
              <span class="align-middle">{{ __('messages.settings') }}</span>
            </a>
          </li>
          @if(Auth::check() && Auth::user()->hasRole('admin'))
          <li>
            <a class="dropdown-item" href="{{ route('translations.index') }}">
              <i class="bx bx-globe me-2"></i>
              <span class="align-middle">{{ __('messages.manage_translations') }}</span>
            </a>
          </li>
          @endif
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            @auth
              <a class="dropdown-item" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bx bx-power-off me-2"></i>
                <span class="align-middle">{{ __('messages.logout') }}</span>
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
              </form>
            @else
              <a class="dropdown-item" href="{{ route('login') }}">
                <i class="bx bx-log-in me-2"></i>
                <span class="align-middle">{{ __('messages.login') }}</span>
              </a>
            @endauth
          </li>
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>
</nav> 