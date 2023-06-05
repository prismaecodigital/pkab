<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuUserPivotTable extends Migration
{
    public function up()
    {
        Schema::create('bu_user', function (Blueprint $table) {
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('bu_id')->references('id')->on('bus')->onDelete('cascade');
        });
    }
}