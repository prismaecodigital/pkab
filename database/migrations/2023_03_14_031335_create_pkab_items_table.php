<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePkabItemsTable extends Migration
{
    public function up()
    {
        Schema::create('pkab_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable()->unique();
            $table->date('req_date');
            $table->string('status')->nullable()->default('leader_acc');
            $table->string('ket')->nullable();
            $table->timestamps();
        });
    }
}