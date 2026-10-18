<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderDashController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AssociationRuleController;
use App\Http\Controllers\Admin\AprioriController;
use App\Http\Controllers\Admin\RecommendationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Artisan;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
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

require __DIR__ . '/auth.php';
Route::get('/view-clear', function () {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('config:cache');
    $exitCode = Artisan::call('config:clear');
    // $exitCode = Artisan::call('route:trans:cache');

    return '<h1>View cache cleared</h1>';
});
//checkout
Route::middleware('auth')->controller(OrderController::class)->group(
    function () {
        Route::get('checkout', 'index')->name('checkout.index');
        Route::post('checkout/store', 'store')->name('checkout.store');
    }
);


Route::middleware('auth')->group(function () {
    Route::get('/profile-show', [ProfileController::class, 'showProfile'])->name('profile.show');
    // غير مستخدمين بالكود 
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    //
});
//import products from excel file
Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
//search عملية البحث وعملية اظهار المنتجات 
Route::get('all_products', [SearchController::class, 'Search'])->name('products.all');
Route::get('sort_products', [SearchController::class, 'Search'])->name('products.sort');
//wishlist ماعنا احببته مشان هيك مااستخدمناه
// Route::get('favorite-update/{id}', [WishlistController::class, 'favoriteUpdate'])->name('favorite.add');
// Route::get('wishlist', [WishlistController::class, 'wishlist'])->name('wishlist');
// Route::delete('favorite-remove', [WishlistController::class, 'delete'])->name('remove.from.wishlist');

//barnd products  لانه بدا صور وشغل  وماتم استخدماها عرض الماركات 
// Route::get('product/{id}', [BrandController::class, 'products'])->name('brand.products');
//Home_barnds
// Route::get('brands', [BrandController::class, 'homeBrands'])->name('home.brands');
//user orders
Route::get('user_order', [UserController::class, 'userOrders'])->name('user.order');
Route::get('order_details/{id}', [UserController::class, 'orderDetails'])->name('order.details');

