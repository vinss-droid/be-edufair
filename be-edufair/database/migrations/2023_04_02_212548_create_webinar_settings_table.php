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
        Schema::create('webinar_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('register_date')->nullable();
            $table->string('implementation_date')->nullable();
            $table->string('benefit_date')->nullable();
            $table->string('webinar_group');
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
        Schema::dropIfExists('webinar_settings');
    }
};
