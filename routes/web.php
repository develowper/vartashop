<?php

use App\Http\Controllers\AdController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BusinessController;

use App\Http\Controllers\CartController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\HireController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\PartnershipController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PodcastController;
use App\Http\Controllers\VariationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectItemController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\TextController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Helpers\Pay;
use App\Http\Helpers\SmsHelper;
use App\Http\Helpers\Telegram;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Business;
use App\Models\Category;
use App\Models\City;
use App\Models\County;
use App\Models\Notification;
use App\Models\Podcast;
use App\Models\Province;
use App\Models\Repository;
use App\Models\Setting;
use App\Models\Site;
use App\Models\Text;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/cache', function () {
    Artisan::call('optimize:clear');
    echo Artisan::output();
});
Route::get('test', function () {

    return;
    if (!File::exists("storage/app/public/variations/198")) {
//            Storage::makeDirectory("public/$type", 766);
        File::makeDirectory(Storage::path("public/variations/198"), $mode = 0755,);
    }
    return File::copy(storage_path("app/public/variations/182/thumb.jpg"), storage_path("app/public/variations/198/thumb.jpg"));
    return;
    return;
    return \Illuminate\Support\Facades\Artisan::call('store:transactions');
    return (new ArticleController())->search(new Request([]));
    return;
    return (new PanelController())->chartLogs(new Request(['user_id' => 1, 'dateFrom' => '1401/06/01', 'dateTo' => '1402/06/24']));
//    File::makeDirectory(Storage::path("public/sites"), $mode = 0755,);
//    return Telegram::log(null, 'site_created', Site::find(2));

});
Route::get('panel')->name('panel');
Route::get('storage')->name('storage');
Route::get('storage/sites')->name('storage.sites');
Route::get('storage/users')->name('storage.users');
Route::get('storage/businesses')->name('storage.businesses');
Route::get('storage/podcasts')->name('storage.podcasts');
Route::get('storage/videos')->name('storage.videos');
Route::get('storage/banners')->name('storage.banners');
Route::get('storage/articles')->name('storage.articles');
Route::get('storage/tickets')->name('storage.tickets');
Route::get('storage/slides')->name('storage.slides');
Route::get('storage/variations')->name('storage.variations');
Route::get('storage/products')->name('storage.products');
Route::get('storage/admins')->name('storage.admins');
Route::get('storage/drivers')->name('storage.drivers');
Route::get('storage/cars')->name('storage.cars');

Route::get('/', function (Request $request) {
    if ($r = $request->ref) {
        session(['ref' => $r]);
    }
//    Telegram::log(null, 'order_created', \App\Models\Order::with('items')->with('agency')->orderBy('id', 'DESC')->first());


    return Inertia::render('Main', [
        'heroText' => \App\Models\Setting::getValue('hero_main_page'),
        'slides' => \App\Models\Slider::where('is_active', true)->get(),
        'articles' => \App\Models\Article::where('status', 'active')->orderBy('id', 'desc')->take(12)->get(),
        'section1Header' => __('our_services'),
        'section1' => [
            ['header' => 'تحویل در محل', 'sub' => 'سفارش خود را در سریعترین زمان در محل تحویل بگیرید', 'icon' => 'TruckIcon'],
            ['header' => 'قیمت به صرفه', 'sub' => 'با حذف واسطه ها، حمل و نقل هوشمند و انبارهای اختصاصی، قیمت تمام شده کالا را به حداقل می رسانیم', 'icon' => 'RocketLaunchIcon'],
            ['header' => 'پاسخگویی شبانه روزی', 'sub' => 'در هر زمان از شبانه روز پاسخگوی شما خوهیم بود', 'icon' => 'UsersIcon'],
            ['header' => 'جذب نمایندگی فعال', 'sub' => 'در صورت داشتن امکاناتی چون انبار و وسایل حمل و نقل در سراسر کشور با ما تماس بگیرید', 'icon' => 'MapPinIcon'],
        ],
        'section2Header' => __('our_benefits'),
        'section2' => [
        ],
        'carouselImages' => [],
        'counts' => [
            'users' => ['icon' => 'UsersIcon', 'count' => User::count()],
            'articles' => ['icon' => 'PencilIcon', 'count' => Article::count()],
        ]
    ]);
})->name('/');

