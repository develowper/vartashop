<?php


use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PanelController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PackController;
use App\Http\Controllers\PartnershipController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepositoryCartController;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\RepositoryOrderController;
use App\Http\Controllers\RepositoryShippingController;
use App\Http\Controllers\RepositoryShopController;
use App\Http\Controllers\RepositoryTransportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\ShippingMethodController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VariationController;
use App\Http\Helpers\Variable;
use App\Models\Agency;
use App\Models\Article;
use App\Models\Product;
use App\Models\Variation;
use Illuminate\Support\Facades\Route;

//return;
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {

    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('admin.register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('admin.login-form');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('admin.login');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('admin.password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('admin.password.update');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('admin.password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('admin.password.store');

    Route::middleware(['auth:sanctum', 'auth:admin',
        config('jetstream.auth_session'),
        'verified'])->prefix('panel')->group(function ($route) {


        Route::get('', [PanelController::class, 'index'])->name('admin.panel.index');

        PanelController::makeInertiaRoute('get', 'setting/index', 'admin.panel.setting.index', 'Panel/Admin/Setting/Index',
            []);
        Route::get('setting/search', [SettingController::class, 'searchPanel'])->name('admin.panel.setting.search');
        Route::patch('setting/update', [SettingController::class, 'update'])->name('admin.panel.setting.update');
        Route::post('setting/create', [SettingController::class, 'create'])->name('admin.panel.setting.create')->middleware("can:create,App\Models\Admin,App\Models\Ticket,'1'");
        Route::get('setting/{setting}', [SettingController::class, 'edit'])->name('admin.panel.setting.edit');


        PanelController::makeInertiaRoute('get', 'slider/index', 'admin.panel.slider.index', 'Panel/Admin/Slider/Index',
            [

            ]);
        PanelController::makeInertiaRoute('get', 'slider/create', 'admin.panel.slider.create', 'Panel/Admin/Slider/Create',
            [
                'sliderRatio' => Variable::RATIOS['slider'],
            ]);

        PanelController::makeInertiaRoute('get', 'notification/index', 'admin.panel.notification.index', 'Panel/Admin/Notification/Index',
            [

            ]);
        PanelController::makeInertiaRoute('get', 'notification/create', 'admin.panel.notification.create', 'Panel/Admin/Notification/Create',
            [

            ]);

        PanelController::makeInertiaRoute('get', 'ticket/index', 'admin.panel.ticket.index', 'Panel/Ticket/Index',
            [
                'statuses' => Variable::TICKET_STATUSES

            ]);
        PanelController::makeInertiaRoute('get', 'ticket/create', 'admin.panel.ticket.create', 'Panel/Ticket/Create',
            [
                'attachment_allowed_mimes' => implode(',.', Variable::TICKET_ATTACHMENT_ALLOWED_MIMES),
            ]);


        Route::get('ticket/search', [TicketController::class, 'searchPanel'])->name('admin.panel.ticket.search');
        Route::patch('ticket/update', [TicketController::class, 'update'])->name('admin.panel.ticket.update');
        Route::post('ticket/create', [TicketController::class, 'create'])->name('admin.panel.ticket.create')->middleware("can:create,App\Models\Admin,App\Models\Ticket,'1'");
        Route::get('ticket/{agency}', [TicketController::class, 'edit'])->name('admin.panel.ticket.edit');

        PanelController::makeInertiaRoute('get', 'user/index', 'admin.panel.user.index', 'Panel/Admin/User/Index',
            [
            ], "can:view,App\Models\Admin,App\Models\User,'1'");
        PanelController::makeInertiaRoute('get', 'user/create', 'admin.panel.user.create', 'Panel/Admin/User/Create',
            [
            ]);
        Route::get('user/search', [UserController::class, 'searchPanel'])->name('admin.panel.user.search');
        Route::patch('user/update', [UserController::class, 'update'])->name('admin.panel.user.update');
        Route::post('user/create', [UserController::class, 'create'])->name('admin.panel.user.create')->middleware("can:create,App\Models\Admin,App\Models\User,'1'");
        Route::get('user/{setting}', [UserController::class, 'edit'])->name('admin.panel.user.edit');

        PanelController::makeInertiaRoute('get', 'admin/index', 'admin.panel.admin.index', 'Panel/Admin/Admin/Index',
            [
                'user_statuses' => collect(Variable::USER_STATUSES)->filter(fn($e) => $e['name'] != 'block'),
                'admin_roles' => array_filter(Variable::ADMIN_ROLES, fn($e) => $e != 'god')
            ]);
        PanelController::makeInertiaRoute('get', 'admin/create', 'admin.panel.admin.create', 'Panel/Admin/Admin/Create',
            [
                'admin_roles' => array_values(array_filter(Variable::ADMIN_ROLES, fn($e) => $e != 'god')),
                'user_statuses' => collect(Variable::USER_STATUSES)->filter(fn($e) => $e['name'] != 'block')->pluck('name'),

            ]);
        Route::get('admin/search', [AdminController::class, 'searchPanel'])->name('admin.panel.admin.search');
        Route::patch('admin/update', [AdminController::class, 'update'])->name('admin.panel.admin.update');
        Route::post('admin/create', [AdminController::class, 'create'])->name('admin.panel.admin.create')->middleware("can:create,App\Models\Admin,App\Models\Admin,'1'");
        Route::get('admin/{setting}', [AdminController::class, 'edit'])->name('admin.panel.admin.edit');


        PanelController::makeInertiaRoute('get', 'message/index', 'admin.panel.message.index', 'Panel/Admin/Message/Index',
            [
                'statuses' => Variable::MESSAGE_STATUSES,
            ]);
        PanelController::makeInertiaRoute('get', 'message/create', 'admin.panel.message.create', 'Panel/Admin/Message/Create',
            [
            ]);
        PanelController::makeInertiaRoute('get', 'article/index', 'admin.panel.article.index', 'Panel/Admin/Article/Index',
            [
                'statuses' => Variable::STATUSES
            ]
        );
        PanelController::makeInertiaRoute('get', 'article/create', 'admin.panel.article.create', 'Panel/Admin/Article/Create',
            [
                'statuses' => Variable::STATUSES,
            ]
        );


        PanelController::makeInertiaRoute('get', 'transaction/index', 'admin.panel.financial.transaction.index', 'Panel/Financial/Transaction/Index',
            []
        );
        Route::get('transaction/search', [TransactionController::class, 'searchPanel'])->name('admin.panel.financial.transaction.search');

        PanelController::makeInertiaRoute('get', 'financial/index', 'admin.panel.financial.index', 'Panel/Financial/Index',
            []
        );
        Route::get('financial/search', [FinancialController::class, 'searchMerged'])->name('admin.panel.financial.search');
        Route::patch('financial/update', [FinancialController::class, 'update'])->name('admin.panel.financial.update');


        PanelController::makeInertiaRoute('get', 'agency/index', 'admin.panel.agency.index', 'Panel/Admin/Agency/Index',
            [
            ]
        );
        PanelController::makeInertiaRoute('get', 'agency/create', 'admin.panel.agency.create', 'Panel/Admin/Agency/Create',
            [
                'parent_agencies' => Agency::whereNot('level', Variable::AGENCY_TYPES[count(Variable::AGENCY_TYPES) - 1]['level'])->whereNotNull('level')->select('id', 'name', 'province_id', 'level', 'access')->get(),
            ]
        );

        Route::get('agency/search', [AgencyController::class, 'searchPanel'])->name('admin.panel.agency.search');
        Route::patch('agency/update', [AgencyController::class, 'update'])->name('admin.panel.agency.update');
        Route::post('agency/create', [AgencyController::class, 'create'])->name('admin.panel.agency.create')->middleware("can:create,App\Models\Admin,App\Models\Agency,'1'");
        Route::get('agency/{agency}', [AgencyController::class, 'edit'])->name('admin.panel.agency.edit');


        PanelController::makeInertiaRoute('get', 'repository/index', 'admin.panel.repository.index', 'Panel/Admin/Repository/Index',
            [

            ]
        );

        PanelController::makeInertiaRoute('get', 'repository/create', 'admin.panel.repository.create', 'Panel/Admin/Repository/Create',
            [
            ]
        );

        Route::get('repository/search', [RepositoryController::class, 'searchPanel'])->name('admin.panel.repository.search');
        Route::patch('repository/update', [RepositoryController::class, 'update'])->name('admin.panel.repository.update');
        Route::post('repository/create', [RepositoryController::class, 'create'])->name('admin.panel.repository.create')->middleware("can:create,App\Models\Admin,App\Models\Repository,'1'");
        Route::get('repository/{repository}', [RepositoryController::class, 'edit'])->name('admin.panel.repository.edit');


        PanelController::makeInertiaRoute('get', 'repository/transport/index', 'admin.panel.repository.transport.index', 'Panel/Admin/Repository/Transport/Index', []);

        PanelController::makeInertiaRoute('get', 'repository/transport/create', 'admin.panel.repository.transport.create', 'Panel/Admin/Repository/Transport/Create', []);


        Route::get('repository/shipping/search', [RepositoryShippingController::class, 'searchPanel'])->name('admin.panel.repository.shipping.search');
        Route::patch('repository/shipping/update', [RepositoryShippingController::class, 'update'])->name('admin.panel.repository.shipping.update');
        Route::post('repository/shipping/create', [RepositoryShippingController::class, 'create'])->name('admin.panel.repository.shipping.create')->middleware("can:create,App\Models\Admin,App\Models\RepositoryShipping,'1'");
        Route::get('repository/shipping/{shipping}', [RepositoryShippingController::class, 'edit'])->name('admin.panel.repository.shipping.edit');

        PanelController::makeInertiaRoute('get', 'repository/order/index', 'admin.panel.repository.order.index', 'Panel/Admin/Repository/Order/Index', ['order_statuses' => Variable::ORDER_STATUSES]);
        Route::post('repository/order/create', [RepositoryOrderController::class, 'create'])->name('admin.panel.repository.order.create')->middleware("can:create,App\Models\Admin,App\Models\RepositoryOrder,'1'");
        PanelController::makeInertiaRoute('get', 'repository/order/create', 'admin.panel.repository.order.create', 'Panel/Admin/Repository/Order/Create', [
            'pay_timeout' => Variable::PAY_TIMEOUT,
            'statuses' => collect(Variable::ORDER_STATUSES)->whereIn('name', ['request', 'pending', 'processing'])->pluck('name'),
        ]);
        Route::get('repository/order/search}', [RepositoryOrderController::class, 'searchPanel'])->name('admin.panel.repository.order.search');
        Route::get('repository/order/{order}', [RepositoryOrderController::class, 'edit'])->name('admin.panel.repository.order.edit');

        PanelController::makeInertiaRoute('get', 'repository/shop/index', 'admin.panel.repository.shop.index', 'Panel/Admin/Repository/Shop/Index', []);
        Route::get('repository/shop/search', [RepositoryShopController::class, 'search'])->name('admin.panel.repository.shop.search');
        Route::patch('repository/cart/update', [RepositoryCartController::class, 'update'])->name('admin.panel.repository.cart.update');


        PanelController::makeInertiaRoute('get', 'order/user/index', 'admin.panel.order.user.index', 'Panel/Admin/Order/User/Index', ['order_statuses' => collect(Variable::ORDER_STATUSES)->filter(fn($e) => $e['name'] != 'request'),]);
        PanelController::makeInertiaRoute('get', 'order/user/create', 'admin.panel.order.user.create', 'Panel/Admin/Order/User/Create', ['order_statuses' => collect(Variable::ORDER_STATUSES)->filter(fn($e) => $e['name'] != 'request'),]);
        Route::get('order/user/search', [OrderController::class, 'searchPanel'])->name('admin.panel.order.user.search');
        Route::patch('order/user/update', [OrderController::class, 'update'])->name('admin.panel.order.user.update');
        Route::get('order/user/{order}', [OrderController::class, 'edit'])->name('admin.panel.order.user.edit');

        Route::get('order/factor/{order}', [OrderController::class, 'factor'])->name('admin.panel.order.factor');

        PanelController::makeInertiaRoute('get', 'order/agency/index', 'admin.panel.order.agency.index', 'Panel/Admin/Order/Agency/Index', ['order_statuses' => collect(Variable::ORDER_STATUSES)->filter(fn($e) => $e['name'] != 'request'),]);
        PanelController::makeInertiaRoute('get', 'order/agency/create', 'admin.panel.order.agency.create', 'Panel/Admin/Order/Agency/Create', ['order_statuses' => collect(Variable::ORDER_STATUSES)->filter(fn($e) => $e['name'] != 'request'),]);
        Route::get('order/agency/search', [RepositoryOrderController::class, 'searchPanel'])->name('admin.panel.order.agency.search');
        Route::patch('order/agency/update', [RepositoryOrderController::class, 'update'])->name('admin.panel.order.agency.update');
        Route::get('order/agency/{order}', [RepositoryOrderController::class, 'edit'])->name('admin.panel.order.agency.edit');
        Route::get('order/agency/factor/{order}', [RepositoryOrderController::class, 'factor'])->name('admin.panel.order.agency.factor');

        Route::get('order/merged/search', [OrderController::class, 'searchMerged'])->name('admin.panel.order.merged.search');

        PanelController::makeInertiaRoute('get', 'shipping/method/index', 'admin.panel.shipping-method.index', 'Panel/Admin/Shipping/Method/Index',
            []
        );
        PanelController::makeInertiaRoute('get', 'shipping/method/create', 'admin.panel.shipping-method.create', 'Panel/Admin/Shipping/Method/Create',
            [
                'help' => __('help.shipping_method'),
            ]
        );
        Route::get('shipping/method/search', [ShippingMethodController::class, 'searchPanel'])->name('admin.panel.shipping-method.search');
        Route::patch('shipping/method/update', [ShippingMethodController::class, 'update'])->name('admin.panel.shipping-method.update');
        Route::post('shipping/method/create', [ShippingMethodController::class, 'create'])->name('admin.panel.shipping-method.create')->middleware("can:create,App\Models\Admin,App\Models\ShippingMethod,'1'");
        Route::get('shipping/method/{shipping_method}', [ShippingMethodController::class, 'edit'])->name('admin.panel.shipping-method.edit');


        PanelController::makeInertiaRoute('get', 'shipping/index', 'admin.panel.shipping.index', 'Panel/Admin/Shipping/Index',
            ['shipping_statuses' => Variable::SHIPPING_STATUSES]
        );
        PanelController::makeInertiaRoute('get', 'shipping/create', 'admin.panel.shipping.create', 'Panel/Admin/Shipping/Create',
            ['order_statuses' => Variable::ORDER_STATUSES]
        );
        Route::get('shipping/search', [ShippingController::class, 'searchPanel'])->name('admin.panel.shipping.search');
        Route::patch('shipping/update', [ShippingController::class, 'update'])->name('admin.panel.shipping.update');
        Route::post('shipping/create', [ShippingController::class, 'create'])->name('admin.panel.shipping.create')->middleware("can:create,App\Models\Admin,App\Models\Shipping,'1'");
        Route::get('shipping/{shipping}', [ShippingController::class, 'edit'])->name('admin.panel.shipping.edit');


        PanelController::makeInertiaRoute('get', 'pack/index', 'admin.panel.pack.index', 'Panel/Admin/Pack/Index',
            []
        );

        PanelController::makeInertiaRoute('get', 'pack/create', 'admin.panel.pack.create', 'Panel/Admin/Pack/Create',
            [], "can:create,App\Models\Admin,App\Models\Pack,'1'"
        );

        Route::get('pack/search', [PackController::class, 'searchPanel'])->name('admin.panel.pack.search');
        Route::patch('pack/update', [PackController::class, 'update'])->name('admin.panel.pack.update');
        Route::post('pack/create', [PackController::class, 'create'])->name('admin.panel.pack.create')->middleware("can:create,App\Models\Admin,App\Models\Pack,'1'");
        Route::get('pack/{pack}', [PackController::class, 'edit'])->name('admin.panel.pack.edit');


        PanelController::makeInertiaRoute('get', 'product/index', 'admin.panel.product.index', 'Panel/Admin/Product/Index',
            []
        );

        PanelController::makeInertiaRoute('get', 'product/create', 'admin.panel.product.create', 'Panel/Admin/Product/Create',
            [

            ]
        );

        Route::get('product/tree', [ProductController::class, 'getTree'])->name('admin.panel.product.tree');
        Route::get('product/search', [ProductController::class, 'searchPanel'])->name('admin.panel.product.search')->middleware("can:view,App\Models\Admin,App\Models\Product,'1'");
        Route::patch('product/update', [ProductController::class, 'update'])->name('admin.panel.product.update');
        Route::post('product/create', [ProductController::class, 'create'])->name('admin.panel.product.create')->middleware("can:create,App\Models\Admin,App\Models\Product,'1'");
        Route::get('product/{product}', [ProductController::class, 'edit'])->name('admin.panel.product.edit');

        PanelController::makeInertiaRoute('get', 'repository/shop/cart', 'admin.panel.repository.shop.cart', 'Panel/Admin/Repository/Shop/Cart',
            []
        );


        PanelController::makeInertiaRoute('get', 'variation/create', 'admin.panel.variation.create', 'Panel/Admin/Variation/Create', []);

        Route::get('variation/index', [VariationController::class, 'index'])->name('admin.panel.variation.index')->middleware("can:view,App\Models\Admin,App\Models\Variation,'1'");
        Route::get('variation/search', [VariationController::class, 'searchPanel'])->name('admin.panel.variation.search')->middleware("can:view,App\Models\Admin,App\Models\Variation,'1'");
        Route::patch('variation/update', [VariationController::class, 'update'])->name('admin.panel.variation.update');
        Route::post('variation/create', [VariationController::class, 'create'])->name('admin.panel.variation.create')->middleware("can:create,App\Models\Admin,App\Models\Variation,'1'");
        Route::get('variation/{Variation}', [VariationController::class, 'edit'])->name('admin.panel.variation.edit');


        PanelController::makeInertiaRoute('get', 'shipping/driver/index', 'admin.panel.shipping.driver.index', 'Panel/Admin/Shipping/Driver/Index', []);

        PanelController::makeInertiaRoute('get', 'shipping/driver/create', 'admin.panel.shipping.driver.create', 'Panel/Admin/Shipping/Driver/Create', [], "can:create,App\Models\Admin,App\Models\Driver,'1'"
        );

        Route::get('shipping/driver/search', [DriverController::class, 'searchPanel'])->name('admin.panel.shipping.driver.search');
        Route::patch('shipping/driver/update', [DriverController::class, 'update'])->name('admin.panel.shipping.driver.update');
        Route::post('shipping/driver/create', [DriverController::class, 'create'])->name('admin.panel.shipping.driver.create')->middleware("can:create,App\Models\Admin,App\Models\Driver,'1'");
        Route::get('shipping/driver/{driver}', [DriverController::class, 'edit'])->name('admin.panel.shipping.driver.edit');

        PanelController::makeInertiaRoute('get', 'shipping/car/index', 'admin.panel.shipping.car.index', 'Panel/Admin/Shipping/Car/Index', []);

        PanelController::makeInertiaRoute('get', 'shipping/car/create', 'admin.panel.shipping.car.create', 'Panel/Admin/Shipping/Car/Create', [], "can:create,App\Models\Admin,App\Models\Car,'1'"
        );

        Route::get('shipping/car/search', [CarController::class, 'searchPanel'])->name('admin.panel.shipping.car.search');
        Route::patch('shipping/car/update', [CarController::class, 'update'])->name('admin.panel.shipping.car.update');
        Route::post('shipping/car/create', [CarController::class, 'create'])->name('admin.panel.shipping.car.create')->middleware("can:create,App\Models\Admin,App\Models\Car,'1'");
        Route::get('shipping/car/{car}', [CarController::class, 'edit'])->name('admin.panel.shipping.car.edit');


        PanelController::makeInertiaRoute('get', 'password/edit', 'admin.panel.profile.password.edit', 'Panel/Profile/PasswordEdit',
            [
            ]);
        Route::get('profile/edit', [ProfileController::class, 'edit'])->name('admin.panel.profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('admin.panel.profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('admin.panel.profile.destroy');
        Route::patch('/profile/reset-password', [ProfileController::class, 'resetPassword'])->name('admin.panel.profile.password.reset');


    });

});
