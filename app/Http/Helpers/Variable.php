<?php

namespace App\Http\Helpers;

use App\Models\Admin;
use App\Models\AdminFinancial;
use App\Models\Agency;
use App\Models\AgencyFinancial;
use App\Models\Article;
use App\Models\ArticleTransaction;
use App\Models\Banner;
use App\Models\BannerTransaction;
use App\Models\Business;
use App\Models\BusinessTransaction;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Message;
use App\Models\Order;
use App\Models\Podcast;
use App\Models\PodcastTransaction;
use App\Models\Product;
use App\Models\RepositoryOrder;
use App\Models\Setting;
use App\Models\Site;
use App\Models\SiteTransaction;
use App\Models\Slider;
use App\Models\Text;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserFinancial;
use App\Models\Variation;
use App\Models\Video;
use App\Models\VideoTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;

class Variable
{


    const LANGS = ['fa', 'en', 'ar'];

    const MARKETS = ['bazaar', 'myket', 'playstore', 'site'];
    const GATEWAYS = ['bazaar', 'myket', 'nextpay'];

    const USER_ROLES = ['us'];
    const ADMIN_ROLES = ['god', 'owner', 'admin'];
    const AGENCY_TYPES = [
        ['id' => 0, 'name' => 'central', 'level' => '0'],
        ['id' => 1, 'name' => 'zone_agency', 'level' => '1'],
        ['id' => 2, 'name' => 'province_agency', 'level' => '2'],
        ['id' => 3, 'name' => 'branch_agency', 'level' => '3']
//        ['name' => 'branch', 'code' => 4],
    ];
    const PRODUCT_UNITS = ['qty', 'kg'/*, 'gr'*/];

    const ADMIN_ACCESS = ['all'];
    const GRADES = ['ممتاز','1', '2' ];
    const PARTNERSHIP_TYPES = [
        ['name' => 'agency', 'color' => 'gray'],
        ['name' => 'farmer', 'color' => 'teal'],
        ['name' => 'gardener', 'color' => 'lemon'],

    ];

    const  STATUSES = [
        ["name" => 'inactive', "color" => 'gray'],
        ["name" => 'active', "color" => 'success'],
        ["name" => 'block', "color" => 'danger'],
    ];
    const  USER_STATUSES = [
        ["name" => 'active', "color" => 'success'],
        ["name" => 'inactive', "color" => 'gray'],
        ["name" => 'block', "color" => 'danger'],
    ];
    const  TICKET_STATUSES = [
        ["name" => 'review', "color" => 'danger'],
        ["name" => 'closed', "color" => 'gray'],
        ["name" => 'responded', "color" => 'success'],
    ];
    const  MESSAGE_STATUSES = [
        ["name" => 'order', "color" => 'teal'],
        ["name" => 'referral', "color" => 'blue'],
    ];
    const  ORDER_STATUSES = [

        ["name" => 'request', "color" => 'violet'],
        ["name" => 'pending', "color" => 'danger'],
        ["name" => 'processing', "color" => 'orange'],
        ["name" => 'ready', "color" => 'green'],
        ["name" => 'sending', "color" => 'blue'],
        ["name" => 'delivered', "color" => 'gray'],
        ["name" => 'canceled', "color" => 'gray'],
        ["name" => 'rejected', "color" => 'gray'],
        ["name" => 'refunded', "color" => 'gray'],
    ];
    const  SHIPPING_STATUSES = [

        ["name" => 'preparing', "color" => 'orange'],
        ["name" => 'sending', "color" => 'blue'],
        ["name" => 'done', "color" => 'success'],
        ["name" => 'canceled', "color" => 'gray'],

    ];
    const CATEGORIES = [
        ['name' => 'fruit_vegetable', 'color' => 'teal'],
        ['name' => 'protein', 'color' => 'rose'],
        ['name' => 'beans', 'color' => 'indigo'],


    ];
    const TIMESTAMPS = [
        ['from' => 7, 'to' => 10, 'active' => true],
        ['from' => 10, 'to' => 13, 'active' => true],
        ['from' => 13, 'to' => 16, 'active' => true],
        ['from' => 16, 'to' => 19, 'active' => true],
        ['from' => 7, 'to' => 10, 'active' => true],
        ['from' => 10, 'to' => 13, 'active' => true],
        ['from' => 13, 'to' => 16, 'active' => true],
        ['from' => 16, 'to' => 19, 'active' => true],
    ];

    const SUCCESS_STATUS = 200;
    const ERROR_STATUS = 422;

    const  TRANSACTION_TYPES = ['pay', 'profit', 'settlement', 'charge', 'shipping'];
    const  FINANCIALS = ['admin' => AdminFinancial::class, 'user' => UserFinancial::class, 'agency' => AgencyFinancial::class];
    const  TRANSACTION_MODELS = ['order' => Order::class, 'repo-order' => RepositoryOrder::class, 'admin' => Admin::class, 'user' => User::class, 'agency' => Agency::class];
    const  PAYER_TYPES = ['admin' => Admin::class, 'user' => User::class, 'agency' => Agency::class];
    const REF_TYPES = ['register',];
    const BANK_GATEWAY = 'nextpay';
    const PAY_TIMEOUT = 1;
    const BANNER_IMAGE_LIMIT_MB = 10;
    const SITE_IMAGE_LIMIT_MB = 4;
    const BUSINESS_IMAGE_LIMIT = 4;
    const TICKET_ATTACHMENT_MAX_LEN = 5;

    const TICKET_ATTACHMENT_ALLOWED_MIMES = ['jpeg', 'jpg', 'png', 'txt', 'pdf'];
    const BANNER_ALLOWED_MIMES = ['jpeg', 'jpg', 'png'];
    const PRODUCT_IMAGE_LIMIT_MB = 50;
    const DRIVER_IMAGE_LIMIT_MB = 10;
    const VARIATION_IMAGE_LIMIT = 5;

