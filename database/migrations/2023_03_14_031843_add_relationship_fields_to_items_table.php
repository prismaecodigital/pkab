<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToItemsTable extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger('pkab_id')->nullable();
            $table->foreign('pkab_id', 'pkab_fk_8175429')->references('id')->on('pkab_items')->onDelete('cascade');
        });
    }
}