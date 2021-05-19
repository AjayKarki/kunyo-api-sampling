<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Modules\Application\Http\Controllers')
    ->prefix('api/v1/')
    ->name('api.')
    ->middleware([ 'api', 'cors', \Modules\Application\Http\Middleware\EnforceJson::class, ])->group( function () {

//    Route::fallback(function(){
//        return response()->json([
//            'message' => 'Page Not Found. If error persists, contact info@kunyo.co'], 404);
//    });

    # Home Api
    Route::post('/home', 'Home\HomeAction')->name('feeds.home');
    Route::post('/container', 'Product\ProductListByPattern')->name('feeds.by-pattern');
    Route::post('/general', 'General\GeneralAction')->name('feeds.general');
    Route::post('/banners', 'Home\BannerListAction')->name('banner.index');
    Route::post('/testimonials', 'Testimonial\ListAction')->name('testimonials.index');
    Route::post('/search', 'SearchAction')->name('search.index');

    Route::post('/page/{slug}', 'Cms\PageAction')->name('cms.page');

        Route::post('/products/{product_type}', 'Product\ListAction')
            ->where('product_type', \Foundation\Lib\Product::PRODUCT_GIFT_CARD.'|'.\Foundation\Lib\Product::PRODUCT_TOP_UP)
            ->name('feeds.product');

        Route::post('/products/{product_type}/{category_id}', 'Product\ListByCategory')
            ->where('product_type', \Foundation\Lib\Product::PRODUCT_GIFT_CARD.'|'.\Foundation\Lib\Product::PRODUCT_TOP_UP)
            ->name('feeds.product.by-category');

        Route::post('/product/{product_type}/{slug}/related-product', 'Product\RelatedProduct')
            ->where('product_type', \Foundation\Lib\Product::PRODUCT_GIFT_CARD.'|'.\Foundation\Lib\Product::PRODUCT_TOP_UP)
            ->name('feeds.related-product');

        Route::post('/products/{product_type}/{slug}/view', 'Product\ViewAction')
            ->where('product_type', \Foundation\Lib\Product::PRODUCT_GIFT_CARD.'|'.\Foundation\Lib\Product::PRODUCT_TOP_UP)
            ->name('feeds.product.view');

    Route::post('/categories/{slug?}', 'Category\ListAction')->name('category.index');

    Route::post('/category/{id}', 'Category\ViewAction')->name('category.show');
    Route::post('/collection/{slug}', 'Collection\ViewAction')->name('collection.show');
    Route::post('/collection/{slug}/list', 'Collection\ListAction')->name('collection.list');

    Route::post('/pattern/{type}', 'Home\Pattern\ListAction')->name('pattern.index');

        Route::middleware([ 'auth:api', ])->group(function () {

            Route::post('/comment', 'Order\CommentAction')->name('user.order.comment');

            Route::post('/order/{identifier}', 'Order\OrderAction')->name('user.order.action');
            Route::post('/orders', 'Order\OrderList')->name('user.order.list');
            Route::post('/order/{transactionId}/view', 'Order\OrdersByTransaction')->name('user.order.by-transaction');

            Route::get('verify/khalti', 'Order\Payment\KhaltiVerification')->name('verify.khalti');
            Route::post('confirm/prabhupay', 'Order\Payment\ConfirmPrabhuPay')->name('confirm.prabhupay');
            Route::post('checkout/{gateway}/{identifier}/{result}', 'Order\ResultAction')
                ->where('gateway', 'imepay|khalti|bank|prabhupay')
                ->name('user.order.result');

            # Cart Api
            Route::post('carts', 'Order\Cart\CartListAction')->name('carts');
            Route::post('cart/add-to-cart', 'Order\Cart\AddToCartAction')->name('carts.add-to-cart');
            Route::post('wishlist', 'Order\Cart\WishListAction')->name('wishlist');
            Route::post('wishlist/add-to-wishlist', 'Order\Cart\AddToWishList')->name('carts.add-to-wish-list');
            Route::post('cart/remove-item', 'Order\Cart\RemoveItemAction')->name('carts.remove-item');

            Route::post('cart/sync', 'Order\Cart\SyncCartAction')->name('carts.sync-cart');

        });

    Route::middleware([  ])->group( function () {

        Route::post('/login', 'Auth\LoginAction')->name('login');
        Route::post('/register', 'Auth\RegisterAction')->name('register');
        Route::post('/refresh-token', 'Auth\AuthProcessAction@refresh')->name('refresh.token');

        Route::middleware([ 'auth:api', ])->group(function () {

            Route::post('detail', 'Auth\AuthProcessAction@user')->name('user.detail');
            Route::post('change-password', 'Auth\ChangePasswordAction')->name('user.change-password');
            Route::post('change-notification-channel', 'Auth\ChangeNotificationChannel')->name('user.change-notification-channel');
            Route::post('profile/update', 'Profile\UpdateProfile')->name('user.post-detail');
            Route::post('logout', 'Auth\AuthProcessAction@logout')->name('logout');

        });

    });

});
