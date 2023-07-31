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
        Schema::create('marketlist_order_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ml_id')->references('id')->on('marketlist_order')->onDelete('cascade');
            $table->foreignId('item_id')->references('id')->on('marketlist_items')->onDelete('cascade');
            $table->date('required_date');
            $table->integer('qty');
            $table->string('satuan');
            $table->integer('approved_qty')->nullable();
            $table->date('approved_date')->nullable();
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('marketlist_order_item');

    }
};
