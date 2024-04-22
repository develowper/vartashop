<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Models\Agency;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Business;
use App\Models\Category;
use App\Models\City;
use App\Models\County;
use App\Models\Doc;
use App\Models\Notification;
use App\Models\Pack;
use App\Models\Payment;
use App\Models\Podcast;
use App\Models\Product;
use App\Models\Variation;
use App\Models\Repository;
use App\Models\Site;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    private \Faker\Generator $faker;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = Faker::create('fa_IR');
        if (DB::connection()->getDriverName() == 'mysql')
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->productionData();
        return;
        $this->createCities();
        $this->createUsers(20);
        $this->createAgencies(20);
        $this->createAdmins(20);
        $this->createPacks(20);
        $this->createRepositories(20);
        $this->createShippingMethods(20);
        $this->createProducts();
        $this->createVariations();


    }

    private function productionData()
    {
        $this->createCities();

        DB::table('users')->truncate();
        DB::table('users')->insert(Variable::getUsers());

        DB::table('admins')->truncate();
        DB::table('admins')->insert(Variable::getAdmins());

        $levels = array_column(Variable::AGENCY_TYPES, 'level');
        DB::table('agencies')->truncate();
        DB::table('agencies')->insert([
            [
                'id' => 1,
                'name' => 'دفتر مرکزی',
                'access' => null,
                'parent_id' => null,
//                'has_shop' => true,
                'level' => strval($levels[0]),
//                'owner_id' => 2,
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'تهران')->first()->id,
                'address' => 'تهران',
                'status' => 'active',
                'postal_code' => null,
            ], [
                'id' => 2,
                'name' => 'مرکز ایران (تهران)',
                'parent_id' => 1,
                'access' => json_encode(City::where('level', 1)->whereIn('name', ['تهران'])->pluck('id')),
                'level' => strval($levels[1]),
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'تهران')->first()->id,
                'address' => 'تهران',
                'postal_code' => null,
                'status' => 'active',
            ], [
                'id' => 3,
                'name' => 'مرکز ایران',
                'parent_id' => 1,
                'access' => json_encode(City::where('level', 1)->whereIn('name', ['اصفهان', 'فارس', 'یزد', 'چهارمحال و بختیاری', 'قم', 'مرکزی'])->pluck('id')),
                'level' => strval($levels[1]),
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'تهران')->first()->id,
                'address' => 'تهران',
                'postal_code' => null,
                'status' => 'active',
            ], [
                'id' => 4,
                'name' => 'شمال ایران',
                'parent_id' => 1,
                'access' => json_encode(City::where('level', 1)->whereIn('name', ['گلستان', 'مازندران', 'گیلان', 'قزوین', 'البرز'])->pluck('id')),
                'level' => strval($levels[1]),
                'province_id' => null,
                'county_id' => null,
                'address' => '-',
                'postal_code' => null,
                'status' => 'active',
            ], [
                'id' => 5,
                'name' => 'جنوب/جنوب‌شرق ایران',
                'parent_id' => 1,
                'access' => json_encode(City::where('level', 1)->whereIn('name', ['هرمزگان', 'کرمان', 'سیستان و بلوچستان'])->pluck('id')),
                'level' => strval($levels[1]),
                'province_id' => null,
                'county_id' => null,
                'address' => '-',
                'postal_code' => null,
                'status' => 'active',
            ], [
                'id' => 6,
                'name' => 'شمال‌شرق ایران',
                'parent_id' => 1,
                'access' => json_encode(City::where('level', 1)->whereIn('name', ['خراسان شمالی', 'خراسان رضوی', 'خراسان جنوبی', 'سمنان'])->pluck('id')),
                'level' => strval($levels[1]),
                'province_id' => null,
                'county_id' => null,
                'address' => '-',
                'postal_code' => null,
                'status' => 'active',
            ], [
                'id' => 7,
                'name' => 'جنوب‌غرب ایران',
                'parent_id' => 1,
                'access' => json_encode(City::where('level', 1)->whereIn('name', ['خوزستان', 'بوشهر', 'کهگیلویه و بویراحمد', 'لرستان', 'ایلام'])->pluck('id')),
                'level' => strval($levels[1]),
                'province_id' => null,
                'county_id' => null,
                'address' => '-',
                'postal_code' => null,
                'status' => 'active',
            ], [
                'id' => 8,
                'name' => 'غرب/شمال‌غرب ایران',
                'parent_id' => 1,
                'access' => json_encode(City::where('level', 1)->whereIn('name', ['اردبیل', 'آذربایجان شرقی', 'آذربایجان غربی', 'زنجان', 'کردستان', 'کرمانشاه', 'همدان'])->pluck('id')),
                'level' => strval($levels[1]),
                'province_id' => null,
                'county_id' => null,
                'address' => '-',
                'postal_code' => null,
                'status' => 'active',
            ], [
                'id' => 9,
                'name' => 'انحصاری استان تهران',
                'parent_id' => 2,
                'access' => json_encode([10]),
                'level' => strval($levels[2]),
                'province_id' => json_encode(City::where('level', 1)->where('name', 'تهران')->first()->id ?? null),
                'county_id' => json_encode(City::where('level', 2)->where('name', 'تهران')->first()->id ?? null),
                'address' => 'تهران، منطقه 16، محله نازی آباد، میدان بهمن، خیابان دشت آزادگان، جنب شهرداری منطقه 16',
                'postal_code' => '1811813453',
                'status' => 'active',
            ], [
                'id' => 10,
                'name' => 'شعبه لطفی شهر قدس تهران',
                'parent_id' => 9,
                'access' => json_encode([]),
                'level' => strval($levels[3]),
                'province_id' => json_encode(City::where('level', 1)->where('name', 'تهران')->first()->id ?? null),
                'county_id' => json_encode(City::where('level', 2)->where('name', 'تهران')->first()->id ?? null),
                'address' => 'شهر قدس- میدان مصلی',
                'postal_code' => '3751847387',
                'status' => 'active',
            ],
        ]);
        DB::table('repositories')->truncate();
        DB::table('repositories')->insert([
            [
                'id' => 1,
                'name' => 'انبار انحصاری استان تهران',
                'agency_id' => 9,
                'is_shop' => false,
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'تهران')->first()->id,
                'address' => 'تهران، منطقه 16، محله نازی آباد، میدان بهمن، خیابان دشت آزادگان، جنب شهرداری منطقه 16',
                'location' => '35.642897,51.3986079',
                'postal_code' => '1811813453',
                'status' => 'active',
                'cities' => json_encode([]),
            ], [
                'id' => 2,
                'name' => 'انبار شهر قدس لطفی',
                'agency_id' => 10,
                'is_shop' => true,
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'تهران')->first()->id,
                'address' => 'شهر قدس- میدان مصلی',
                'location' => '35.642897,51.3986079',
                'postal_code' => '3751847387',
                'status' => 'active',
                'cities' => json_encode([]),
            ],
        ]);
        $this->createPacks();
        $this->createProducts();
