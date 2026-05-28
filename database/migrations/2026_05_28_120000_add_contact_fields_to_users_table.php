<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identification_type', 40)->nullable()->after('name');
            $table->string('identification_number', 30)->nullable()->after('identification_type');
            $table->unique(['identification_type', 'identification_number'], 'users_identification_unique');
            $table->string('phone', 20)->nullable()->after('identification_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_identification_unique');
            $table->dropColumn(['identification_type', 'identification_number', 'phone']);
        });
    }
};
