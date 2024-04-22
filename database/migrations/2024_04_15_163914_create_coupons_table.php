<?php

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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->tinyInteger('discount_percent')->unsigned()->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->integer('used_times')->unsigned()->default(0);
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('min_price')->nullable();
            $table->unsignedInteger('limit_discount')->nullable();
            $table->unsignedInteger('limit_use')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable(); //null is for public

            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
};
