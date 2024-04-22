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
        Schema::create('admin_financials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('no action');
            $table->unsignedInteger('wallet')->default(0);
            $table->string('card', 20)->nullable();
            $table->string('sheba', 30)->nullable();
            $table->timestamps();
//            $table->unsignedBigInteger('agency_id')->nullable();
//            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_financials');
    }
};
