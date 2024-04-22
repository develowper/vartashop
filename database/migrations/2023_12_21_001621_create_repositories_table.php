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
        Schema::create('repositories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->boolean('is_shop')->default(false);
            $table->unsignedBigInteger('agency_id')->nullable();
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('no action');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('no action');
            $table->unsignedSmallInteger('province_id')->nullable()->index();
            $table->foreign('province_id')->references('id')->on('cities')->onDelete('no action');
            $table->unsignedSmallInteger('county_id')->nullable();
            $table->foreign('county_id')->references('id')->on('cities')->onDelete('no action');
            $table->unsignedSmallInteger('district_id')->nullable();
            $table->foreign('district_id')->references('id')->on('cities')->onDelete('no action');

            $table->string('address', 2048)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('location', 50)->nullable();
            $table->boolean('allow_visit')->default(true);
            $table->string('postal_code', 20)->nullable();
            $table->json('cities')->nullable();

            $table->enum('status', array_column(Variable::STATUSES, 'name'))->default('inactive');
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
        Schema::dropIfExists('repositories');
    }
};
