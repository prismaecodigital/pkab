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
        //
        Schema::create('marketlist_order', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->nullable();
            $table->string('event');
            $table->string('status');
            $table->foreignId('bu_id')->references('id')->on('bus')->onDelete('cascade')->nullable();
            $table->foreignId('dept_id')->nullable()->references('id')->on('depts')->onDelete('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade')->nullable();
            $table->foreignId('site_id')->nullable()->references('id')->on('site')->onDelete('Cascade');
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
        //
        Schema::dropIfExists('marketlist_order');

    }
};
