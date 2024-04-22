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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('repo_id')->nullable();
            $table->foreign('repo_id')->references('id')->on('repositories')->onDelete('no action');
            $table->unsignedBigInteger('agency_id')->nullable();
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('no action');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('no action');

            $table->foreignId('user_id')->nullable()->index();
            $table->enum('status', array_column(Variable::ORDER_STATUSES, 'name'))->index();
            $table->unsignedSmallInteger('province_id')->nullable();
            $table->foreign('province_id')->references('id')->on('cities')->onDelete('no action');
            $table->unsignedSmallInteger('county_id')->nullable();
            $table->foreign('county_id')->references('id')->on('cities')->onDelete('no action');
            $table->unsignedSmallInteger('district_id')->nullable();
            $table->foreign('district_id')->references('id')->on('cities')->onDelete('no action');
            $table->string('receiver_fullname', 200)->nullable();
            $table->string('receiver_phone', 20)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('address', 2048)->nullable();
            $table->string('location', 50)->nullable();
            $table->string('coupon', 10)->nullable();
            $table->unsignedBigInteger('total_discount')->default(0);
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedBigInteger('total_price')->default(0);
            $table->unsignedBigInteger('total_items_price')->default(0);
            $table->unsignedBigInteger('total_shipping_price')->default(0);
            $table->bigInteger('change_price')->default(0);
            $table->timestamps();
            $table->timestamp('done_at')->nullable();//deliver|cancel
            $table->unsignedBigInteger('shipping_id')->nullable();
            $table->foreign('shipping_id')->references('id')->on('shippings')->onDelete('no action');
            $table->unsignedBigInteger('shipping_method_id')->nullable();
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods')->onDelete('no action');
            $table->date('delivery_date')->nullable();//deliver|cancel
            $table->string('delivery_timestamp', 15)->nullable();//deliver|cancel
            $table->enum('pay_type', array_column(Variable::getPaymentMethods(), 'key'))->nullable();//deliver|cancel
            $table->timestamp('payed_at')->nullable();
            $table->unsignedInteger('distance')->nullable();
            $table->unsignedInteger('tax_price')->default(0);
            $table->unsignedDecimal('total_weight', 8, 3)->default(0); //kg

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
