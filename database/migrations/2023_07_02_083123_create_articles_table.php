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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('no action');
            $table->string('author', 200)->nullable();
            $table->string('title', 1024);
//            $table->string('summary', 2048)->nullable();
            $table->string('slug', 2048)->nullable();
            $table->unsignedInteger('view')->default(0);
            $table->unsignedInteger('duration')->default(0);
            $table->string('tags', 200)->nullable();
            $table->json('content')->nullable();
            $table->enum('status', array_column(Variable::STATUSES, 'name'))->nullable();

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
        Schema::dropIfExists('articles');
    }
};
