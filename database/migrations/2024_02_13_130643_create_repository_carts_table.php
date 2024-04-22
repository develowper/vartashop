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
        Schema::create('repository_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('to_repo_id')->nullable();
            $table->foreign('to_repo_id')->references('id')->on('repositories')->onDelete('no action');
            $table->unsignedBigInteger('to_agency_id')->nullable();
            $table->foreign('to_agency_id')->references('id')->on('agencies')->onDelete('no action');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('no action');

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
        Schema::dropIfExists('repository_carts');
    }
};
