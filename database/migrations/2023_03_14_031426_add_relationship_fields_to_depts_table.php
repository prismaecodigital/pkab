<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToDeptsTable extends Migration
{
    public function up()
    {
        Schema::table('depts', function (Blueprint $table) {
            $table->unsignedBigInteger('bu_id')->nullable();
            $table->foreign('bu_id', 'bu_fk_8175382')->references('id')->on('bus');
        });
    }
}