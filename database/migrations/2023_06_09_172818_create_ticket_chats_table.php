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
        Schema::create('ticket_chats', function (Blueprint $table) {
            $table->id();

            $table->morphs('from');
//            $table->bigInteger('from_id')->unsigned();
            $table->bigInteger('ticket_id')->unsigned();
            $table->boolean('from_seen')->default(false);
            $table->boolean('to_seen')->default(false);

            $table->text('message', 2048);
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('no action');
            $table->foreign('from_id')->references('id')->on('users')->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_chats');
    }
};
