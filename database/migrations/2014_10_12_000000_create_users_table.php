<?php

use App\Http\Helpers\Variable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
//            $table->string('username', 50)->nullable();
            $table->string('fullname', 100);
            $table->string('email', 100)->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone', 20)->nullable();
            $table->boolean('phone_verified')->default(false);
            $table->string('password', 200)->nullable();
            $table->unsignedSmallInteger('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities');

            $table->string('telegram_id', 50)->nullable();
            $table->string('bale_id', 50)->nullable();
            $table->string('soroush_id', 50)->nullable();
            $table->enum('role', \App\Http\Helpers\Variable::USER_ROLES)->default(\App\Http\Helpers\Variable::USER_ROLES[0]);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_block')->default(false);
            $table->enum('status', array_column(Variable::USER_STATUSES, 'name'))->default(array_column(Variable::USER_STATUSES, 'name')[0]);

            $table->boolean('wallet_active')->default(false);
            $table->integer('notifications')->unsigned()->default(0);
            $table->unsignedInteger('meta_wallet')->default(0);
//            $table->unsignedInteger('wallet')->default(0);
//            $table->string('card', 16)->default(null)->nullable();
            $table->string('ref_id', 10)->nullable();
            $table->string('push_id', 20)->nullable();
            $table->timestamp('expires_at')->nullable()->default(null);
            $table->json('settings')->nullable()->default(null);
            $table->json('addresses')->nullable();
            $table->string('access', 20)->nullable()->default(null);
            $table->rememberToken();
            $table->timestamps();
            $table->tinyInteger('step')->nullable();
        });

//        \Illuminate\Support\Facades\DB::table('users')->insert(\App\Http\Helpers\Variable::getUsers());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
