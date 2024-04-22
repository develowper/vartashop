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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->nullable();
            $table->unsignedBigInteger('shipping_id')->nullable();
            $table->foreign('shipping_id')->references('id')->on('shipping_methods')->onDelete('no action');
            $table->unsignedBigInteger('cart_id')->nullable();
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('no action');
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('no action');
            $table->unsignedBigInteger('repo_id')->nullable();
            $table->foreign('repo_id')->references('id')->on('repositories')->onDelete('no action');
//            $table->unsignedInteger('qty')->nullable();
            $table->unsignedDecimal('qty', 8, 3)->default(0);//weight|count

            $table->boolean('visit_checked')->default(false);

            $table->timestamps();
            $table->date('delivery_date')->nullable();//deliver|cancel
            $table->string('delivery_timestamp', 15)->nullable();//deliver|cancel

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
};
