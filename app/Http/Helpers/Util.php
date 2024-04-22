<?php

namespace App\Http\Helpers;


use App\Models\City;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;

class Util
{
    public static function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        if (in_array(null, [$lat1, $lon1, $lat2, $lon2])) {
            return null;
        } elseif (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return intval($miles * 1.609344);
            } else if ($unit == "N") {
                return intval($miles * 0.8684);
            } else {
                return intval($miles);
            }
        }
    }

    public static function createFile(array|\Illuminate\Http\UploadedFile|null $file, string $type, $name)
    {
        if (str_contains($type, '/')) {
            $path0 = explode('/', $type)[0];
            if (!Storage::exists("public/$path0")) {
                File::makeDirectory(Storage::path("public/$path0"), $mode = 0755,);
            }
        }
        if (!Storage::exists("public/$type")) {
            File::makeDirectory(Storage::path("public/$type"), $mode = 0755,);
        }
        $file->move(storage_path("app/public/$type"), "$name." . $file->extension());
    }

    static function createImage($img, $type, $name = null, $folder = null, $maxSize = null)
    {

        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);

        if (!Storage::exists("public/$type")) {
            File::makeDirectory(Storage::path("public/$type"), $mode = 0755,);
        }
        $source = imagecreatefromstring($image_base64);
        if ($folder) { //is gallery
            if (!Storage::exists("public/$type/$folder"))
                File::makeDirectory(Storage::path("public/$type/$folder"), $mode = 0755,);
            $allFiles = Storage::allFiles("public/$type/$folder");
            if (!$name) {
                $name = 1;
                foreach ($allFiles as $path) {
                    if (str_contains($path, "/$name.jpg")) {
                        $name++;
                    }
                }
            }
            $type = "$type/$folder";
        }

//        imagetruecolortopalette($source, false, 16);

        $imageSave = imagejpeg($source, storage_path("app/public/$type/$name.jpg"), 80);

        if ($maxSize) {
            $maxSize = $maxSize * 1024;
            $path = storage_path("app/public/$type/$name.jpg");
            $img = Image::make($path);
            while ($img->filesize() >= $maxSize) {
                $width = $img->width();
                $img->resize($width - round($width / 4), null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path);
                clearstatcache();

            }
        }
        imagedestroy($source);
        return $imageSave;
        return "/storage/$type/$name.jpg";
