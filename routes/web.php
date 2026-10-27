<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BookImageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', [StoreController::class, 'index'])->name('home');
Route::get('/gioi-thieu', [PageController::class, 'about'])->name('about');
Route::get('/san-pham', [PageController::class, 'products'])->name('products');
Route::get('/blog', [PageController::class, 'blog'])->name('blog.index');
Route::get('/lien-he', [PageController::class, 'contact'])->name('contact');
Route::post('/lien-he', [PageController::class, 'sendContact'])->name('contact.send');
Route::get('/book-image/{path}', [BookImageController::class, 'show'])->where('path', '.*')->name('book.image');
Route::get('/sach/{book:slug}', [StoreController::class, 'show'])->name('books.show');
Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
Route::post('/dang-nhap', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');
Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
Route::post('/dang-ky', [AuthController::class, 'register'])->name('register.post');
Route::post('/dang-xuat', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::post('/gio-hang/them/{book}', [CartController::class, 'add'])->middleware('auth')->name('cart.add');
Route::post('/mua-ngay/{book}', [CartController::class, 'buyNow'])->middleware('auth')->name('cart.buy-now');
Route::post('/gio-hang/cap-nhat', [CartController::class, 'update'])->name('cart.update');
Route::put('/gio-hang/cap-nhat', [CartController::class, 'update'])->name('cart.update.put');
Route::delete('/gio-hang/xoa/{book}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/thanh-toan', [CheckoutController::class, 'show'])->middleware('auth')->name('checkout.show');
Route::post('/thanh-toan', [CheckoutController::class, 'place'])->middleware('auth')->name('checkout.place');
Route::get('/don-hang/{order}/thanh-cong', [CheckoutController::class, 'thankyou'])->name('orders.thankyou');
Route::get('/tai-khoan/don-hang', [CheckoutController::class, 'mine'])->middleware('auth')->name('orders.mine');
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('books', BookController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
