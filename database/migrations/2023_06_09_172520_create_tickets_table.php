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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agency_id')->nullable();
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('no action');


            $table->string('subject', 100);
            $table->enum('status', array_column(Variable::TICKET_STATUSES, 'name'));
            $table->unsignedBigInteger('from_id')->index();
            $table->enum('from_type', array_keys(Variable::PAYER_TYPES))->nullable()->index();
            $table->unsignedBigInteger('to_id')->index();
            $table->enum('to_type', array_keys(Variable::PAYER_TYPES))->nullable()->index();

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
        Schema::dropIfExists('tickets');
    }
};
