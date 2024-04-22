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
        Schema::create('partnerships', function (Blueprint $table) {
            $table->id();
            $table->string('fullname', 100);
            $table->string('meterage', 10)->nullable();
            $table->unsignedSmallInteger('province_id')->nullable()->index();
            $table->foreign('province_id')->references('id')->on('cities')->onDelete('no action');
            $table->unsignedSmallInteger('county_id')->nullable();
            $table->foreign('county_id')->references('id')->on('cities')->onDelete('no action');
            $table->string('address', 2048)->nullable();
            $table->string('description', 2048)->nullable();
            $table->json('products')->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('type', array_column(Variable::PARTNERSHIP_TYPES, 'name'))->nullable();
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
        Schema::dropIfExists('partnerships');
    }
};
