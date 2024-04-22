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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->enum('type', Variable::TRANSACTION_TYPES)->index()->nullable();
            $table->enum('for_type', array_keys(Variable::TRANSACTION_MODELS))->nullable();
            $table->unsignedBigInteger('for_id')->nullable()->index();
            $table->enum('from_type', array_keys(Variable::PAYER_TYPES))->nullable();
            $table->unsignedBigInteger('from_id')->nullable();
            $table->enum('to_type', array_keys(Variable::PAYER_TYPES))->nullable();
            $table->unsignedBigInteger('to_id')->nullable();
            $table->string('info', 2048)->nullable();
            $table->unsignedInteger('amount')->default(0);
            $table->string('pay_id', 50)->nullable()->index();
            $table->string('coupon', 10)->nullable();
            $table->string('pay_gate', 10)->nullable();
            $table->timestamps();
            $table->timestamp('payed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
