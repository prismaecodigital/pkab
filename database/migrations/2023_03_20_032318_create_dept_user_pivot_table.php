<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeptUserPivotTable extends Migration
{
    public function up()
    {
        Schema::create('dept_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'user_id_fk_8202058')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('dept_id');
            $table->foreign('dept_id', 'dept_id_fk_8202058')->references('id')->on('depts')->onDelete('cascade');
        });
    }
}