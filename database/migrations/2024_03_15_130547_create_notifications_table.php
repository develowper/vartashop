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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('subject', 100);
            $table->unsignedBigInteger('data_id')->nullable();
            $table->enum('type', Variable::NOTIFICATION_TYPES)->nullable();
            $table->text('description')->nullable();
            $table->string('link', 512)->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->enum('owner_type', array_keys(Variable::PAYER_TYPES))->nullable();
            $table->timestamp('created_at')->useCurrent();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