//content
Route::get('contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('contact-store', [ContactController::class, 'store'])->name('contact.store');

// association-rules recommendations
Route::get('recommendations', [AssociationRuleController::class, 'recommendations'])->name('recommendations.index');

Route::post('detail/add', [StoreController::class, 'addToCartFromDetail'])->name('cart.detail.store');
//cart
Route::get('list', [StoreController::class, 'cartList'])->name('cart.list');
Route::get('cart/mini', [StoreController::class, 'miniCart'])->name('cart.mini');
Route::post('store/add-to-cart/{id}', [StoreController::class, 'addToCart'])->name('cart.store');
Route::post('cart/bundle', [StoreController::class, 'addBundle'])->name('cart.bundle');
Route::post('cart/swap-upsell', [StoreController::class, 'swapUpsell'])->name('cart.swap');
Route::patch('update-cart', [StoreController::class, 'update'])->name('update.cart');
Route::delete('remove-from-cart', [StoreController::class, 'remove'])->name('remove.from.cart');
/*Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {*/
//category
Route::get('detail/{id}', [ProductController::class, 'showDetail'])->name('product.details');


Route::get('/cart/count', [StoreController::class, 'cartCount'])->name('cart.count');
// Route::get('/test-clear-cart-session', function () {
//     session()->forget('cart');

//     return response()->json([
//         'status' => 'cart session cleared',
//         'cart_count' => 0
//     ]);
// });
/*}
);*/
/*Route::get('/', function () {

    return view('welcome');
});*/
Route::get('/', [HomeController::class, 'index'])->name('welcome.index');
//to show product 
Route::prefix('admin')->middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/dash', [CategoryController::class, 'index'])->name('ad.master');
    //show messages
    Route::get('show-message', [AdminController::class, 'showMessgae'])->name('contact.message');
    //category
    Route::get('category', [CategoryController::class, 'index'])->name('category.index');
    // Route::resource('slider', SliderController::class);
    Route::get('category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('category/store', [CategoryController::class, 'store'])->name('category.store');
    Route::get('category/{id}/update', [CategoryController::class, 'update'])->name('category.update');
    Route::post('category/update_save/{id}', [CategoryController::class, 'update_save'])->name('category.update_save');
    Route::delete('category/delete/{id}', [CategoryController::class, 'delete'])->name('category.delete');
    //brands
    Route::get('brand', [BrandController::class, 'index'])->name('brand.index');
    Route::get('brand/create', [BrandController::class, 'create'])->name('brand.create');
    Route::post('brand/store', [BrandController::class, 'store'])->name('brand.store');
    Route::get('brand/{id}/update', [BrandController::class, 'update'])->name('brand.update');
    Route::post('brand/update_save/{id}', [BrandController::class, 'update_save'])->name('brand.update_save');
    Route::delete('brand/delete/{id}', [BrandController::class, 'delete'])->name('brand.delete');

    //products
    Route::get('product', [productController::class, 'index'])->name('product.index');
    Route::get('product/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('product/store', [ProductController::class, 'store'])->name('product.store');
    Route::get('product/{id}/update', [ProductController::class, 'update'])->name('product.update');
    Route::post('product/update_save/{id}', [ProductController::class, 'update_save'])->name('product.update_save');
    Route::delete('product/delete/{id}', [ProductController::class, 'delete'])->name('product.delete');
    //users
    Route::controller(UserController::class)->group(
        function () {
            Route::get('users', 'index')->name('user.index');
            Route::get('user/create', 'create')->name('user.create');
            Route::post('user/store', 'store')->name('user.store');
            Route::get('user/{id}/update', 'update')->name('user.update');
            Route::post('user/update_save/{id}', 'update_save')->name('user.update_save');
            Route::delete('user/delete/{id}', 'delete')->name('user.delete');
        }
    );
    Route::controller(SliderController::class)->group(
        function () {
            Route::get('slider', 'index')->name('slider.index');
            Route::get('slider/create', 'create')->name('slider.create');
            Route::post('slider/store', 'store')->name('slider.store');
            Route::get('slider/{id}/update', 'update')->name('slider.update');
            Route::post('slider/update_save/{id}', 'update_save')->name('slider.update_save');
            Route::delete('slider/delete/{id}', 'delete')->name('slider.delete');
        }
    );
    Route::controller(SettingController::class)->group(
        function () {
            Route::get('setting', 'index')->name('setting.index');
            Route::get('setting/create', 'create')->name('setting.create');
            Route::post('setting/store', 'store')->name('setting.store');
            Route::get('setting/update', 'update')->name('setting.update');
            Route::post('setting/update_save', 'update_save')->name('setting.update_save');
        }
    );

    Route::controller(ShopController::class)->group(
        function () {
            Route::resource('shop', ShopController::class);
            Route::post('shop/update_save/{id}', 'update_save')->name('shop.update_save');
        }
    );
    Route::controller(OrderDashController::class)->group(
        function () {
            Route::resource('order', OrderDashController::class);
            Route::post('order/{order}', 'changeState')->name('order.state');
            Route::get('product/{product}', 'OrderProductDetail')->name('order_product.detail');
        }
    );

    Route::controller(RecommendationController::class)->group(function () {
        Route::get('recommendations', 'index')->name('admin.recommendations.index');
        Route::post('recommendations/train', 'train')->name('admin.recommendations.train');
        Route::post('recommendations/evaluate', 'evaluate')->name('admin.recommendations.evaluate');
        Route::get('association-rules', 'rules')->name('admin.association-rules.index');
    });

    // Legacy php-ml Apriori page (kept for comparison)
    Route::controller(AprioriController::class)->group(function () {
        Route::get('apriori', 'index')->name('apriori.index');
        Route::get('apriori/rules', 'generateRules')->name('apriori.rules');
        Route::get('apriori/statistics', 'statistics')->name('apriori.statistics');
    });
});

//select code
Route::get('product_code_search/{code}', [ProductController::class, 'product_code_search'])->name('product.code_search');

Route::post('review-store', [ProductController::class, 'reviewStore'])->name('review.store');