    const PRODUCT_ALLOWED_MIMES = ['jpeg', 'jpg', 'png'];
    const DRIVER_ALLOWED_MIMES = ['jpeg', 'jpg', 'png'];


    const MIN_SELL_PRICE = 5000;
    const PODCAST_ALLOWED_MIMES = ['mp3', 'mpga'];
    const VIDEO_ALLOWED_MIMES = ['mp4',];
    const LOGS = [72534783, 1212754313];
    const PAGINATE = [24, 50, 100];
    const IMAGE_FOLDERS = [
        Article::class => 'articles',
        Ticket::class => 'tickets',
        User::class => 'users',
        Admin::class => 'admins',
        Slider::class => 'slides',
        Product::class => 'products',
        Variation::class => 'variations',
        Driver::class => 'drivers',
        Car::class => 'cars',
    ];
    const NOTIFICATION_TYPES = [
        "pay",
        "access_change",
        "ticket_answer"
    ];

    const PROJECT_ITEMS = [
        ['name' => 'podcast', 'color' => 'sky',],
        ['name' => 'video', 'color' => 'purple',],
        ['name' => 'banner', 'color' => 'orange',],
        ['name' => 'text', 'color' => 'rose',]

    ];


    const NOTIFICATION_LIMIT = 5;
    const CITY_ID = null; //61 تجریش
    const RATIOS = ['slider' => 1.8];
    const PACKAGE = 'com.dabel.dabelchin';
    const TELEGRAM_BOT = 'dabelchinbot';
    const LINKS = ['bazaar' => '', 'myket' => '', 'playstore' => ''];

    static $CITIES = [];
    public static $BANK = 'zarinpal';

    static function getPaymentMethods()
    {
        return [
            ['name' => __('online_payment'), 'key' => 'online', 'selected' => true, 'active' => true],
            ['name' => __('local_payment'), 'key' => 'local', 'selected' => true, 'active' => false]
        ];
    }

    static function getAdmins()
    {
        return [

            ['id' => 1, 'fullname' => 'مدیر مرکزی', 'phone' => '09351414815', 'status' => 'active', 'role' => 'owner', 'agency_id' => 1, 'agency_level' => '0',
                'access' => json_encode(['all']), 'email' => 'moj2raj2@gmail.com', 'password' => Hash::make(env('ADMIN_PASSWORD')), 'email_verified_at' => Carbon::now(), 'created_at' => Carbon::now(), 'phone_verified' => true,
            ],
            ['id' => 2, 'fullname' => 'سهیل لطیفی', 'phone' => '09121391009', 'status' => 'active', 'role' => 'owner', 'agency_id' => 10, 'agency_level' => '3',
                'access' => json_encode(['all']), 'email' => null, 'password' => null, 'email_verified_at' => Carbon::now(), 'created_at' => Carbon::now(), 'phone_verified' => true,
            ],
        ];
    }

    static function getUsers()
    {
        return [

            ['id' => 1, 'fullname' => 'رجبی', 'phone' => '09018945844', 'status' => 'active', 'ref_id' => 'develowper',
                'access' => json_encode(['all']), 'email' => 'moj2raj2@gmail.com', 'password' => Hash::make('o7615564351'), 'email_verified_at' => Carbon::now(), 'created_at' => Carbon::now(), 'phone_verified' => true,
            ],
            ['id' => 2, 'fullname' => 'داریوش بهشتی', 'phone' => '09351414815', 'status' => 'active', 'ref_id' => 'dabel',
                'access' => json_encode(['all']), 'email' => null, 'password' => Hash::make('Dd20552055'), 'email_verified_at' => Carbon::now(), 'created_at' => Carbon::now(), 'phone_verified' => true,
            ],
        ];
    }

    static function getSettings()
    {
        return [
            ['key' => 'hero_main_page', 'value' => __('hero_main_page'), "created_at" => \Carbon\Carbon::now(),],
            ['key' => 'social_telegram', 'value' => 'lord2095', "created_at" => \Carbon\Carbon::now(),],
            ['key' => 'social_whatsapp', 'value' => '00989351414815', "created_at" => \Carbon\Carbon::now(),],
            ['key' => 'social_email', 'value' => 'info@tapchin.ir', "created_at" => \Carbon\Carbon::now(),],
            ['key' => 'social_phone', 'value' => '09351414815', "created_at" => \Carbon\Carbon::now(),],
            ['key' => 'social_address', 'value' => __('social_address'), "created_at" => \Carbon\Carbon::now(),],
            ['key' => 'order_reserve_minutes', 'value' => 30, "created_at" => \Carbon\Carbon::now(),],
            ['key' => 'order_percent_level_0', 'value' => 15, "created_at" => \Carbon\Carbon::now(),],
            ['key' => 'order_percent_level_1', 'value' => 0.5, "created_at" => \Carbon\Carbon::now(),],
            ['key' => 'order_percent_level_2', 'value' => 82, "created_at" => \Carbon\Carbon::now(),],
            ['key' => 'order_percent_level_3', 'value' => 2.5, "created_at" => \Carbon\Carbon::now(),],

        ];
    }

    public static function getDefaultShippingMethods()
    {
        return [
            [
                'id' => 1,
                'repo_id' => null,
                'products' => null,
                'cities' => null,
                'min_order_weight' => 0,
                'per_weight_price' => 0,
                'base_price' => 0,
                'free_from_price' => null,
                'description' => 'مشتری با مراجعه به انبار، کالای خود را دریافت می نماید',
                'name' => 'دریافت حضوری از انبار',
            ],

        ];
    }

}
