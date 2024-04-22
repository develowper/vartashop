<?php

namespace App\Providers;

use App\Http\Helpers\Variable;
use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
//            Variable::MODELS[User::class] => User::class,
//            Variable::MODELS[Business::class] => Business::class,

        ]);
        Schema::defaultStringLength(191);

        Validator::extend('base64_image_size', function ($attribute, $value, $parameters, $validator) {
            // Decode the image
            $decodedImage = base64_decode($value);
            // Get image size in kilobytes
            $imageSize = strlen($decodedImage) / 1024;
            // Check if image is below max size
            return $imageSize <= $parameters[0];

        });

        Validator::extend('base64_image_mime', function ($attribute, $value, $parameters, $validator) {
            $explode = explode(',', $value);
            $allow = $parameters;
            $format = str_replace(
                [
                    'data:image/',
                    ';',
                    'base64',
                ],
                [
                    '', '', '',
                ],
                $explode[0]
            );
            // check file format
            if (!in_array($format, $allow)) {
                return false;
            }
            // check base64 format
            if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                return false;
            }

            return true;

        });
    }
}
