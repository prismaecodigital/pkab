<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `marketlist_order` was created before the `site` table, so the foreign key
     * could only be declared on databases where `site` already existed. Add it
     * where it is missing (fresh installs) and leave existing databases alone.
     */
    public function up(): void
    {
        if (! Schema::hasTable('marketlist_order') || ! Schema::hasTable('site')) {
            return;
        }

        $hasSiteForeignKey = collect(Schema::getForeignKeys('marketlist_order'))
            ->contains(fn (array $key) => $key['foreign_table'] === 'site');

        if ($hasSiteForeignKey) {
            return;
        }

        Schema::table('marketlist_order', function (Blueprint $table) {
            $table->foreign('site_id')->references('id')->on('site')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('marketlist_order')) {
            return;
        }

        $hasSiteForeignKey = collect(Schema::getForeignKeys('marketlist_order'))
            ->contains(fn (array $key) => $key['foreign_table'] === 'site');

        if (! $hasSiteForeignKey) {
            return;
        }

        Schema::table('marketlist_order', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
        });
    }
};
