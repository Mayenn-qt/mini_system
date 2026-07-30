<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SettingController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    
    // Core Shared Dashboard & Profile Settings
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [SettingController::class, 'index'])->name('profile');
    Route::post('/profile/account', [SettingController::class, 'updateAccount'])->name('profile.account');
    Route::post('/profile/password', [SettingController::class, 'changePassword'])->name('profile.password');

    // ==========================================
    // OWNER ONLY ROUTES
    // ==========================================
    Route::middleware(['role:owner'])->group(function () {
        
        // Products Admin CRUD
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Categories Admin CRUD
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Inventory Admin View
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

        // Sales Admin View
        Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');

        // Branch Monitoring
        Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
        Route::get('/branches/{branch}', [BranchController::class, 'show'])->name('branches.show');

        // User (Staff) Admin CRUD
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Global Store Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/store', [SettingController::class, 'updateStore'])->name('settings.store');

        // SMS Dispatcher
        Route::get('/sms', [SmsController::class, 'index'])->name('sms.index');
        Route::post('/sms/arrival', [SmsController::class, 'sendArrival'])->name('sms.arrival');
        Route::post('/sms/lowstock', [SmsController::class, 'sendLowStock'])->name('sms.lowstock');
    });

    // ==========================================
    // STAFF ONLY ROUTES
    // ==========================================
    Route::middleware(['role:staff'])->group(function () {
        
        // Products Staff View & Stock Alterations
        Route::get('/staff/products', [ProductController::class, 'staffIndex'])->name('staff.products');
        Route::post('/staff/products/{product}/stock', [ProductController::class, 'updateStock'])->name('staff.products.stock');

        // Inventory Staff View & Manual In/Out Adjustments
        Route::get('/staff/inventory', [InventoryController::class, 'staffIndex'])->name('staff.inventory');
        Route::post('/staff/inventory/in', [InventoryController::class, 'stockIn'])->name('staff.inventory.in');
        Route::post('/staff/inventory/out', [InventoryController::class, 'stockOut'])->name('staff.inventory.out');

        // POS Walk-in Sales Recording
        Route::get('/pos', [SalesController::class, 'pos'])->name('pos');
        Route::get('/pos/products/search', [SalesController::class, 'searchProducts'])->name('pos.products.search');
        Route::post('/pos/sales', [SalesController::class, 'store'])->name('pos.sales.store');
        Route::get('/staff/sales', [SalesController::class, 'staffHistory'])->name('staff.sales.history');
    });

    // ==========================================
    // SHARED / ROLE-RESTRICTED REPORTS & EXPORTS
    // ==========================================
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/sales/{sale}/receipt', [SalesController::class, 'receipt'])->name('sales.receipt');
});
