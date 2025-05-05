<?php $__env->startSection('title', 'Theme Settings'); ?>

<?php $__env->startSection('page-css'); ?>
<style>
  .theme-option-preview {
    height: 160px;
    border-radius: 0.5rem;
    border: 1px solid #d4d8dd;
    position: relative;
    overflow: hidden;
    margin-bottom: 1rem;
  }
  
  .theme-preview-header {
    height: 30px;
    padding: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: var(--bs-primary);
  }
  
  .theme-preview-header .preview-dots {
    display: flex;
    gap: 4px;
  }
  
  .theme-preview-header .preview-dots .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.7);
  }
  
  .theme-preview-body {
    display: flex;
    height: calc(100% - 30px);
  }
  
  .theme-preview-sidebar {
    width: 25%;
    height: 100%;
    background-color: #f5f5f9;
  }
  
  .rtl .theme-preview-sidebar {
    border-right: none;
    border-left: 1px solid #d4d8dd;
  }
  
  .theme-preview-content {
    width: 75%;
    height: 100%;
    padding: 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  
  .theme-preview-line {
    height: 8px;
    border-radius: 4px;
    background-color: #e7e7e8;
    margin-bottom: 2px;
  }
  
  .theme-preview-line.sm {
    width: 50%;
  }
  
  .theme-preview-box {
    height: 40px;
    border-radius: 4px;
    background-color: #e7e7e8;
    margin-top: auto;
  }
  
  .dark-mode .theme-preview-sidebar {
    background-color: #2b2c40;
  }
  
  .dark-mode .theme-preview-line,
  .dark-mode .theme-preview-box {
    background-color: #444564;
  }
  
  .option-card {
    cursor: pointer;
    transition: all 0.2s ease;
  }
  
  .option-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.45);
  }
  
  .active-option {
    border-color: var(--bs-primary) !important;
  }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Settings /</span> Theme
</h4>

