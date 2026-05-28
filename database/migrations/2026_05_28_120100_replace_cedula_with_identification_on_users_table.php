<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'cedula')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('identification_type', 40)->nullable()->after('name');
            $table->string('identification_number', 30)->nullable()->after('identification_type');
        });

        DB::table('users')
            ->whereNotNull('cedula')
            ->update([
                'identification_type' => 'cc',
                'identification_number' => DB::raw('cedula'),
            ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['cedula']);
            $table->dropColumn('cedula');
            $table->unique(['identification_type', 'identification_number'], 'users_identification_unique');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'identification_type')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('cedula', 20)->nullable()->unique()->after('name');
        });

        DB::table('users')
            ->where('identification_type', 'cc')
            ->whereNotNull('identification_number')
            ->update([
                'cedula' => DB::raw('identification_number'),
            ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_identification_unique');
            $table->dropColumn(['identification_type', 'identification_number']);
        });
    }
};
