/**
 * Theme Manager - Handles theme mode (light/dark/system) and layout settings
 */
 
const ThemeManager = (function() {
  'use strict';
  
  // DOM elements
  const html = document.documentElement;
  
  // Constants
  const THEME_KEY = 'sneat-theme-mode';
  const RTL_KEY = 'sneat-is-rtl';
  const MENU_COLLAPSED_KEY = 'sneat-menu-collapsed';
  
  // API endpoints
  const API_ROUTES = {
    mode: '/theme/mode',
    rtl: '/theme/rtl',
    menuCollapsed: '/theme/menu-collapsed'
  };
  
  /**
   * Initialize the theme manager
   */
  function init() {
    // Apply stored theme or use default
    applyStoredThemeMode();
    
    // Apply stored RTL setting
    applyStoredRTLSetting();
    
    // Apply stored menu collapsed state
    applyStoredMenuCollapsedState();
    
    // Add event listeners for theme controls
    bindEventListeners();
  }
  
  /**
   * Apply stored theme mode or use default
   */
  function applyStoredThemeMode() {
    const storedMode = localStorage.getItem(THEME_KEY) || 'light';
    
    if (storedMode === 'system') {
      const systemMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      applyThemeMode(systemMode);
      
      // Watch for changes in system preference
      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (localStorage.getItem(THEME_KEY) === 'system') {
          applyThemeMode(e.matches ? 'dark' : 'light');
        }
      });
    } else {
      applyThemeMode(storedMode);
    }
  }
  
  /**
   * Apply theme mode (light/dark)
   * @param {string} mode - 'light' or 'dark'
   */
  function applyThemeMode(mode) {
    if (mode === 'dark') {
      html.setAttribute('data-theme', 'dark');
      document.querySelector('html').classList.add('dark-style');
      document.querySelector('html').classList.remove('light-style');
      
      // Update icon in navbar if it exists
      const themeIcon = document.getElementById('theme-icon');
      if (themeIcon) {
        themeIcon.innerHTML = '<i class="bx bx-moon"></i>';
      }
    } else {
      html.setAttribute('data-theme', 'light');
      document.querySelector('html').classList.add('light-style');
      document.querySelector('html').classList.remove('dark-style');
      
      // Update icon in navbar if it exists
      const themeIcon = document.getElementById('theme-icon');
      if (themeIcon) {
        themeIcon.innerHTML = '<i class="bx bx-sun"></i>';
      }
    }
  }
  
  /**
   * Set theme mode and store it
   * @param {string} mode - 'light', 'dark', or 'system'
   */
  function setThemeMode(mode) {
    localStorage.setItem(THEME_KEY, mode);
    
    // Update via API
    fetch(API_ROUTES.mode, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ mode: mode })
    }).catch(error => console.error('Error updating theme mode via API:', error));
    
    // Update icon for system mode
    const themeIcon = document.getElementById('theme-icon');
    if (themeIcon && mode === 'system') {
      themeIcon.innerHTML = '<i class="bx bx-desktop"></i>';
    }
    
    if (mode === 'system') {
      const systemMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      applyThemeMode(systemMode);
    } else {
      applyThemeMode(mode);
    }
    
    // Update active button
    document.querySelectorAll('[data-theme-mode]').forEach(button => {
      if (button.getAttribute('data-theme-mode') === mode) {
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-outline-primary');
        const icon = button.querySelector('i');
        if (icon) {
          icon.className = 'bx bx-check';
        }
      } else {
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-outline-secondary');
        const icon = button.querySelector('i');
        if (icon) {
          icon.className = '';
        }
      }
    });
  }
  
  /**
   * Apply stored RTL setting
   */
  function applyStoredRTLSetting() {
    const isRTL = localStorage.getItem(RTL_KEY) === 'true';
    
    if (isRTL) {
      html.setAttribute('dir', 'rtl');
      loadRTLStylesheets();
    } else {
      html.removeAttribute('dir');
    }
  }
  
  /**
   * Load RTL stylesheets
   */
  function loadRTLStylesheets() {
    // Implementation for loading RTL stylesheets if needed
  }
  
  /**
   * Set RTL mode and store it
   * @param {boolean} isRTL - Whether to enable RTL mode
   */
  function setRTLMode(isRTL) {
    localStorage.setItem(RTL_KEY, isRTL);
    
    // Update via API
    fetch(API_ROUTES.rtl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ is_rtl: isRTL.toString() })
    }).catch(error => console.error('Error updating RTL mode via API:', error));
    
    if (isRTL) {
      html.setAttribute('dir', 'rtl');
      loadRTLStylesheets();
    } else {
      html.removeAttribute('dir');
      // Unload RTL stylesheets if needed
    }
  }
  
  /**
   * Apply stored menu collapsed state
   */
  function applyStoredMenuCollapsedState() {
    const isMenuCollapsed = localStorage.getItem(MENU_COLLAPSED_KEY) === 'true';
    
    if (isMenuCollapsed) {
      html.classList.add('layout-menu-collapsed');
    } else {
      html.classList.remove('layout-menu-collapsed');
    }
  }
  
  /**
   * Set menu collapsed state and store it
   * @param {boolean} isCollapsed - Whether the menu is collapsed
   */
  function setMenuCollapsed(isCollapsed) {
    localStorage.setItem(MENU_COLLAPSED_KEY, isCollapsed);
    
    // Update via API
    fetch(API_ROUTES.menuCollapsed, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ is_collapsed: isCollapsed.toString() })
    }).catch(error => console.error('Error updating menu collapsed state via API:', error));
    
    if (isCollapsed) {
      html.classList.add('layout-menu-collapsed');
    } else {
      html.classList.remove('layout-menu-collapsed');
    }
  }
  
  /**
   * Toggle menu collapsed state
   */
  function toggleMenuCollapsed() {
    const isCollapsed = html.classList.contains('layout-menu-collapsed');
    setMenuCollapsed(!isCollapsed);
  }
  
  /**
   * Bind event listeners for theme controls
   */
  function bindEventListeners() {
    // Theme mode buttons
    document.querySelectorAll('[data-theme-mode]').forEach(button => {
      button.addEventListener('click', () => {
        const mode = button.getAttribute('data-theme-mode');
        setThemeMode(mode);
      });
    });
    
    // RTL toggle
    const rtlToggle = document.querySelector('[data-toggle-rtl]');
    if (rtlToggle) {
      rtlToggle.addEventListener('click', () => {
        const isRTL = html.getAttribute('dir') === 'rtl';
        setRTLMode(!isRTL);
      });
    }
    
    // Menu collapse toggle
    const menuToggle = document.querySelector('.menu-toggle');
    if (menuToggle) {
      menuToggle.addEventListener('click', () => {
        toggleMenuCollapsed();
      });
    }
  }
  
  // Public API
  return {
    init: init,
    setThemeMode: setThemeMode,
    setRTLMode: setRTLMode,
    setMenuCollapsed: setMenuCollapsed,
    toggleMenuCollapsed: toggleMenuCollapsed
  };
})();

// Initialize on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
  ThemeManager.init();
}); 