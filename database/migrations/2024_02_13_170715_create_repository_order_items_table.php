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
        Schema::create('repository_order_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->nullable();
            $table->string('title', 1024)->nullable();

            $table->unsignedBigInteger('shipping_id')->nullable();
            $table->foreign('shipping_id')->references('id')->on('order_shipping')->onDelete('no action');
            $table->unsignedBigInteger('shipping_method_id')->nullable();
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods')->onDelete('no action');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('repository_orders')->onDelete('no action');
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('no action');
            $table->unsignedBigInteger('repo_id')->nullable();
            $table->foreign('repo_id')->references('id')->on('repositories')->onDelete('no action');
//            $table->unsignedInteger('qty')->nullable();
            $table->unsignedDecimal('qty', 8, 3)->default(0);//weight|count

            $table->unsignedBigInteger('total_price');
            $table->unsignedBigInteger('discount_price');
            $table->unsignedInteger('pack_id')->nullable();
            $table->foreign('pack_id')->references('id')->on('packs')->onDelete('no action');
            $table->unsignedDecimal('total_weight', 8, 3)->default(0); //kg
            $table->enum('grade', \App\Http\Helpers\Variable::GRADES)->nullable();

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
        Schema::dropIfExists('repository_order_items');
    }
};