Route::group(['prefix' => '', 'namespace' => 'User'], function () {
    Route::middleware(['auth:sanctum',
        config('jetstream.auth_session'),
        'verified'])->prefix('panel')->group(function ($route) {


        Route::get('', [PanelController::class, 'index'])->name('user.panel.index');

        Route::post('transaction/chart', [PanelController::class, 'chartLogs'])->name('transaction.chart');


//    PanelController::makeInertiaRoute('get', 'site/edit/{site}', 'panel.site.edit', 'Panel/Site/Edit', ['categories' => Site::categories('parents'), 'site_statuses' => Variable::SITE_STATUSES, 'site' => $tmp = Site::with('category')->find(request()->segment(count(request()->segments())))], 'can:edit,App\Models\User,App\Models\Site,"","' . $tmp . '"');


        PanelController::makeInertiaRoute('get', 'password/edit', 'user.panel.profile.password.edit', 'Panel/Profile/PasswordEdit',
            [
            ]);
        Route::get('profile/edit', [ProfileController::class, 'edit'])->name('user.panel.profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('user.panel.profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('user.panel.profile.destroy');
        Route::patch('/profile/reset-password', [ProfileController::class, 'resetPassword'])->name('user.panel.profile.password.reset');


        PanelController::makeInertiaRoute('get', 'notification/index', 'panel.notification.index', 'Panel/Notification/Index',
            [
            ]
        );
        PanelController::makeInertiaRoute('get', 'ticket/index', 'panel.ticket.index', 'Panel/Ticket/Index',
            [
                'statuses' => Variable::TICKET_STATUSES

            ]);
        PanelController::makeInertiaRoute('get', 'ticket/create', 'panel.ticket.create', 'Panel/Ticket/Create',
            [
                'attachment_allowed_mimes' => implode(',.', Variable::TICKET_ATTACHMENT_ALLOWED_MIMES),
            ]);


        PanelController::makeInertiaRoute('get', 'order/index', 'user.panel.order.index', 'Panel/User/Order/Index', ['order_statuses' => collect(Variable::ORDER_STATUSES)->filter(fn($e) => $e['name'] != 'request'),]);
        Route::get('order/search', [OrderController::class, 'searchPanel'])->name('user.panel.order.search');
        Route::patch('order/update', [OrderController::class, 'userUpdate'])->name('user.panel.order.update');
        Route::get('order/{order}', [OrderController::class, 'edit'])->name('user.panel.order.edit');

        PanelController::makeInertiaRoute('get', 'transaction/index', 'user.panel.financial.transaction.index', 'Panel/Financial/Transaction/Index',
            []
        );
        Route::get('transaction/search', [TransactionController::class, 'searchPanel'])->name('user.panel.financial.transaction.search');

    });

});


Route::post('article/create', [ArticleController::class, 'create'])->name('article.create')->middleware('can:create,App\Models\User,App\Models\Article,""');

Route::post('ticket/create', [TicketController::class, 'create'])->name('ticket.create')->middleware('can:create,App\Models\User,App\Models\Ticket,""');


Route::middleware('throttle:6,1')->group(function () {
    Route::post('sms/send', [MainController::class, 'sendSms'])->name('sms.send.verification');
    Route::post('message/create', [MessageController::class, 'create'])->name('message.create');

});

Route::middleware(['auth:sanctum',
    config('jetstream.auth_session'),
    'verified'])->group(function () {
//    Route::patch('/user', [UserController::class, 'update'])->name('user.update');


    PanelController::makeInertiaRoute('get', 'notification/index', 'user.panel.notification.index', 'Panel/Notification/Index',
        [

        ]);
    PanelController::makeInertiaRoute('get', 'notification/create', 'user.panel.notification.create', 'Panel/Notification/Create',
        [

        ]);
    Route::get('notification/search', [NotificationController::class, 'searchPanel'])->name('panel.notification.search');
    Route::get('notification/edit/{notification}', [NotificationController::class, 'edit'])->name('panel.notification.edit');
    Route::patch('notification/update', [NotificationController::class, 'update'])->name('panel.notification.update');

    PanelController::makeInertiaRoute('get', 'ticket/index', 'user.panel.ticket.index', 'Panel/Ticket/Index',
        [
            'ticket_statuses' => Variable::TICKET_STATUSES
        ]);
    PanelController::makeInertiaRoute('get', 'ticket/create', 'user.panel.ticket.create', 'Panel/Ticket/Create',
        [
        ]);
    Route::get('ticket/search', [TicketController::class, 'searchPanel'])->name('panel.ticket.search');
    Route::patch('ticket/update', [TicketController::class, 'update'])->name('panel.ticket.update');
    Route::get('ticket/{ticket}', [TicketController::class, 'edit'])->name('panel.ticket.edit');


    Route::get('checkout/shipping', [ShopController::class, 'shippingPage'])->name('checkout.shipping');
    Route::get('checkout/payment', [ShopController::class, 'paymentPage'])->name('checkout.payment');
    Route::post('order/create', [OrderController::class, 'create'])->name('order.create');
    Route::get('order/{order}', [OrderController::class, 'edit'])->name('order.edit');

    Route::get('order/factor/{order}', [OrderController::class, 'factor'])->name('user.panel.order.factor');


});


Route::get('/make_money', [MainController::class, 'makeMoneyPage'])->name('page.make_money');
Route::get('/prices', [MainController::class, 'pricesPage'])->name('page.prices');
Route::get('/help', [MainController::class, 'helpPage'])->name('page.help');
Route::get('/contact_us', [MainController::class, 'contactUsPage'])->name('page.contact_us');
Route::get('/exchange', [ExchangeController::class, 'index'])->name('exchange.index');


Route::get('/articles', [ArticleController::class, 'index'])->name('article.index');
Route::get('/article/search', [ArticleController::class, 'search'])->name('article.search');
Route::post('article/view', [ArticleController::class, 'increaseView'])->name('article.view');
Route::get('article/{article}', [ArticleController::class, 'view'])->name('article');

Route::get('/variation/search', [VariationController::class, 'search'])->name('variation.search');
Route::get('/variation/{id}/{name}', [VariationController::class, 'view'])->name('variation.view');

Route::patch('/cart/update', [CartController::class, 'update'])->name('cart.update');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/checkout/cart', [ShopController::class, 'cartPage'])->name('checkout.cart');

Route::post('/update_location', [UserController::class, 'updateLocation'])->name('user.update_location');

Route::get('language/{language}', function ($language) {
    session()->put('locale', $language);
    return;
})->name('language');

Route::post('partnership/create', [PartnershipController::class, 'create'])->name('partnership.create');

//
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
