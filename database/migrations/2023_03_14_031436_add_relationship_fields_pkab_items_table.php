<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsPkabItemsTable extends Migration
{
    public function up()
    {
        Schema::table('pkab_items', function (Blueprint $table) {
            $table->foreignId('bu_id')->references('id')->on('bus')->nullable();
        });
    }
}