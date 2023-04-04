<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('status_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status')->nullable()->default('leader_acc');
            $table->foreignId('user_id')->reference('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->foreignId('pkab_id')->reference('id')->on('pkab_items')->onDelete('cascade');
        });
    }
}