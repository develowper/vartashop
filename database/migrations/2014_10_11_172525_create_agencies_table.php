<?php

use App\Http\Helpers\Variable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('phone', 20)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->json('access')->nullable();
            $table->enum('level', array_column(Variable::AGENCY_TYPES, 'level'))->default(array_column(Variable::AGENCY_TYPES, 'level')[count(Variable::AGENCY_TYPES) - 1]);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('agencies')->onDelete('no action');
            //            $table->unsignedBigInteger('owner_id')->nullable();
//            $table->foreign('owner_id')->references('id')->on('admins')->onDelete('no action');
            $table->unsignedSmallInteger('province_id')->nullable()->index();
            $table->foreign('province_id')->references('id')->on('cities')->onDelete('no action');
            $table->unsignedSmallInteger('county_id')->nullable();
            $table->foreign('county_id')->references('id')->on('cities')->onDelete('no action');
            $table->unsignedSmallInteger('district_id')->nullable();
            $table->foreign('district_id')->references('id')->on('cities')->onDelete('no action');

            $table->string('address', 2048)->nullable();
            $table->string('location', 50)->nullable();
            $table->enum('status', array_column(Variable::STATUSES, 'name'))->default('inactive');
            $table->bigInteger('wallet')->default(0);

            $table->timestamps();
            $table->unsignedDecimal('order_profit_percent', 4, 2)->nullable(); //weight|count

        });
        DB::table('agencies')->insert(Variable::getAgencies());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agencies');
    }
};
