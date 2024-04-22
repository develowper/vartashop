<?php

use App\Helpers\Helper;
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
//    'pay_type', 'pay_for', 'pay_result',
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->smallInteger('app_version')->unsigned()->nullable();
//            $table->bigInteger('inviter_user_id')->unsigned()->nullable();
            $table->string('order_id', 50)->nullable();
            $table->string('pay_for', 50)->nullable();
            $table->enum('pay_market', ['bazaar', 'myket', 'playstore', 'bank'])->nullable();
//            $table->enum('pay_for', array_map(function ($e) {
//                return $e['key'];
//            }, Helper::$products))->nullable();
//            $table->string('coupon', 10)->index()->nullable();
            $table->string('info', 2048)->nullable();
            $table->integer('amount')->default(0);
            $table->boolean('is_success')->default(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
//            $table->foreign('inviter_user_id')->references('id')->on('users')->onDelete('no action');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
