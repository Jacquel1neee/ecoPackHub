<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\EnquiryController as AdminEnquiryController;
use App\Http\Controllers\Admin\CategoryController as CategoryController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// ========== PUBLIC ROUTES ==========
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

// ========== PAGE ROUTES ==========
Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');

// ========== PRODUCT PAGE ROUTE ==========
Route::get('/products', [ProductPageController::class, 'index'])->name('products.index');

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
    Route::get('/count', [CartController::class, 'getCount'])->name('count');
    Route::get('/', [CartController::class, 'index'])->name('index');
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
    Route::post('/enquiry/{enquiry}/reply', [EnquiryController::class, 'userReply'])->name('enquiry.user.reply');
});

// ========== FEEDBACK ROUTES ==========
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
Route::get('/feedback/{feedback}', [FeedbackController::class, 'show'])->name('feedback.show');
Route::get('/my-feedbacks', [FeedbackController::class, 'history'])->name('feedback.history');
Route::post('/feedback/{feedback}/reply', [FeedbackController::class, 'userReply'])->name('feedback.user.reply');

// ========== ADMIN ROUTES ==========
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', AdminProductController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // User Management (index replaced by hierarchy view)
    Route::get('/users', [App\Http\Controllers\Admin\HierarchyController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle-role', [AdminUserController::class, 'toggleRole'])->name('users.toggle-role');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Admin Order Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    // Admin Enquiry Management
    Route::get('/enquiries', [AdminEnquiryController::class, 'index'])->name('enquiries.index');
    Route::get('/enquiries/{enquiry}', [AdminEnquiryController::class, 'show'])->name('enquiries.show');
    Route::post('/enquiries/{enquiry}/reply', [AdminEnquiryController::class, 'reply'])->name('enquiries.reply');
    Route::patch('/enquiries/{enquiry}/status', [AdminEnquiryController::class, 'updateStatus'])->name('enquiries.update-status');
    Route::delete('/enquiries/{enquiry}', [AdminEnquiryController::class, 'destroy'])->name('enquiries.destroy');

    // Admin Feedback Management
    Route::get('/feedbacks', [AdminFeedbackController::class, 'index'])->name('feedbacks.index');
    Route::get('/feedbacks/{feedback}', [AdminFeedbackController::class, 'show'])->name('feedbacks.show');
    Route::post('/feedbacks/{feedback}/reply', [AdminFeedbackController::class, 'reply'])->name('feedbacks.reply');
    Route::patch('/feedbacks/{feedback}/status', [AdminFeedbackController::class, 'updateStatus'])->name('feedbacks.update-status');
    Route::delete('/feedbacks/{feedback}', [AdminFeedbackController::class, 'destroy'])->name('feedbacks.destroy');

    // Admin Hierarchy Management
    Route::get('/hierarchy', [App\Http\Controllers\Admin\HierarchyController::class, 'index'])->name('hierarchy.index');
    Route::post('/hierarchy/promote', [App\Http\Controllers\Admin\HierarchyController::class, 'sendPromoteRequest'])->name('hierarchy.send-promote');
    Route::patch('/hierarchy/promote/{request}/accept', [App\Http\Controllers\Admin\HierarchyController::class, 'acceptPromoteRequest'])->name('hierarchy.accept-promote');
    Route::patch('/hierarchy/promote/{request}/reject', [App\Http\Controllers\Admin\HierarchyController::class, 'rejectPromoteRequest'])->name('hierarchy.reject-promote');
    Route::delete('/hierarchy/promote/{request}', [App\Http\Controllers\Admin\HierarchyController::class, 'cancelPromoteRequest'])->name('hierarchy.cancel-promote');
    
    

    Route::post('/hierarchy/unlink', [App\Http\Controllers\Admin\HierarchyController::class, 'sendUnlinkRequest'])->name('hierarchy.send-unlink');
    Route::patch('/hierarchy/unlink/{request}/accept', [App\Http\Controllers\Admin\HierarchyController::class, 'acceptUnlinkRequest'])->name('hierarchy.accept-unlink');
    Route::patch('/hierarchy/unlink/{request}/reject', [App\Http\Controllers\Admin\HierarchyController::class, 'rejectUnlinkRequest'])->name('hierarchy.reject-unlink');
    Route::delete('/hierarchy/unlink/{request}', [App\Http\Controllers\Admin\HierarchyController::class, 'cancelUnlinkRequest'])->name('hierarchy.cancel-unlink');
});

// Notifications (available to all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post('notifications/{id}/promote/accept', [App\Http\Controllers\NotificationController::class, 'promoteAccept'])->name('notifications.promote.accept');
    Route::post('notifications/{id}/promote/reject', [App\Http\Controllers\NotificationController::class, 'promoteReject'])->name('notifications.promote.reject');
});

require __DIR__.'/auth.php';