<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `User::$fillable` and the notification observers reference these columns,
     * but no migration ever created them. Guarded so databases that already
     * have them (created by hand in production) are left untouched.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'no_hp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('no_hp')->nullable()->after('email');
            });
        }

        if (! Schema::hasColumn('users', 'notifable_email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('notifable_email')->default(false)->after('no_hp');
            });
        }

        if (! Schema::hasColumn('users', 'notifable_wa')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('notifable_wa')->default(false)->after('notifable_email');
            });
        }
    }

    public function down(): void
    {
        foreach (['notifable_wa', 'notifable_email', 'no_hp'] as $column) {
            if (Schema::hasColumn('users', $column)) {
                Schema::table('users', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
