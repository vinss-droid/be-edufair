<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_webinars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_id');
            $table->string('year');
            $table->string('name');
            $table->string('email');
            $table->string('no_whatsapp');
            $table->string('agency_name');
            $table->string('province');
            $table->string('regency');
            $table->string('proof_himsika');
            $table->string('proof_edufair');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('register_webinars');
    }
};
