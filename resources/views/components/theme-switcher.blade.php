<div class="dropdown-menu dropdown-menu-end py-0" aria-labelledby="theme-switcher">
  <div class="dropdown-menu-header border-bottom">
    <div class="dropdown-header d-flex align-items-center py-3">
      <h5 class="text-body mb-0 me-auto">Theme</h5>
    </div>
  </div>
  <div class="dropdown-divider my-0"></div>
  <div class="dropdown-menu-content p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <span class="fw-semibold">Light</span>
      <button 
        type="button" 
        class="btn btn-icon rounded-pill btn-outline-{{ $themeConfig['mode'] === 'light' ? 'primary' : 'secondary' }} mode-button" 
        data-theme-mode="light">
        <i class="bx bx-{{ $themeConfig['mode'] === 'light' ? 'check' : '' }} d-block d-sm-none"></i>
        <span class="d-none d-sm-block">Light</span>
      </button>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <span class="fw-semibold">Dark</span>
      <button 
        type="button" 
        class="btn btn-icon rounded-pill btn-outline-{{ $themeConfig['mode'] === 'dark' ? 'primary' : 'secondary' }} mode-button" 
        data-theme-mode="dark">
        <i class="bx bx-{{ $themeConfig['mode'] === 'dark' ? 'check' : '' }} d-block d-sm-none"></i>
        <span class="d-none d-sm-block">Dark</span>
      </button>
    </div>
    <div class="d-flex justify-content-between align-items-center">
      <span class="fw-semibold">System</span>
      <button 
        type="button" 
        class="btn btn-icon rounded-pill btn-outline-{{ $themeConfig['mode'] === 'system' ? 'primary' : 'secondary' }} mode-button" 
        data-theme-mode="system">
        <i class="bx bx-{{ $themeConfig['mode'] === 'system' ? 'check' : '' }} d-block d-sm-none"></i>
        <span class="d-none d-sm-block">System</span>
      </button>
    </div>
  </div>
</div> 