//        $this->createRepositories();
//        $this->createVariations();
    }

    private function createCities()
    {
        ini_set('max_execution_time', '0'); // for infinite time of execution
        if (DB::connection()->getDriverName() == 'mysql')
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
//        DB::statement("ALTER TABLE cities AUTO_INCREMENT = 2000;");

        City::where('level', 3)->delete();
        $areas = [
            ['name' => 'تهران', 'count' => 22],
            ['name' => 'رشت', 'count' => 5],
            ['name' => 'اصفهان', 'count' => 15],
            ['name' => 'اهواز', 'count' => 7],
            ['name' => 'اردبیل', 'count' => 5],
            ['name' => 'تبریز', 'count' => 10],
            ['name' => 'اراک', 'count' => 6],
            ['name' => 'یزد', 'count' => 10],
        ];
        foreach ($areas as $area) {
            $cityId = City::where('level', 2)->where('name', $area['name'])->first()->id;
            foreach (range(1, $area['count']) as $item) {
                City::create(
                    [
                        'level' => 3,
                        'name' => "منطقه $item",
                        'parent_id' => $cityId,
                        'slug' => "m-$item",
                    ]
                );
            }

        }
        foreach (City::get() as $item) {
            $item->has_child = City::where('parent_id', $item->id)->exists();
            $item->save();
        }
    }

    private
    function createUsers($count = 30)
    {

        DB::table('users')->truncate();
        DB::table('users')->insert(Variable::getUsers());

    }

    private
    function createAdmins($count = 30)
    {

        DB::table('admins')->truncate();
        \Illuminate\Support\Facades\DB::table('admins')->insert(\App\Http\Helpers\Variable::getAdmins());
        $agencyCount = 10;
        for ($i = 1; $i < $count; $i++) {
            $phone = $this->faker->numerify("091########");
            $agencyId = $i < $agencyCount ? $i + 1 : $this->faker->numberBetween(2, $agencyCount);
            DB::table('admins')->insert([
                [
//                    'id' => 2,
                    'fullname' => $this->faker->name,
                    'phone' => $phone,
                    'phone_verified' => true,
                    'password' => Hash::make($phone),
                    'status' => 'active',
                    'agency_id' => $agencyId,
                    'agency_level' => Agency::find($agencyId)->level,
                    'role' => $i < $agencyCount ? 'owner' : 'admin',
                    'access' => json_encode([]),
                ]
            ]);
        }

    }

    private
    function createAgencies($count = 30)
    {
//zone_agency access : provinces
        // province_agency access : agencies
        DB::table('agencies')->truncate();
        $levels = array_column(Variable::AGENCY_TYPES, 'level');


        //section agencies
        $provinces1 = City::where('level', 1)->inRandomOrder()->take(5)->pluck('id');
        $provinces2 = City::where('level', 1)->whereNotIn('id', $provinces1)->inRandomOrder()->take(5)->pluck('id');
        DB::table('agencies')->insert([
            [
                'id' => 1,
                'name' => 'دفتر مرکزی',
                'access' => null,
                'parent_id' => null,
//                'has_shop' => true,
                'level' => strval($levels[0]),
//                'owner_id' => 2,
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'تهران')->first()->id,
                'address' => 'تهران',
                'status' => 'active',
            ], [
                'id' => 2,
                'name' => 'نمایندگی جنوب ایران',
                'parent_id' => 1,
                'access' => json_encode(City::where('level', 1)->whereIn('name', ['خوزستان', 'بوشهر', 'هرمزگان'])->pluck('id')),
//                'has_shop' => false,
                'level' => strval($levels[1]),
//                'owner_id' => 2,
                'province_id' => City::where('level', 1)->where('name', 'خوزستان')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'اهواز')->first()->id,
                'address' => 'کوی کارگر',
                'status' => 'active',
            ],
            [
                'id' => 3,
                'name' => 'نمایندگی مرکز ایران',
                'access' => json_encode(City::where('level', 1)->whereIn('name', ['تهران', 'اصفهان', 'قزوین'])->pluck('id')),
                'parent_id' => 1,
//                'has_shop' => false,
                'level' => strval($levels[1]),
//                'owner_id' => 3,
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'تهران')->first()->id,
                'address' => 'میدان آزادی',
                'status' => 'active',

            ],
            [
                'id' => 4,
                'name' => 'نمایندگی استان اصفهان',
                'parent_id' => 3,
                'access' => json_encode([9, 10]),
//                'has_shop' => false,
                'level' => strval($levels[2]),
//                'owner_id' => 4,
                'province_id' => City::where('level', 1)->where('name', 'اصفهان')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'اصفهان')->first()->id,
                'address' => 'سه راه سیمین',
                'status' => 'active',

            ],
            [
                'id' => 5,
                'name' => 'نمایندگی استان تهران1',
                'access' => json_encode([7]),
//                'has_shop' => false,
                'parent_id' => 3,
                'level' => strval($levels[2]),
//                'owner_id' => 5,
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'تهران')->first()->id,
                'address' => 'میدان آرژانتین',
                'status' => 'active',

            ],
            [
                'id' => 6,
                'name' => 'نمایندگی استان تهران2',
                'access' => json_encode([8]),
//                'has_shop' => false,
                'parent_id' => 3,
                'level' => strval($levels[2]),
//                'owner_id' => 5,
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'تهران')->first()->id,
                'address' => 'تجریش',
                'status' => 'active',

            ],

            [
                'id' => 7,
                'name' => 'شعبه فیروزکوه',
                'parent_id' => 5,
                'access' => null,
//                'has_shop' => true,
                'level' => strval($levels[3]),
//                'owner_id' => 6,
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'فیروزکوه')->first()->id,
                'address' => 'فیروزکوه',
                'status' => 'active',

            ],
            [
                'id' => 8,
                'name' => 'شعبه اسلامشهر',
                'parent_id' => 6,
                'access' => null,
//                'has_shop' => true,
                'level' => strval($levels[3]),
//                'owner_id' => 7,
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'اسلام‌شهر')->first()->id,
                'address' => 'اسلامشهر',
                'status' => 'active',

            ],
            [
                'id' => 9,
                'name' => 'شعبه اصفهان',
                'parent_id' => 4,
                'access' => null,
//                'has_shop' => true,
                'level' => strval($levels[3]),
//                'owner_id' => 8,
                'province_id' => City::where('level', 1)->where('name', 'اصفهان')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'اصفهان')->first()->id,
                'address' => 'میدان شهدا',
                'status' => 'active',

            ],
            [
                'id' => 10,
                'name' => 'نمایندگی شعبه شهرضا',
                'parent_id' => 4,
                'access' => null,
//                'has_shop' => true,
                'level' => strval($levels[3]),
//                'owner_id' => 9,
                'province_id' => City::where('level', 1)->where('name', 'اصفهان')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'شهرضا')->first()->id,
                'address' => 'میدان شهدا',
                'status' => 'active',

            ],

        ]);


    }

    private
    function createPacks($count = 30)
    {

        DB::table('packs')->truncate();
        //section agencies
        DB::table('packs')->insert([
            [

                'name' => 'بدون بسته',
                'weight' => 1,
                'height' => 0,
                'length' => 0,
                'width' => 0,
                'price' => 0,

            ], [

                'name' => 'جعبه چوبی',
                'weight' => 500,
                'height' => 50,
                'length' => 80,
                'width' => 60,
                'price' => 0,

            ],
            [
                'name' => 'جعبه پلاستیکی',
                'weight' => 350,
                'height' => 50,
                'length' => 80,
                'width' => 60,
                'price' => 0,

            ],
            [
                'name' => 'گونی',
                'weight' => 100,
                'height' => 150,
                'length' => 80,
                'width' => 60,
                'price' => 0,
            ],
        ]);
    }

    private
    function createRepositories($count = 30)
    {


        DB::table('repositories')->truncate();
        //section agencies
        DB::table('repositories')->insert([
            [
                'id' => 1,
                'name' => 'انبار فیروزکوه',
                'agency_id' => 7,
                'is_shop' => true,
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'فیروزکوه')->first()->id,
                'address' => 'میدان امام خمینی, ولیعصر شرقی, فیروزکوه, بخش مرکزی فیروزکوه, شهرستان فیروزکوه, استان تهران, ایران',
                'location' => '35.759704549999995,52.77595990334879',
                'status' => 'active',
                'cities' => json_encode(array_merge(City::where('parent_id', City::where('level', 2)->where('name', 'تهران')->first()->id)->take(20)->inRandomOrder()->pluck('id')->toArray(), [])),
            ],
            [
                'id' => 2,
                'name' => 'انبار اسلامشهر',
                'agency_id' => 8,
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'اسلام‌شهر')->first()->id,
                'address' => 'شهرک صادقیه, اسلامشهر, بخش مرکزی اسلامشهر, شهرستان اسلامشهر, استان تهران, 33147-53135, ایران',
                'is_shop' => true,
                'location' => '35.55335329744479,51.23351527234567',
                'status' => 'active',
                'cities' => json_encode(array_merge(City::where('parent_id', City::where('level', 2)->where('name', 'تهران')->first()->id)->take(25)->inRandomOrder()->pluck('id')->toArray(), [])),

            ],
            [
                'id' => 3,
                'name' => 'انبار اصفهان',
                'agency_id' => 9,
                'province_id' => City::where('level', 1)->where('name', 'اصفهان')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'اصفهان')->first()->id,
                'address' => 'خیابان مشتاق',
                'is_shop' => true,
                'location' => null,
                'status' => 'active',
                'cities' => json_encode(array_merge(City::where('parent_id', City::where('level', 2)->where('name', 'اصفهان')->first()->id)->take(20)->inRandomOrder()->pluck('id')->toArray(), [1543])),
            ],
            [
                'id' => 4,
                'name' => 'انبار شهرضا',
                'agency_id' => 10,
                'province_id' => City::where('level', 1)->where('name', 'اصفهان')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'اصفهان')->first()->id,
                'address' => 'میدان شهدا',
                'is_shop' => true,
                'location' => null,
                'status' => 'active',
                'cities' => json_encode(array_merge(City::where('parent_id', City::where('level', 1)->where('name', 'اصفهان')->first()->id)->take(20)->inRandomOrder()->pluck('id')->toArray(), [])),
            ],
            [
                'id' => 5,
                'name' => 'انبار انحصاری تهران',
                'agency_id' => 5,
                'is_shop' => false,
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'فیروزکوه')->first()->id,
                'address' => 'تهران',
                'location' => null,
                'status' => 'active',
                'cities' => json_encode([1770]),
            ],
            [
                'id' => 6,
                'name' => 'انبار انحصاری اصفهان',
                'agency_id' => 4,
                'is_shop' => false,
                'province_id' => City::where('level', 1)->where('name', 'اصفهان')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'اصفهان')->first()->id,
                'address' => 'اصفهان',
                'location' => null,
                'status' => 'active',
                'cities' => null,
            ],
            [
                'id' => 7,
                'name' => 'انبار فیروزکوه 2',
                'agency_id' => 7,
                'is_shop' => true,
                'province_id' => City::where('level', 1)->where('name', 'تهران')->first()->id,
                'county_id' => City::where('level', 2)->where('name', 'فیروزکوه')->first()->id,
                'address' => 'فیروزکوه2',
                'location' => '35.759704549999995,52.77595990334879',
                'status' => 'active',
                'cities' => json_encode(array_merge(City::where('parent_id', City::where('level', 2)->where('name', 'تهران')->first()->id)->take(20)->inRandomOrder()->pluck('id')->toArray(), [])),
            ],

        ]);
    }

    private
    function createShippingMethods($count = 30)
    {


        DB::table('shipping_methods')->truncate();
        DB::table('shipping_methods')->insert(\App\Http\Helpers\Variable::getDefaultShippingMethods());

        //section agencies
        DB::table('shipping_methods')->insert([
            [
                'id' => 2,
                'repo_id' => 1,
                'agency_id' => 7,
                'products' => null,
                'cities' => null,
                'per_weight_price' => 5000,
                'base_price' => 10000,
                'free_from_price' => null,
                'min_order_weight' => 30,
                'description' => '',
                'name' => 'پخش فیروزکوه',
                'status' => 'active',
                'timestamps' => json_encode(Variable::TIMESTAMPS),

            ], [
                'id' => 3,
                'repo_id' => 1,
                'agency_id' => 7,
                'products' => json_encode(Variation::where('repo_id', 1)->inRandomOrder()->take(4)->pluck('id')->toArray()),
                'cities' => json_encode(collect(Repository::where('id', 1)->first()->cities)->shuffle()->take(10)),
                'per_weight_price' => 0,
                'base_price' => 12000,
                'free_from_price' => null,
                'description' => '',
                'name' => 'پخش فیروزکوه',
                'min_order_weight' => 30,
                'status' => 'active',
                'timestamps' => json_encode(Variable::TIMESTAMPS),


            ], [
                'id' => 4,
                'repo_id' => 1,
                'agency_id' => 7,
                'products' => json_encode(Variation::where('repo_id', 1)->inRandomOrder()->take(6)->pluck('id')->toArray()),
                'cities' => null,
                'per_weight_price' => 0,
                'base_price' => 15000,
                'free_from_price' => null,
                'description' => '',
                'min_order_weight' => 30,
                'name' => 'پخش ویژه فیروزکوه',
                'status' => 'active',
                'timestamps' => json_encode(Variable::TIMESTAMPS),


            ], [
                'id' => 5,
                'repo_id' => 2,
                'agency_id' => 8,
                'products' => null,
                'cities' => json_encode(collect(Repository::where('id', 1)->first()->cities)->shuffle()->take(10)),
                'per_weight_price' => 4000,
                'base_price' => 14000,
                'free_from_price' => null,
                'description' => '',
                'name' => 'پخش اسلامشهر',
                'min_order_weight' => 30,
                'status' => 'active',
                'timestamps' => json_encode(Variable::TIMESTAMPS),


            ], [
                'id' => 6,
                'repo_id' => 5,
                'agency_id' => 5,
                'products' => null,
                'cities' => null,
                'per_weight_price' => 500,
                'base_price' => 11000,
                'free_from_price' => null,
                'description' => '',
                'min_order_weight' => 30,
                'name' => 'پخش انحصاری تهران',
                'status' => 'active',
                'timestamps' => json_encode(Variable::TIMESTAMPS),


            ],

        ]);
    }

    private
    function createProducts()
    {
        DB::table('products')->truncate();
        File::deleteDirectory("storage/app/public/products");
        File::makeDirectory("storage/app/public/products");


        $prods = [

            'سیب زمینی',
            'پیاز',
            'گوجه',
            'خیار',
            'پرتقال',
            'سبزی',
            'بادمجان',
            'کدو',
            'هویج',
            'فلفل',
            'فلفل دلمه',
            'کاهو',
            'لیمو ترش',
            'لیمو شیرین',
            'لوبیا سبز',
            'موز',
            'انار',
            'انگور',
            'نارنگی',
            'نارنج',
            'غوره',
            'هلو',
            'آلو',
            'زرد آلو',
            'آلبالو',
            'گیلاس',
            'به',
            'انبه',
            'گوجه سبز',
            'چاقاله',
            'نارگیل',
            'آناناس',
            'توت',
            'شاه توت',
            'توت فرنگی',
            'انجیر',
            'گلابی',
            'ملون',
            'خربزه',
            'طالبی',
            'هندوانه',
            'کیوی',
            'ازگیل',
            'شلیل',
            'کلم',


        ];
        foreach ($prods as $prod) {
            $pp = Product::create([
                'name' => $prod,
                'status' => 'active',
                'category_id' => 1,
            ]);
            $this->makeFile("products", $pp->id, '.jpg', false);

        }


    }

    public function createVariations()
    {
        DB::table('variations')->truncate();
        File::deleteDirectory("storage/app/public/variations");
        File::makeDirectory("storage/app/public/variations");
        $repoIds = [1, 2, 3, 4, 5, 6];
        foreach (Product::get() as $pp) {
            $packs = Pack::where('id', '!=', 1)->inRandomOrder()->take(2)->pluck('id');

            foreach (Repository::whereIn('id', $repoIds)->get() as $repo) {
                $isPrivate = in_array($repo->id, [5, 6]);
                $weight = $this->faker->numberBetween(0, 50);
                $p = Variation::create([
                    'agency_level' => Agency::find($repo->agency_id)->level,//level 1 agency sales to level 2 agencies
                    'name' => $pp->name,
                    'product_id' => $pp->id,
                    'grade' => $this->faker->randomElement(Variable::GRADES),
                    'agency_id' => $repo->agency_id,
                    'repo_id' => $repo->id,
                    'pack_id' => $isPrivate ? 1 : $packs[0],
                    'unit' => $isPrivate ? 'kg' : 'qty',
                    'in_repo' => $this->faker->numberBetween(0, 50),
                    'in_shop' => $isPrivate ? $weight : $this->faker->numberBetween(0, 50),
                    'price' => $this->faker->randomElement([10000, 5000, 3000, 22000, 12000]),
                    'auction_price' => 0,
                    'in_auction' => false,
                    'category_id' => 1,
                    'weight' => $isPrivate ? 1 : $this->faker->randomElement([5.5, 10, 15, 20]),
                    'description' => $this->faker->realText($this->faker->numberBetween(256, 512)),
                ]);

                $this->makeGallery("variations", "products", $pp->id, $p->id);

                $p = Variation::create([
                    'agency_level' => Agency::find($repo->agency_id)->level,//level 1 agency sales to level 2 agencies
                    'name' => $pp->name,
                    'product_id' => $pp->id,
                    'grade' => $this->faker->randomElement(Variable::GRADES),
                    'agency_id' => $repo->agency_id,
                    'repo_id' => $repo->id,
                    'pack_id' => $packs[1],
                    'in_repo' => $this->faker->numberBetween(0, 50),
                    'in_shop' => $this->faker->numberBetween(0, 50),
                    'price' => $this->faker->randomElement([10000, 5000, 3000, 22000, 12000]),
                    'auction_price' => 0,
                    'category_id' => 1,
                    'in_auction' => false,
                    'weight' => $this->faker->randomElement([2.5, 10, 15, 20]),
                    'description' => $this->faker->realText($this->faker->numberBetween(256, 512)),
                ]);

                $this->makeGallery("variations", "products", $pp->id, $p->id);
            }
        }
    }

    private
    function createPayments($count = 30)
    {
        User::whereNotNull('id')->update(['wallet' => 0]);
        DB::table('payments')->truncate();
        DB::table('transactions')->truncate();
        for ($i = 0; $i < $count; $i++) {
            $ownerId = $this->faker->numberBetween(1, 2);
            $user = User::find($ownerId);
            $amount = $this->faker->randomElement([10000, 5000, 3000, 22000, 12000]);
            $orderId = "{$ownerId}-" . floor(microtime(true) * 1000);
            $data = Payment::create([
                'owner_id' => $ownerId,
                'order_id' => $orderId,
                'amount' => $amount,
                'is_success' => true,
                'transaction_id' => $this->faker->regexify("[A-Za-z0-9]{10}"),
                'title' => __('charge') . " $amount " . __('currency'),
                'type' => 'charge',
                'market' => $this->faker->randomElement(Variable::MARKETS),
                'gateway' => $this->faker->randomElement(Variable::GATEWAYS),
                'coupon' => null,
                'created_at' => Carbon::now()->subMinutes($this->faker->numberBetween(2000, 360000)),
            ]);

            $data = Transaction::create([
                'owner_id' => $data->owner_id,
                'amount' => $data->amount,
                'type' => $data->type,
                'title' => $data->title,
                'source_id' => $data->id
            ]);
            $user->wallet += $amount;

            if ($this->faker->randomElement([false, false, true,])) {
                $type = $this->faker->randomElement(['buy_article', 'buy_video', 'buy_podcast', 'buy_banner',]);
                $table = explode('_', $type)[1] . "s";
                $id = DB::table($table)->inRandomOrder()->first()?->id;
                $data = Transaction::create([
                    'owner_id' => $data->owner_id,
                    'amount' => $this->faker->randomElement([-1200, -4200, -3400, -2300, -11000]),
                    'type' => "{$type}_$id",
                    'title' => __($type) . " $id ",
                    'source_id' => null,
                    'created_at' => Carbon::now()->subMinutes($this->faker->numberBetween(2000, 360000)),

                ]);
            }
            $user->save();
        }
    }

    private
    function createNotifications($count = 30)
    {

        DB::table('notifications')->truncate();
        for ($i = 0; $i < $count; $i++) {
            $ownerId = $this->faker->numberBetween(1, 2);
            $element = $this->faker->randomElement(["article", "site", "business", "podcast", "banner", "video",]);
            $for = DB::table("{$element}" . ($element == 'business' ? 'es' : 's'))->where('owner_id', $ownerId)->inRandomOrder()->first()?->id;
            $description = $this->faker->realText($this->faker->numberBetween(128, 512));
            $type = $this->faker->randomElement(["pay", "ticket_answer", "{$element}_approve", "{$element}_reject"]);

            $data = Notification::create([
                'owner_id' => $ownerId,
                'type' => $type,
                'subject' => __($type),
                'description' => $description,
                'data_id' => !in_array($type, ['pay', 'ticket_answer']) ? $for : null,
                'created_at' => Carbon::now(),
            ]);
            $this->makeFile("articles", $data->id);

        }
    }

    private
    function createArticles($count = 30)
    {
        File::deleteDirectory("storage/app/public/articles");
        File::makeDirectory("storage/app/public/articles");
        DB::table('articles')->truncate();

        for ($i = 0; $i < $count; $i++) {

            $elements = $this->faker->randomElements(["text", "text", "podcast", "banner", "video", "text", "text", "podcast", "banner", "video", "text", "text", "podcast", "banner", "video",], $this->faker->numberBetween(3, 5));

            $content = [];
            $duration = 0;
            foreach ($elements as $element) {
                if ($element != 'text') {
                    $item = DB::table("{$element}s")->inRandomOrder()->first();
                    $content[] = ['id' => $item?->id, 'type' => $element, 'value' => $item?->name];
                    $duration += $item->duration ?? 0;
                } else {
                    $html = "<p><strong>تست متن</strong> " . $this->faker->realText($this->faker->numberBetween(60, 500)) . "</p><h2>" . $this->faker->realText($this->faker->numberBetween(10, 30)) . "</h2>";
                    $duration += Util::estimateReadingTime($html);
                    $content[] = ['id' => microtime(true), 'type' => $element, 'value' => $html];
                }
            }

            $title = $this->faker->realText($this->faker->numberBetween(60, 120));
            $status = $this->faker->randomElement(["active", "need_charge", "active", "review"]);
            $viewFee = $this->faker->numberBetween(0, 600);
            $data = Article::create([
                'title' => $title,
                'slug' => str_slug($title),
                'status' => $status,
                'view_fee' => $viewFee,
                'charge' => $status == 'need_charge' ? $viewFee - intval($viewFee / 3) : $viewFee + $this->faker->numberBetween(200, 500),
                'owner_id' => $this->faker->numberBetween(1, 2),
                'category_id' => $this->faker->randomElement(Category::pluck('id')),
                'author' => $this->faker->name,
                'duration' => $duration,
                'view' => $this->faker->numberBetween(0, 10),
                'lang' => 'fa',
                'content' => json_encode($content),
                'summary' => $this->faker->realText($this->faker->numberBetween(200, 1024)),
                'created_at' => Carbon::now(),
                'tags' => implode(",", $this->faker->randomElements(["مقاله", "متن", "تبلیغ", "مقاله", "مقاله", "تست",], $this->faker->numberBetween(0, 5))),
            ]);
            $this->makeFile("articles", $data->id);

        }
    }

    private
    function createBanners($count = 30)
    {
        File::deleteDirectory("storage/app/public/banners");
        File::makeDirectory("storage/app/public/banners");
        DB::table('banners')->truncate();

        for ($i = 0; $i < $count; $i++) {

            $name = $this->faker->company;
            $data = Banner::create([
                'name' => $name,
                'designer' => $this->faker->name,
                'slug' => str_slug($name),
                'status' => $this->faker->randomElement(["active", "active", "active", "review"]),
                'owner_id' => $this->faker->numberBetween(1, 2),
                'category_id' => $this->faker->randomElement(Category::pluck('id')),
                'view' => 0,
                'lang' => 'fa',
                'description' => $this->faker->realText($this->faker->numberBetween(200, 1024)),
                'created_at' => Carbon::now(),
                'tags' => implode(",", $this->faker->randomElements(["بنر", "بازدید", "صدا", "تست", "بنر", "بنر",], $this->faker->numberBetween(0, 5))),
            ]);
            $this->makeFile("banners", "$data->id");
            $this->makeFile("banners", "$data->id-file", null);
        }
    }

    private
    function createVideos($count = 30)
    {
        File::deleteDirectory("storage/app/public/videos");
        File::makeDirectory("storage/app/public/videos");
        DB::table('videos')->truncate();

        for ($i = 0; $i < $count; $i++) {

            $name = $this->faker->company;
            $data = Video::create([
                'name' => $name,
                'slug' => str_slug($name),
                'duration' => $this->faker->numberBetween(60, 250),
                'status' => $this->faker->randomElement(["active", "active", "active", "review"]),
                'owner_id' => $this->faker->numberBetween(1, 2),
                'category_id' => $this->faker->randomElement(Category::pluck('id')),
                'view' => 0,
                'lang' => 'fa',
                'description' => $this->faker->realText($this->faker->numberBetween(200, 1024)),
                'created_at' => Carbon::now(),
                'tags' => implode(",", $this->faker->randomElements(["ویدیو", "بازدید", "صدا", "تست", "تصویر", "ویدیو",], $this->faker->numberBetween(0, 5))),
            ]);
            $this->makeFile("videos", $data->id);
            $this->makeFile("videos", $data->id, '.mp4');
        }
    }

    private
    function createPodcasts($count = 30)
    {
        File::deleteDirectory("storage/app/public/podcasts");
        File::makeDirectory("storage/app/public/podcasts");
        DB::table('podcasts')->truncate();

        for ($i = 0; $i < $count; $i++) {

            $name = $this->faker->company;
            $data = Podcast::create([
                'name' => $name,
                'narrator' => $this->faker->name,
                'slug' => str_slug($name),
                'duration' => $this->faker->numberBetween(60, 250),
                'status' => $this->faker->randomElement(["active", "active", "active", "review"]),
                'owner_id' => $this->faker->numberBetween(1, 2),
                'category_id' => $this->faker->randomElement(Category::pluck('id')),
                'view' => 0,
                'lang' => 'fa',
                'description' => $this->faker->realText($this->faker->numberBetween(200, 1024)),
                'created_at' => Carbon::now(),
                'tags' => implode(",", $this->faker->randomElements(["پادکست", "بازدید", "صدا", "تست", "گوینده", "پادکست",], $this->faker->numberBetween(0, 5))),
            ]);
            $this->makeFile("podcasts", $data->id);
            $this->makeFile("podcasts", $data->id, '.mp3');
        }
    }

    private
    function makeGallery($type, $faker = null, $parent, $id)
    {
        $faker = $faker ?? $type;
        if (!File::exists("storage/app/public/$type/$id")) {
//            Storage::makeDirectory("public/$type", 766);
            File::makeDirectory(Storage::path("public/$type/$id"), $mode = 0755,);
        }
        $path = storage_path("app/public/faker/$faker/$parent.jpg");

        $file = new UploadedFile(
            $path,
            '1.' . File::extension($path),
            File::mimeType($path),
            null,
            true

        );
        copy($file->path(), (storage_path("app/public/$type/$id/thumb.jpg")   /*. $file->extension()*/));


    }

    private
    function createBusinesses($count = 30)
    {
        File::deleteDirectory("storage/app/public/businesses");
        File::makeDirectory("storage/app/public/businesses");

        DB::table('businesses')->truncate();
        $socials = [
            ['name' => 'تلگرام', 'value' => 't.me/develowper',],
            ['name' => 'ایتا', 'value' => 'eitaa.com/vartastudio',],
            ['name' => 'واتساپ', 'value' => 'wa.me/00989398793845',],
            ['name' => 'ایمیل', 'value' => 'moj2raj2@gmail.com',],
        ];
        for ($i = 0; $i < $count; $i++) {

            $name = $this->faker->company;
            $county = County::inRandomOrder()->first();

            $data = Business::create([
                'owner_id' => $this->faker->numberBetween(1, 2),
                'category_id' => $this->faker->randomElement(Category::pluck('id')),
                'province_id' => $county->province_id,
                'county_id' => $county->id,
                'name' => $name,
                'slug' => str_slug($name),
                'description' => $this->faker->realText($this->faker->numberBetween(200, 1024)),
                'phone' => $this->faker->numerify("09#########"),
                'tags' => implode(",", $this->faker->randomElements(["سایت", "بازدید", "تگ", "تست", "خدمات", "افزایش",], $this->faker->numberBetween(0, 5))),
                'status' => $this->faker->randomElement(["active", "active", "active", "review", "inactive"]),
                'lang' => 'fa',
                'view' => 0,
                'socials' => json_encode($this->faker->randomElements($socials, $this->faker->numberBetween(0, 4))),
                'created_at' => Carbon::now(),
            ]);
            $this->makeGallery("businesses", $data->id);
        }
    }

    private
    function createSites($count = 30)
    {
        File::deleteDirectory("storage/app/public/sites");
        File::makeDirectory("storage/app/public/sites");
        DB::table('sites')->truncate();

        for ($i = 0; $i < $count; $i++) {

            $name = $this->faker->company;
            $data = Site::create([
                'name' => $name,
                'slug' => str_slug($name),
                'link' => $this->faker->url(),

                'status' => $this->faker->randomElement(["view", "view", "view", "ready"]),
                'meta' => $this->faker->numberBetween(0, 5),
                'owner_id' => $this->faker->numberBetween(1, 2),
                'category_id' => $this->faker->randomElement(Category::pluck('id')),
                'view' => 0,
                'viewer' => 0,
                'charge' => $this->faker->numerify("####00"),
                'view_fee' => $this->faker->numerify("##0"),
                'lang' => 'fa',
                'description' => $this->faker->realText($this->faker->numberBetween(200, 1024)),
                'created_at' => Carbon::now(),
                'tags' => implode(",", $this->faker->randomElements(["سایت", "بازدید", "تگ", "تست", "خدمات", "افزایش",], $this->faker->numberBetween(0, 5))),
            ]);
            $this->makeFile("sites", $data->id);
        }
    }

    private
    function makeFile($type, $id, $extension = '.jpg', $random = true)
    {
        if ($random) {
            $allFiles = array_filter(Storage::allFiles("public/faker/$type"), fn($e) => !$extension || str_contains($e, $extension));

            $path = storage_path('app/' . $random ? $allFiles[array_rand($allFiles)] : $allFiles[$id - 1]);
        } else {
            $path = Storage::path("public/faker/$type/$id$extension");

        }
        $extension = $extension ?? "." . File::extension($path);
        //profile picture
        $file = new UploadedFile(
            $path,
            File::name($path) . ($extension),
            File::mimeType($path),
            null,
            true

        );

        if (!File::exists("storage/app/public/$type")) {
//            Storage::makeDirectory("public/$type", 766);
            File::makeDirectory(Storage::path("public/$type"), $mode = 0755,);
        }

        copy($file->path(), (storage_path("app/public/$type/") . "$id$extension" /*. $file->extension()*/));

    }


    function makeGallery2($type, $id)
    {
        $fakeFiles = Storage::allFiles("public/faker/$type");
        if (!File::exists("storage/app/public/$type/$id")) {
//            Storage::makeDirectory("public/$type", 766);
            File::makeDirectory(Storage::path("public/$type/$id"), $mode = 0755,);
        }

        $rand = [1, 2, 3, 4][array_rand([1, 2, 3, 4])];

        for ($i = 0; $i < $rand; $i++) {

            $path = storage_path('app/' . $fakeFiles[array_rand($fakeFiles)]);

            //profile picture
            $file = new UploadedFile(
                $path,
                File::name($path) . '.' . File::extension($path),
                File::mimeType($path),
                null,
                true

            );
            $name = 1;
            $allFiles = Storage::allFiles("public/businesses/$id");

            foreach ($allFiles as $path) {
                if (str_contains($path, "/$name.jpg")) {
                    $name++;
                }
            }
            copy($file->path(), (storage_path("app/public/$type/$id/$name.jpg")   /*. $file->extension()*/));
        }
    }


}