<div class="row">
  <!-- Theme Mode -->
  <div class="col-12 mb-4">
    <div class="card">
      <h5 class="card-header">Display Mode</h5>
      <div class="card-body">
        <div class="row">
          <!-- Light Mode -->
          <div class="col-md-4 mb-3">
            <div class="card option-card <?php echo e($themeConfig['mode'] === 'light' ? 'active-option' : ''); ?>" id="light-mode-option">
              <div class="card-body p-3">
                <div class="theme-option-preview">
                  <div class="theme-preview-header">
                    <div class="preview-dots">
                      <div class="dot"></div>
                      <div class="dot"></div>
                      <div class="dot"></div>
                    </div>
                  </div>
                  <div class="theme-preview-body">
                    <div class="theme-preview-sidebar"></div>
                    <div class="theme-preview-content">
                      <div class="theme-preview-line"></div>
                      <div class="theme-preview-line sm"></div>
                      <div class="theme-preview-line"></div>
                      <div class="theme-preview-box"></div>
                    </div>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <h6 class="mb-0">Light Mode</h6>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="theme-mode" id="light-mode-radio" value="light" <?php echo e($themeConfig['mode'] === 'light' ? 'checked' : ''); ?>>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Dark Mode -->
          <div class="col-md-4 mb-3">
            <div class="card option-card <?php echo e($themeConfig['mode'] === 'dark' ? 'active-option' : ''); ?>" id="dark-mode-option">
              <div class="card-body p-3">
                <div class="theme-option-preview dark-mode">
                  <div class="theme-preview-header" style="background-color: #696cff;">
                    <div class="preview-dots">
                      <div class="dot"></div>
                      <div class="dot"></div>
                      <div class="dot"></div>
                    </div>
                  </div>
                  <div class="theme-preview-body" style="background-color: #2b2c40;">
                    <div class="theme-preview-sidebar"></div>
                    <div class="theme-preview-content">
                      <div class="theme-preview-line"></div>
                      <div class="theme-preview-line sm"></div>
                      <div class="theme-preview-line"></div>
                      <div class="theme-preview-box"></div>
                    </div>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <h6 class="mb-0">Dark Mode</h6>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="theme-mode" id="dark-mode-radio" value="dark" <?php echo e($themeConfig['mode'] === 'dark' ? 'checked' : ''); ?>>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- System Mode -->
          <div class="col-md-4 mb-3">
            <div class="card option-card <?php echo e($themeConfig['mode'] === 'system' ? 'active-option' : ''); ?>" id="system-mode-option">
              <div class="card-body p-3">
                <div class="theme-option-preview">
                  <div class="theme-preview-header">
                    <div class="preview-dots">
                      <div class="dot"></div>
                      <div class="dot"></div>
                      <div class="dot"></div>
                    </div>
                  </div>
                  <div class="theme-preview-body" style="background: linear-gradient(to right, white 50%, #2b2c40 50%);">
                    <div class="theme-preview-sidebar" style="background: linear-gradient(to right, #f5f5f9 50%, #2b2c40 50%);"></div>
                    <div class="theme-preview-content">
                      <div class="theme-preview-line" style="background: linear-gradient(to right, #e7e7e8 50%, #444564 50%);"></div>
                      <div class="theme-preview-line sm" style="background: linear-gradient(to right, #e7e7e8 50%, #444564 50%);"></div>
                      <div class="theme-preview-line" style="background: linear-gradient(to right, #e7e7e8 50%, #444564 50%);"></div>
                      <div class="theme-preview-box" style="background: linear-gradient(to right, #e7e7e8 50%, #444564 50%);"></div>
                    </div>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <h6 class="mb-0">System Mode</h6>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="theme-mode" id="system-mode-radio" value="system" <?php echo e($themeConfig['mode'] === 'system' ? 'checked' : ''); ?>>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Text Direction -->
  <div class="col-12 mb-4">
    <div class="card">
      <h5 class="card-header">Text Direction</h5>
      <div class="card-body">
        <div class="row">
          <!-- LTR Direction -->
          <div class="col-md-6 mb-3">
            <div class="card option-card <?php echo e(!($themeConfig['is_rtl'] ?? false) ? 'active-option' : ''); ?>" id="ltr-option">
              <div class="card-body p-3">
                <div class="theme-option-preview">
                  <div class="theme-preview-header">
                    <div class="preview-dots">
                      <div class="dot"></div>
                      <div class="dot"></div>
                      <div class="dot"></div>
                    </div>
                  </div>
                  <div class="theme-preview-body">
                    <div class="theme-preview-sidebar" style="border-right: 1px solid #d4d8dd;"></div>
                    <div class="theme-preview-content">
                      <div class="theme-preview-line"></div>
                      <div class="theme-preview-line" style="width: 75%; margin-left: 0;"></div>
                      <div class="theme-preview-line" style="width: 65%; margin-left: 0;"></div>
                      <div class="theme-preview-box"></div>
                    </div>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <h6 class="mb-0">LTR Direction</h6>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="direction" id="ltr-radio" value="ltr" <?php echo e(!($themeConfig['is_rtl'] ?? false) ? 'checked' : ''); ?>>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- RTL Direction -->
          <div class="col-md-6 mb-3">
            <div class="card option-card <?php echo e(($themeConfig['is_rtl'] ?? false) ? 'active-option' : ''); ?>" id="rtl-option">
              <div class="card-body p-3">
                <div class="theme-option-preview rtl">
                  <div class="theme-preview-header">
                    <div class="preview-dots" style="margin-right: auto; margin-left: 0;">
                      <div class="dot"></div>
                      <div class="dot"></div>
                      <div class="dot"></div>
                    </div>
                  </div>
                  <div class="theme-preview-body">
                    <div class="theme-preview-content">
                      <div class="theme-preview-line"></div>
                      <div class="theme-preview-line" style="width: 75%; margin-right: 0; margin-left: auto;"></div>
                      <div class="theme-preview-line" style="width: 65%; margin-right: 0; margin-left: auto;"></div>
                      <div class="theme-preview-box"></div>
                    </div>
                    <div class="theme-preview-sidebar" style="border-left: 1px solid #d4d8dd;"></div>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <h6 class="mb-0">RTL Direction</h6>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="direction" id="rtl-radio" value="rtl" <?php echo e(($themeConfig['is_rtl'] ?? false) ? 'checked' : ''); ?>>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Menu Options -->
  <div class="col-12 mb-4">
    <div class="card">
      <h5 class="card-header">Menu Options</h5>
      <div class="card-body">
        <div class="row">
          <!-- Expanded Menu -->
          <div class="col-md-6 mb-3">
            <div class="card option-card <?php echo e(!($themeConfig['menu_collapsed'] ?? false) ? 'active-option' : ''); ?>" id="expanded-menu-option">
              <div class="card-body p-3">
                <div class="theme-option-preview">
                  <div class="theme-preview-header">
                    <div class="preview-dots">
                      <div class="dot"></div>
                      <div class="dot"></div>
                      <div class="dot"></div>
                    </div>
                  </div>
                  <div class="theme-preview-body">
                    <div class="theme-preview-sidebar" style="width: 25%; border-right: 1px solid #d4d8dd;"></div>
                    <div class="theme-preview-content" style="width: 75%;">
                      <div class="theme-preview-line"></div>
                      <div class="theme-preview-line sm"></div>
                      <div class="theme-preview-line"></div>
                      <div class="theme-preview-box"></div>
                    </div>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <h6 class="mb-0">Expanded Menu</h6>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="menu-collapsed" id="expanded-menu-radio" value="false" <?php echo e(!($themeConfig['menu_collapsed'] ?? false) ? 'checked' : ''); ?>>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Collapsed Menu -->
          <div class="col-md-6 mb-3">
            <div class="card option-card <?php echo e(($themeConfig['menu_collapsed'] ?? false) ? 'active-option' : ''); ?>" id="collapsed-menu-option">
              <div class="card-body p-3">
                <div class="theme-option-preview">
                  <div class="theme-preview-header">
                    <div class="preview-dots">
                      <div class="dot"></div>
                      <div class="dot"></div>
                      <div class="dot"></div>
                    </div>
                  </div>
                  <div class="theme-preview-body">
                    <div class="theme-preview-sidebar" style="width: 10%; border-right: 1px solid #d4d8dd;"></div>
                    <div class="theme-preview-content" style="width: 90%;">
                      <div class="theme-preview-line"></div>
                      <div class="theme-preview-line sm"></div>
                      <div class="theme-preview-line"></div>
                      <div class="theme-preview-box"></div>
                    </div>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <h6 class="mb-0">Collapsed Menu</h6>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="menu-collapsed" id="collapsed-menu-radio" value="true" <?php echo e(($themeConfig['menu_collapsed'] ?? false) ? 'checked' : ''); ?>>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Theme Mode
    const themeOptions = document.querySelectorAll('#light-mode-option, #dark-mode-option, #system-mode-option');
    const modeRadios = document.querySelectorAll('input[name="theme-mode"]');
    
    themeOptions.forEach(option => {
      option.addEventListener('click', function() {
        const mode = this.id.replace('-mode-option', '');
        document.getElementById(`${mode}-mode-radio`).checked = true;
        updateThemeMode(mode);
      });
    });
    
    modeRadios.forEach(radio => {
      radio.addEventListener('change', function() {
        updateThemeMode(this.value);
      });
    });
    
    function updateThemeMode(mode) {
      themeOptions.forEach(option => {
        option.classList.remove('active-option');
      });
      
      document.getElementById(`${mode}-mode-option`).classList.add('active-option');
      
      // Update via API
      fetch('/theme/mode', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ mode: mode })
      })
      .then(response => response.json())
      .then(data => {
        ThemeManager.setThemeMode(mode);
      })
      .catch(error => console.error('Error updating theme mode:', error));
    }
    
    // Direction
    const directionOptions = document.querySelectorAll('#ltr-option, #rtl-option');
    const directionRadios = document.querySelectorAll('input[name="direction"]');
    
    directionOptions.forEach(option => {
      option.addEventListener('click', function() {
        const direction = this.id.replace('-option', '');
        document.getElementById(`${direction}-radio`).checked = true;
        updateDirection(direction === 'rtl');
      });
    });
    
    directionRadios.forEach(radio => {
      radio.addEventListener('change', function() {
        updateDirection(this.value === 'rtl');
      });
    });
    
    function updateDirection(isRtl) {
      directionOptions.forEach(option => {
        option.classList.remove('active-option');
      });
      
      document.getElementById(`${isRtl ? 'rtl' : 'ltr'}-option`).classList.add('active-option');
      
      // Update via API
      fetch('/theme/rtl', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ is_rtl: isRtl.toString() })
      })
      .then(response => response.json())
      .then(data => {
        ThemeManager.setRTLMode(isRtl);
      })
      .catch(error => console.error('Error updating direction:', error));
    }
    
    // Menu Collapsed
    const menuOptions = document.querySelectorAll('#expanded-menu-option, #collapsed-menu-option');
    const menuRadios = document.querySelectorAll('input[name="menu-collapsed"]');
    
    menuOptions.forEach(option => {
      option.addEventListener('click', function() {
        const isCollapsed = this.id === 'collapsed-menu-option';
        document.getElementById(`${isCollapsed ? 'collapsed' : 'expanded'}-menu-radio`).checked = true;
        updateMenuCollapsed(isCollapsed);
      });
    });
    
    menuRadios.forEach(radio => {
      radio.addEventListener('change', function() {
        updateMenuCollapsed(this.value === 'true');
      });
    });
    
    function updateMenuCollapsed(isCollapsed) {
      menuOptions.forEach(option => {
        option.classList.remove('active-option');
      });
      
      document.getElementById(`${isCollapsed ? 'collapsed' : 'expanded'}-menu-option`).classList.add('active-option');
      
      // Update via API
      fetch('/theme/menu-collapsed', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ is_collapsed: isCollapsed.toString() })
      })
      .then(response => response.json())
      .then(data => {
        ThemeManager.setMenuCollapsed(isCollapsed);
      })
      .catch(error => console.error('Error updating menu collapsed state:', error));
    }
  });
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/club/resources/views/pages/theme-settings.blade.php ENDPATH**/ ?>