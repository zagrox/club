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

// Authentication Routes (Laravel default)
Auth::routes();

// Dashboard
Route::get('/', [HomeController::class, 'index'])->name('home');

// Layouts
Route::prefix('layouts')->name('layouts.')->group(function () {
    Route::get('/fluid', [LayoutController::class, 'fluid'])->name('fluid');
    Route::get('/container', [LayoutController::class, 'container'])->name('container');
    Route::get('/without-menu', [LayoutController::class, 'withoutMenu'])->name('without-menu');
    Route::get('/without-navbar', [LayoutController::class, 'withoutNavbar'])->name('without-navbar');
    Route::get('/blank', [LayoutController::class, 'blank'])->name('blank');
});

// Account Settings
Route::prefix('account-settings')->name('account-settings.')->group(function () {
    Route::get('/account', [AccountSettingController::class, 'account'])->name('account');
    Route::get('/notifications', [AccountSettingController::class, 'notifications'])->name('notifications');
    Route::get('/connections', [AccountSettingController::class, 'connections'])->name('connections');
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
});

// Theme Settings
Route::prefix('theme')->name('theme.')->group(function () {
    Route::get('/settings', [ThemeController::class, 'settings'])->name('settings');
    Route::post('/mode', [ThemeController::class, 'updateMode'])->name('mode');
    Route::post('/rtl', [ThemeController::class, 'toggleRtl'])->name('rtl');
    Route::post('/menu-collapsed', [ThemeController::class, 'toggleMenuCollapsed'])->name('menu-collapsed');
});
