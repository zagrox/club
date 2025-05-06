<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LayoutController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\UIController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormLayoutController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\AccountSettingController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChangeLogController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\NotificationCenterController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Auth;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Non-localized routes
Auth::routes();

// Localized routes
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'web',
        \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
        \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class
    ],
], function() {
    
    // Dashboard
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    // Translation test page
    Route::get('/translation-test', function() {
        return view('pages.translation-test');
    })->name('translation-test');
    
    // Admin Routes
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        // Check if user has admin role
        Route::group([], function () {
            Route::get('/translations', [App\Http\Controllers\TranslationController::class, 'index'])->name('translations.index');
            Route::get('/translations/extract', [App\Http\Controllers\TranslationController::class, 'extract'])->name('translations.extract');
            
            // Language Management
            Route::get('/translations/languages', [App\Http\Controllers\TranslationController::class, 'languages'])->name('translations.languages');
            Route::get('/translations/languages/create', [App\Http\Controllers\TranslationController::class, 'createLanguage'])->name('translations.create_language');
            Route::post('/translations/languages', [App\Http\Controllers\TranslationController::class, 'storeLanguage'])->name('translations.store_language');
            Route::get('/translations/languages/{id}/edit', [App\Http\Controllers\TranslationController::class, 'editLanguage'])->name('translations.edit_language');
            Route::put('/translations/languages/{id}', [App\Http\Controllers\TranslationController::class, 'updateLanguage'])->name('translations.update_language');
            Route::delete('/translations/languages/{id}', [App\Http\Controllers\TranslationController::class, 'deleteLanguage'])->name('translations.delete_language');
            Route::post('/translations/languages/toggle/{id}', [App\Http\Controllers\TranslationController::class, 'toggleLanguage'])->name('translations.toggle_language');
            Route::post('/translations/languages/reorder', [App\Http\Controllers\TranslationController::class, 'reorderLanguages'])->name('translations.reorder_languages');
            
            // Translation Files Management
            Route::get('/translations/files/create', [App\Http\Controllers\TranslationController::class, 'createFile'])->name('translations.create_file');
            Route::post('/translations/files', [App\Http\Controllers\TranslationController::class, 'storeFile'])->name('translations.store_file');
            Route::delete('/translations/files/{file}', [App\Http\Controllers\TranslationController::class, 'deleteFile'])->name('translations.delete_file');
            
            // Individual Translation File Edit
            Route::get('/translations/{file}', [App\Http\Controllers\TranslationController::class, 'edit'])->name('translations.edit');
            Route::put('/translations/{file}', [App\Http\Controllers\TranslationController::class, 'update'])->name('translations.update');
            
            // Admin payment routes
            Route::get('/payments', [App\Http\Controllers\PaymentController::class, 'index'])->name('admin.payments.index');
            Route::get('/payments/{payment}', [App\Http\Controllers\PaymentController::class, 'show'])->name('admin.payments.show');
            
            // Payment options routes
            Route::get('/payment-options', [App\Http\Controllers\PaymentOptionsController::class, 'index'])->name('payment-options.index');
            Route::get('/payment-options/create', [App\Http\Controllers\PaymentOptionsController::class, 'create'])->name('payment-options.create');
            Route::post('/payment-options', [App\Http\Controllers\PaymentOptionsController::class, 'store'])->name('payment-options.store');
            Route::get('/payment-options/{option}/edit', [App\Http\Controllers\PaymentOptionsController::class, 'edit'])->name('payment-options.edit');
            Route::put('/payment-options/{option}', [App\Http\Controllers\PaymentOptionsController::class, 'update'])->name('payment-options.update');
            Route::delete('/payment-options/{option}', [App\Http\Controllers\PaymentOptionsController::class, 'destroy'])->name('payment-options.destroy');
            Route::post('/payment-options/update-zibal', [App\Http\Controllers\PaymentOptionsController::class, 'updateZibal'])->name('payment-options.zibal.update');
            Route::post('/payment-options/test-zibal', [App\Http\Controllers\PaymentOptionsController::class, 'testZibal'])->name('payment-options.zibal.test');
            Route::get('/payment-options/test-zibal', [App\Http\Controllers\PaymentOptionsController::class, 'testZibal'])->name('payment-options.test-zibal');
        });
    });
    
    // Tools
    Route::get('/tools', [ToolController::class, 'index'])->name('tools');
    
    // Layouts
    Route::prefix('layouts')->name('layouts.')->group(function () {
        Route::get('/fluid', [LayoutController::class, 'fluid'])->name('fluid');
        Route::get('/container', [LayoutController::class, 'container'])->name('container');
        Route::get('/without-menu', [LayoutController::class, 'withoutMenu'])->name('without-menu');
        Route::get('/without-navbar', [LayoutController::class, 'withoutNavbar'])->name('without-navbar');
        Route::get('/blank', [LayoutController::class, 'blank'])->name('blank');
    });
    
    // Account Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/account', [AccountSettingController::class, 'account'])->name('account');
        Route::get('/security', [AccountSettingController::class, 'security'])->name('security');
        Route::post('/change-password', [AccountSettingController::class, 'changePassword'])->name('change-password');
        Route::get('/notifications', [AccountSettingController::class, 'notifications'])->name('notifications');
        Route::get('/connections', [AccountSettingController::class, 'connections'])->name('connections');
    });
    
    // Notification Center
    Route::prefix('notification-center')->name('notification-center.')->group(function () {
        Route::get('/', [NotificationCenterController::class, 'index'])->name('index');
        Route::get('/archived', [NotificationCenterController::class, 'archived'])->name('archived');
        Route::get('/settings', [NotificationCenterController::class, 'settings'])->name('settings');
        Route::post('/mark-as-read', [NotificationCenterController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/dismiss', [NotificationCenterController::class, 'dismiss'])->name('dismiss');
        Route::post('/archive', [NotificationCenterController::class, 'archive'])->name('archive');
        
        // New notification routes
        Route::get('/create', [NotificationCenterController::class, 'create'])->name('create');
        Route::post('/store', [NotificationCenterController::class, 'store'])->name('store');
        Route::post('/store-draft', [NotificationCenterController::class, 'storeDraft'])->name('store-draft');
        Route::post('/preview', [NotificationCenterController::class, 'preview'])->name('preview');
    });
    
    // Cards
    Route::prefix('cards')->name('cards.')->group(function () {
        Route::get('/basic', [CardController::class, 'basic'])->name('basic');
    });
    
    // UI Elements
    Route::prefix('ui')->name('ui.')->group(function () {
        Route::get('/accordion', [UIController::class, 'accordion'])->name('accordion');
        Route::get('/alerts', [UIController::class, 'alerts'])->name('alerts');
        Route::get('/buttons', [UIController::class, 'buttons'])->name('buttons');
        Route::get('/carousel', [UIController::class, 'carousel'])->name('carousel');
        Route::get('/collapse', [UIController::class, 'collapse'])->name('collapse');
    });
    
    // Form Elements
    Route::prefix('forms')->name('forms.')->group(function () {
        Route::get('/basic-inputs', [FormController::class, 'basicInputs'])->name('basic-inputs');
        Route::get('/input-groups', [FormController::class, 'inputGroups'])->name('input-groups');
    });
    
    // Form Layouts
    Route::prefix('form-layouts')->name('form-layouts.')->group(function () {
        Route::get('/vertical', [FormLayoutController::class, 'vertical'])->name('vertical');
        Route::get('/horizontal', [FormLayoutController::class, 'horizontal'])->name('horizontal');
    });
    
    // Tables
    Route::prefix('tables')->name('tables.')->group(function () {
        Route::get('/basic', [TableController::class, 'basic'])->name('basic');
    });
    
    // Users Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/list', [UserController::class, 'list'])->name('list');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/manage/{user}', [UserController::class, 'manage'])->name('manage');
        Route::get('/details/{user}', [UserController::class, 'details'])->name('details');
        Route::put('/update/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete/{user}', [UserController::class, 'destroy'])->name('delete');
        
        // Roles Routes
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/store', [RoleController::class, 'store'])->name('store');
            Route::get('/{role}', [RoleController::class, 'show'])->name('show');
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
            Route::post('/{role}/assign-users', [RoleController::class, 'assignUsers'])->name('assign-users');
            Route::delete('/{role}/users/{user}', [RoleController::class, 'removeUser'])->name('remove-user');
        });
    
        // Permissions Management
        Route::resource('permissions', PermissionController::class);
    });
    
    // Stand-alone Permissions Routes (for easier access)
    Route::prefix('permissions')->name('permissions.')->group(function () {
        // Main permissions resource routes
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/create', [PermissionController::class, 'create'])->name('create');
        Route::post('/', [PermissionController::class, 'store'])->name('store');
        Route::get('/{permission}', [PermissionController::class, 'show'])->name('show');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
        
        // Matrix routes - using full controller namespace to avoid binding issues
        Route::get('/matrix', [\App\Http\Controllers\PermissionMatrixController::class, 'index'])->name('matrix');
        Route::post('/matrix/update', [\App\Http\Controllers\PermissionMatrixController::class, 'update'])->name('matrix.update');
    });
    
    // Orders Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/list', [OrderController::class, 'list'])->name('list');
        Route::get('/details/{id}', [OrderController::class, 'details'])->name('details');
        Route::post('/update-status/{id}', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/store', [OrderController::class, 'store'])->name('store');
        Route::delete('/destroy/{id}', [OrderController::class, 'destroy'])->name('destroy');
    });
    
    // Theme Settings
    Route::prefix('theme')->name('theme.')->group(function () {
        Route::get('/settings', [ThemeController::class, 'settings'])->name('settings');
        Route::post('/mode', [ThemeController::class, 'updateMode'])->name('mode');
        Route::post('/rtl', [ThemeController::class, 'toggleRtl'])->name('rtl');
        Route::post('/menu-collapsed', [ThemeController::class, 'toggleMenuCollapsed'])->name('menu-collapsed');
    });
    
    // Change Logs
    Route::get('/change-logs', [ChangeLogController::class, 'index'])->name('change-logs');
    
    // Backup Routes
    Route::prefix('backup')->name('backup.')->middleware(['auth'])->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/start', [BackupController::class, 'startBackup'])->name('start');
        Route::post('/delete', [BackupController::class, 'deleteBackup'])->name('delete');
        Route::get('/download/{fileName}', [BackupController::class, 'downloadBackup'])->name('download');
        Route::post('/cleanup', [BackupController::class, 'cleanupBackups'])->name('cleanup');
        Route::post('/update-path', [BackupController::class, 'updateBackupPath'])->name('update-path');
        Route::post('/cleanup-old', [BackupController::class, 'cleanupOldBackups'])->name('cleanup-old');
        Route::post('/update-note', [BackupController::class, 'updateNote'])->name('update-note');
    });
    
    // FAQ
    Route::get('/faq', [FaqController::class, 'index'])->name('faq');
    
    // Setup Routes
    Route::get('/setup/initialize-users', [SetupController::class, 'initializeUsers']);
    
    // Direct matrix routes outside of group (for easier access)
    Route::get('/matrix', [\App\Http\Controllers\PermissionMatrixController::class, 'index'])->name('matrix');
    Route::post('/matrix/update', [\App\Http\Controllers\PermissionMatrixController::class, 'update'])->name('matrix.update');

    // Wallet routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/wallet', [App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');
        
        Route::get('/wallet/deposit', [App\Http\Controllers\WalletController::class, 'showDepositForm'])->name('wallet.showDepositForm');
        Route::post('/wallet/deposit', [App\Http\Controllers\WalletController::class, 'deposit'])->name('wallet.deposit');
        
        Route::get('/wallet/withdraw', [App\Http\Controllers\WalletController::class, 'showWithdrawForm'])->name('wallet.showWithdrawForm');
        Route::post('/wallet/withdraw', [App\Http\Controllers\WalletController::class, 'withdraw'])->name('wallet.withdraw');
        
        Route::get('/wallet/transfer', [App\Http\Controllers\WalletController::class, 'showTransferForm'])->name('wallet.showTransferForm');
        Route::post('/wallet/transfer', [App\Http\Controllers\WalletController::class, 'transfer'])->name('wallet.transfer');
        
        Route::get('/wallet/transactions', [App\Http\Controllers\WalletController::class, 'transactions'])->name('wallet.transactions');
    });
    
    // Notifications inbox
    Route::prefix('inbox')->name('inbox.')->middleware(['auth'])->group(function () {
        Route::get('/', [App\Http\Controllers\InboxController::class, 'index'])->name('index');
        Route::get('/read/{id}', [App\Http\Controllers\InboxController::class, 'read'])->name('read');
        Route::get('/mark-all-read', [App\Http\Controllers\InboxController::class, 'markAllAsRead'])->name('markAllAsRead');
        Route::get('/read-and-redirect/{id}/{type?}', [App\Http\Controllers\InboxController::class, 'readAndRedirect'])->name('readAndRedirect');
    });
    
});

// Non-localized wallet callback route
Route::get('/wallet/deposit/callback', [App\Http\Controllers\WalletController::class, 'depositCallback'])
    ->name('wallet.deposit.callback'); 