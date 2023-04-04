<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToPkabItemsTable extends Migration
{
    public function up()
    {
        Schema::table('pkab_items', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'user_fk_8175233')->references('id')->on('users');
            $table->unsignedBigInteger('dept_id')->nullable();
            $table->foreign('dept_id', 'dept_fk_8175479')->references('id')->on('depts')->onDelete('cascade');
        });
    }
}