//        file_put_contents(storage_path("app/public/$type_id/$image->id.jpg"), $image_base64);


    }


    static function createScreenshot($url, $type, $name)
    {
        return true;
        if (!Storage::exists("public/$type")) {
            File::makeDirectory(Storage::path("public/$type"), $mode = 0755,);
        }
        try {
            return Browsershot::url($url)
                ->setOption('landscape', true)
                //            ->windowSize(3840, 2160)
                ->waitUntilNetworkIdle()
                ->save(storage_path("app/public/$type/$name.jpg"));
        } catch (CouldNotTakeBrowsershot $e) {
            return false;
        }


    }

    static function validate_base64($base64data, array $allowedMime)
    {


        // strip out data uri scheme information (see RFC 2397)
        if (strpos($base64data, ';base64') !== false) {
            list(, $base64data) = explode(';', $base64data);
            list(, $base64data) = explode(',', $base64data);
        }

        // strict mode filters for non-base64 alphabet characters
        if (base64_decode($base64data, true) === false) {
            return false;
        }

        // decoding and then reeconding should not change the data
        if (base64_encode(base64_decode($base64data)) !== $base64data) {
            return false;
        }

        $binaryData = base64_decode($base64data);

        // temporarily store the decoded data on the filesystem to be able to pass it to the fileAdder

        $tmpFile = tempnam(sys_get_temp_dir(), 'medialibrary');
//    $tmpFile = tmpfile();
//    $path = stream_get_meta_data($tmpFile)['uri'];
        file_put_contents($tmpFile, $binaryData);

        // guard Against Invalid MimeType
        $allowedMime = array_flatten($allowedMime);

        // no allowedMimeTypes, then any type would be ok
        if (empty($allowedMime)) {
            return true;
        }

        // Check the MimeTypes
        $validation = Validator::make(
            ['file' => new  File($tmpFile)],
            ['file' => 'mimes:' . implode(',', $allowedMime)]
        );

        $res = !$validation->fails();

        unlink($tmpFile);

        return $res;
    }

    static function e2f($str)
    {
        $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $per = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return str_replace($eng, $per, $str);
    }

    static function f2e($str)
    {
        $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $per = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return str_replace($per, $eng, $str);
    }

    static function array_flatten($array, $depth = INF): array
    {
        return Arr::flatten($array, $depth);
    }

    static function randomString($length = 5, $original)
    {
        return substr(str_shuffle($original), 0, $length);
    }

    static function wipeCaches()
    {
        $exitCode = Artisan::call('cache:clear');
        echo 'cache:clear ' . "$exitCode | " . PHP_EOL;
        $exitCode = Artisan::call('route:clear');
        echo 'route:clear ' . "$exitCode | " . PHP_EOL;
        $exitCode = Artisan::call('view:clear');
        echo 'view:clear ' . "$exitCode | " . PHP_EOL;
//    $exitCode = Artisan::call('view:cache');
//    echo 'view:cache' . "$exitCode | "  . PHP_EOL;
//    $exitCode = Artisan::call('route:cache');
//    echo 'route:cache' . "$exitCode | "  . PHP_EOL;
        $exitCode = Artisan::call('config:clear');
        echo 'config:clear ' . "$exitCode | " . PHP_EOL;
//    $exitCode = Artisan::call('config:cache');
//    echo 'config:cache' . "$exitCode | "  . PHP_EOL;
        $exitCode = Artisan::call('optimize');
        echo 'optimize ' . "$exitCode | " . PHP_EOL;

    }

    public
    static function generateRandomNumber($length = 8)
    {
        $random = "";
        srand((double)microtime() * 1000000);
        $data = "123456123456789071234567890890";
        for ($i = 0; $i < $length; $i++) {
            $random .= substr($data, (rand() % (strlen($data))), 1);
        }
        return $random;
    }

    static function estimateReadingTime($content = '', $wpm = 250)
    {
        $clean_content = strip_tags($content);
        $word_count = str_word_count($clean_content, 0, 'ابپتثجچ‌حخدذرز‌ژس‌شصضطظعغفقکگلمنوهیءآاًهٔه');
        $time = ceil($word_count / $wpm);
        return $time;
    }

    public
    static function createCityTable()
    {
        self::createCityTableFromDivar();
//        City::where('level', 3)->delete();
        DB::statement('ALTER TABLE cities MODIFY COLUMN id SMALLINT UNSIGNED  auto_increment');

    }

    public
    static function createCityTableFromDivar()
    {

        ini_set('max_execution_time', '0'); // for infinite time of execution
        if (DB::connection()->getDriverName() == 'mysql')
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('cities');

        Schema::create('cities', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->index()->primary();
            $table->unsignedSmallInteger('parent_id')->default(0);
            $table->string('name', 50)->index();
            $table->string('latitude', 30)->nullable();
            $table->string('longitude', 30)->nullable();
            $table->string('slug', 100)->index();
            $table->enum('level', [1, 2, 3])->default(1);
            $table->json('tags')->default(json_encode([]));
            $table->json('bbox')->default(json_encode([]));
            $table->boolean('has_child')->default(false);
            $table->unsignedSmallInteger('radius')->default(0);
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('cities');
        });
        self::insertFromSql("");
        foreach (City::get() as $item) {
            $item->has_child = City::where('parent_id', $item->id)->exists();
            $item->save();
        }

        return;
        DB::table('cities')->insert([
            ['id' => 891, 'name' => 'آذربایجان شرقی', 'slug' => str_slug('آذربایجان شرقی'), /* 'amar_code' => '3',*/ 'latitude' => '37.9160365', 'longitude' => '46.6781589'],
            ['id' => 881, 'name' => 'آذربایجان غربی', 'slug' => str_slug('آذربایجان غربی'), /* 'amar_code' => '4',*/ 'latitude' => '37.7416484', 'longitude' => '45.0207638'],
            ['id' => 906, 'name' => 'اردبیل', 'slug' => str_slug('اردبیل'), /* 'amar_code' => '24',*/ 'latitude' => '38.4583983', 'longitude' => '47.9313001'],
            ['id' => 890, 'name' => 'اصفهان', 'slug' => str_slug('اصفهان'), /* 'amar_code' => '10',*/ 'latitude' => '33.1881386', 'longitude' => '52.4981635'],
            ['id' => 895, 'name' => 'البرز', 'slug' => str_slug('البرز'), /* 'amar_code' => '30',*/ 'latitude' => '35.9413239', 'longitude' => '50.7884216'],
            ['id' => 888, 'name' => 'ایلام', 'slug' => str_slug('ایلام'), /* 'amar_code' => '16',*/ 'latitude' => '33.1545696', 'longitude' => '46.7576343'],
            ['id' => 900, 'name' => 'بوشهر', 'slug' => str_slug('بوشهر'), /* 'amar_code' => '18',*/ 'latitude' => '28.8936645', 'longitude' => '51.3204877'],
            ['id' => 904, 'name' => 'تهران', 'slug' => str_slug('تهران'), /* 'amar_code' => '23',*/ 'latitude' => '35.4834424', 'longitude' => '51.4104612'],
            ['id' => 903, 'name' => 'چهارمحال و بختیاری', 'slug' => str_slug('چهارمحال و بختیاری'), /* 'amar_code' => '14',*/ 'latitude' => '32.0163307', 'longitude' => '50.685709'],
            ['id' => 876, 'name' => 'خراسان جنوبی', 'slug' => str_slug('خراسان جنوبی'), /* 'amar_code' => '29',*/ 'latitude' => '33.129577', 'longitude' => '58.1065069'],
            ['id' => 880, 'name' => 'خراسان رضوی', 'slug' => str_slug('خراسان رضوی'), /* 'amar_code' => '9',*/ 'latitude' => '35.4795009', 'longitude' => '59.0237291'],
            ['id' => 896, 'name' => 'خراسان شمالی', 'slug' => str_slug('خراسان شمالی'), /* 'amar_code' => '28',*/ 'latitude' => '37.5378855', 'longitude' => '56.9526137'],
            ['id' => 902, 'name' => 'خوزستان', 'slug' => str_slug('خوزستان'), /* 'amar_code' => '6',*/ 'latitude' => '31.5563051', 'longitude' => '49.0058885'],
            ['id' => 879, 'name' => 'زنجان', 'slug' => str_slug('زنجان'), /* 'amar_code' => '19',*/ 'latitude' => '36.515854', 'longitude' => '48.4777616'],
            ['id' => 882, 'name' => 'سمنان', 'slug' => str_slug('سمنان'), /* 'amar_code' => '20',*/ 'latitude' => '35.3843145', 'longitude' => '54.6209302'],
            ['id' => 883, 'name' => 'سیستان و بلوچستان', 'slug' => str_slug('سیستان و بلوچستان'), /* 'amar_code' => '11',*/ 'latitude' => '28.1292481', 'longitude' => '60.8236848'],
            ['id' => 898, 'name' => 'فارس', 'slug' => str_slug('فارس'), /* 'amar_code' => '7',*/ 'latitude' => '29.299051', 'longitude' => '53.218456'],
            ['id' => 878, 'name' => 'قزوین', 'slug' => str_slug('قزوین'), /* 'amar_code' => '26',*/ 'latitude' => '36.0156291', 'longitude' => '49.8398161'],
            ['id' => 892, 'name' => 'قم', 'slug' => str_slug('قم'), /* 'amar_code' => '25',*/ 'latitude' => '34.7191915', 'longitude' => '51.0122844'],
            ['id' => 885, 'name' => 'کردستان', 'slug' => str_slug('کردستان'), /* 'amar_code' => '12',*/ 'latitude' => '35.672803', 'longitude' => '47.0124376'],
            ['id' => 877, 'name' => 'کرمان', 'slug' => str_slug('کرمان'), /* 'amar_code' => '8',*/ 'latitude' => '29.571858', 'longitude' => '57.301047'],
            ['id' => 905, 'name' => 'کرمانشاه', 'slug' => str_slug('کرمانشاه'), /* 'amar_code' => '5',*/ 'latitude' => '34.3789744', 'longitude' => '46.7010122'],
            ['id' => 887, 'name' => 'کهگیلویه و بویراحمد', 'slug' => str_slug('کهگیلویه و بویراحمد'), /* 'amar_code' => '17',*/ 'latitude' => '30.8143476', 'longitude' => '50.8661454'],
            ['id' => 897, 'name' => 'گلستان', 'slug' => str_slug('گلستان'), /* 'amar_code' => '27',*/ 'latitude' => '37.1984436', 'longitude' => '55.070672'],
            ['id' => 889, 'name' => 'گیلان', 'slug' => str_slug('گیلان'), /* 'amar_code' => '1',*/ 'latitude' => '37.1515627', 'longitude' => '49.6389743'],
            ['id' => 884, 'name' => 'لرستان', 'slug' => str_slug('لرستان'), /* 'amar_code' => '15',*/ 'latitude' => '33.5368206', 'longitude' => '48.2443945'],
            ['id' => 893, 'name' => 'مازندران', 'slug' => str_slug('مازندران'), /* 'amar_code' => '2',*/ 'latitude' => '36.3159159', 'longitude' => '51.8968597'],
            ['id' => 901, 'name' => 'مرکزی', 'slug' => str_slug('مرکزی'), /* 'amar_code' => '0',*/ 'latitude' => '34.5302705', 'longitude' => '49.7864561'],
            ['id' => 894, 'name' => 'هرمزگان', 'slug' => str_slug('هرمزگان'), /* 'amar_code' => '22',*/ 'latitude' => '27.7198095', 'longitude' => '56.335807'],
            ['id' => 899, 'name' => 'همدان', 'slug' => str_slug('همدان'), /* 'amar_code' => '13',*/ 'latitude' => '34.9726302', 'longitude' => '48.6563818'],
            ['id' => 886, 'name' => 'یزد', 'slug' => str_slug('یزد'), /* 'amar_code' => '21',*/ 'latitude' => '32.0406164', 'longitude' => '54.6657189'],
        ]);


        $data = Http::get('https://api.divar.ir/v8/places/cities');
        $data = $data->object();
        $cities = $data->cities;
        $cities = collect($cities)->sortBy('id')->all();
        foreach ($cities as $city) {

            DB::table('cities')->insert([
                'id' => $city->id,
                'name' => $city->name,
                'level' => 2 /*$city->level*/,
                'slug' => $city->slug,
                'radius' => $city->radius,
                'parent_id' => $city->parent,
                'tags' => json_encode($city->tags),
                'bbox' => json_encode($city->bbox),
                'latitude' => $city->centroid->latitude,
                'longitude' => $city->centroid->longitude,
            ]);
        }
        foreach ($cities /*DB::table('cities')->get()*/ as $city) {
            $data = Http::get("https://api.divar.ir/v8/places/cities/$city->id/districts");
            $data = $data->object();
            $districts = $data->districts;
            foreach ($districts as $district) {

                DB::table('cities')->insert([
                    'id' => $district->id,
                    'name' => $district->name,
                    'level' => 3 /* $district->level*/,
                    'slug' => $district->slug,
                    'radius' => $district->radius,
                    'parent_id' => $city->id  /*$district->parent*/,
                    'tags' => json_encode($district->tags),
                    'bbox' => json_encode($district->bbox),
                    'latitude' => $district->centroid->latitude,
                    'longitude' => $district->centroid->longitude,
                ]);
            }
        }
        $cities = DB::table('cities')->orderBy('id')->get();
        DB::table('cities')->truncate();
        foreach ($cities as $city) {
            DB::table('cities')->insert([
                'id' => $city->id,
                'name' => $city->name,
                'level' => $city->level,
                'slug' => $city->slug,
                'radius' => $city->radius,
                'parent_id' => $city->parent_id,
                'tags' => $city->tags,
                'bbox' => $city->bbox,
                'latitude' => $city->latitude,
                'longitude' => $city->longitude,
            ]);
        }
        return 'done';

    }


}
