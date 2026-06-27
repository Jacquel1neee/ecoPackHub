<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\EnquiryController as AdminEnquiryController;
use Illuminate\Support\Facades\Route;

// ========== PUBLIC ROUTES ==========
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

// ========== AUTH ROUTES (Breeze) ==========
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========== CART ROUTES ==========
Route::middleware(['auth'])->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::get('/count', [CartController::class, 'getCount'])->name('count');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{item}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});

// ========== ORDER ROUTES ==========
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/orders/place', [OrderController::class, 'placeOrder'])->name('orders.place');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

// ========== ENQUIRY ROUTES ==========
Route::middleware(['auth'])->group(function () {
    Route::get('/enquiry/product/{product}', [EnquiryController::class, 'create'])->name('enquiry.create');
    Route::post('/enquiry', [EnquiryController::class, 'store'])->name('enquiry.store');
    Route::get('/enquiry/success', [EnquiryController::class, 'success'])->name('enquiry.success');
    Route::get('/enquiries', [EnquiryController::class, 'history'])->name('enquiry.history');
    Route::get('/enquiry/{enquiry}', [EnquiryController::class, 'show'])->name('enquiry.show');
    // User reply to enquiry (chat)
    Route::post('/enquiry/{enquiry}/reply', [EnquiryController::class, 'userReply'])->name('enquiry.user.reply');
});

// ========== ADMIN ROUTES ==========
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class);
    Route::resource('categories', CategoryController::class);
    
    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle-role', [UserController::class, 'toggleRole'])->name('users.toggle-role');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // Admin Order Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    
    // Admin Enquiry Management
    Route::get('/enquiries', [AdminEnquiryController::class, 'index'])->name('enquiries.index');
    Route::get('/enquiries/{enquiry}', [AdminEnquiryController::class, 'show'])->name('enquiries.show');
    Route::post('/enquiries/{enquiry}/reply', [AdminEnquiryController::class, 'reply'])->name('enquiries.reply');
    Route::patch('/enquiries/{enquiry}/status', [AdminEnquiryController::class, 'updateStatus'])->name('enquiries.update-status');
    Route::delete('/enquiries/{enquiry}', [AdminEnquiryController::class, 'destroy'])->name('enquiries.destroy');
});

require __DIR__.'/auth.php';