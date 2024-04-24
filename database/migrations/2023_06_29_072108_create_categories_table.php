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
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('level')->default(1);
            $table->string('name', 100);
            $table->enum('status', array_column(Variable::STATUSES, 'name'))->default('active');
            $table->unsignedInteger('parent_id')->nullable();
            $table->json('children')->nullable();
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('no action');
            $table->timestamps();
        });

        DB::table('categories')->insert(Variable::CATEGORIES);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
