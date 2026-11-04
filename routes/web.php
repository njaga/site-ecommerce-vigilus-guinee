<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\QuoteController as AdminQuoteController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/produits', [StorefrontController::class, 'catalog'])->name('catalog');
Route::get('/categories/{category:slug}', [StorefrontController::class, 'catalog'])->name('category.show');
Route::get('/produits/{slug}', [StorefrontController::class, 'product'])->name('product.show');

Route::get('/demande-de-devis', [QuoteController::class, 'create'])->name('quote.create');
Route::post('/demande-de-devis', [QuoteController::class, 'store'])
    ->middleware('throttle:8,1')->name('quote.store');

Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter', [CartController::class, 'add'])->middleware('throttle:30,1')->name('cart.add');
Route::post('/panier/mettre-a-jour', [CartController::class, 'update'])->name('cart.update');
Route::get('/commande', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/commande', [CheckoutController::class, 'store'])->middleware('throttle:5,1')->name('checkout.store');
Route::get('/commande/demande-recue', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('/robots.txt', [SiteController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])->name('sitemap');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'show'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
});

Route::post('/deconnexion', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'can:manage-catalog'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/produits')->name('home');
    Route::get('/produits', [ProductController::class, 'index'])->name('products.index');
    Route::get('/produits/creer', [ProductController::class, 'create'])->name('products.create');
    Route::post('/produits', [ProductController::class, 'store'])->name('products.store');
    Route::get('/produits/{product}/modifier', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/produits/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::patch('/produits/{product}/archiver', [ProductController::class, 'archive'])->name('products.archive');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::get('/devis', [AdminQuoteController::class, 'index'])->name('quotes.index');
    Route::get('/commandes', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/commandes/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/commandes/{order}/reserver', [OrderController::class, 'reserve'])->name('orders.reserve');
    Route::patch('/commandes/{order}/annuler', [OrderController::class, 'cancel'])->name('orders.cancel');
});
