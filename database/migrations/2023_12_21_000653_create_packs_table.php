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
        Schema::create('packs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 200);
            $table->unsignedInteger('weight')->default(0); //gram
            $table->unsignedInteger('height')->default(0); //cm
            $table->unsignedInteger('width')->default(0); //cm
            $table->unsignedInteger('length')->default(0); //cm
            $table->unsignedInteger('price')->default(0);
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
        Schema::dropIfExists('packs');
    }
};
