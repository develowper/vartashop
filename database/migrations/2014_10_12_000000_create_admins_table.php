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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
//            $table->string('username', 50)->nullable();
            $table->unsignedBigInteger('agency_id')->nullable();
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('no action');
            $table->enum('agency_level', array_column(Variable::AGENCY_TYPES, 'level'))->nullable();

            $table->string('fullname', 100);
            $table->string('email', 100)->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone', 20)->nullable()->index();
            $table->boolean('phone_verified')->default(false);
            $table->string('password', 200)->nullable();
            $table->json('access')->nullable()->default(null);
            $table->enum('role', Variable::ADMIN_ROLES)->default(Variable::ADMIN_ROLES[0]);
            $table->enum('status', array_column(Variable::USER_STATUSES, 'name'))->default(array_column(Variable::USER_STATUSES, 'name')[0]);
            $table->integer('notifications')->unsigned()->default(0);
            $table->unsignedInteger('wallet')->default(0);
//            $table->string('card', 16)->default(null)->nullable();
//            $table->string('sheba', 24)->default(null)->nullable();
            $table->string('national_code', 20)->nullable();
            $table->string('push_id', 20)->nullable();
            $table->timestamp('expires_at')->nullable()->default(null);
            $table->json('settings')->nullable()->default(null);
            $table->string('telegram_id', 50)->nullable()->index();
            $table->string('bale_id', 50)->nullable()->index();
            $table->string('ref_id', 10)->nullable();
            $table->tinyInteger('step')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        DB::table('admins')->insert(Variable::getAdmins());

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
