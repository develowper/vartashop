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
        Schema::create('agency_financials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agency_id')->nullable();
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('no action');

            $table->bigInteger('wallet')->default(0);
            $table->bigInteger('parent_debit')->default(0);
//            $table->bigInteger('payment_balance')->default(0);
            $table->string('card', 20)->nullable();
            $table->string('sheba', 30)->nullable();
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
        Schema::dropIfExists('agency_financials');
    }
};
