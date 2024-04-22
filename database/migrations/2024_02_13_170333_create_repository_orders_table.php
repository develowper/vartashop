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
        Schema::create('repository_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_repo_id')->nullable();
            $table->foreign('from_repo_id')->references('id')->on('repositories')->onDelete('no action');
            $table->unsignedBigInteger('from_agency_id')->nullable();
            $table->foreign('from_agency_id')->references('id')->on('agencies')->onDelete('no action');
            $table->unsignedBigInteger('to_repo_id')->nullable();
            $table->foreign('to_repo_id')->references('id')->on('repositories')->onDelete('no action');
            $table->unsignedBigInteger('to_agency_id')->nullable();
            $table->foreign('to_agency_id')->references('id')->on('agencies')->onDelete('no action');

            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('no action');

            $table->unsignedBigInteger('from_admin_id')->nullable();
            $table->foreign('from_admin_id')->references('id')->on('admins')->onDelete('no action');

            $table->unsignedBigInteger('to_admin_id')->nullable();
            $table->foreign('to_admin_id')->references('id')->on('admins')->onDelete('no action');


            $table->unsignedSmallInteger('from_province_id')->nullable();
            $table->foreign('from_province_id')->references('id')->on('cities')->onDelete('no action');
            $table->unsignedSmallInteger('from_county_id')->nullable();
            $table->foreign('from_county_id')->references('id')->on('cities')->onDelete('no action');
            $table->unsignedSmallInteger('from_district_id')->nullable();
            $table->foreign('from_district_id')->references('id')->on('cities')->onDelete('no action');
            $table->string('from_postal_code', 20)->nullable();
            $table->string('from_location', 50)->nullable();
            $table->string('from_address', 2048)->nullable();

            $table->unsignedSmallInteger('to_province_id')->nullable();
            $table->foreign('to_province_id')->references('id')->on('cities')->onDelete('no action');
            $table->unsignedSmallInteger('to_county_id')->nullable();
            $table->foreign('to_county_id')->references('id')->on('cities')->onDelete('no action');
            $table->unsignedSmallInteger('to_district_id')->nullable();
            $table->foreign('to_district_id')->references('id')->on('cities')->onDelete('no action');
            $table->string('to_postal_code', 20)->nullable();
            $table->string('to_location', 50)->nullable();
            $table->string('to_address', 2048)->nullable();

            $table->string('to_fullname', 200)->nullable();
            $table->string('to_phone', 20)->nullable();

            $table->string('from_fullname', 200)->nullable();
            $table->string('from_phone', 20)->nullable();

            $table->enum('status', array_column(Variable::ORDER_STATUSES, 'name'))->index();
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedBigInteger('total_discount')->default(0);
            $table->unsignedBigInteger('total_price')->default(0);
            $table->unsignedBigInteger('total_items_price')->default(0);
            $table->unsignedBigInteger('total_shipping_price')->default(0);
            $table->timestamps();
            $table->timestamp('done_at')->nullable();//deliver|cancel
            $table->unsignedBigInteger('shipping_id')->nullable();
            $table->foreign('shipping_id')->references('id')->on('order_shipping')->onDelete('no action');
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
        Schema::dropIfExists('repository_orders');
    }
};
