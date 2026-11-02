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
            $table->foreignId('site_id')->nullable();
            $table->timestamps();
        });

        // `site` is created by a later migration, so the constraint is added
        // once it exists (see 2026_11_01_000000_add_site_foreign_key_to_marketlist_order_table).
        if (Schema::hasTable('site')) {
            Schema::table('marketlist_order', function (Blueprint $table) {
                $table->foreign('site_id')->references('id')->on('site')->onDelete('cascade');
            });
        }
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
