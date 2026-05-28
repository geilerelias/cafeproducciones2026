<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('cliente')->after('password');
            $table->json('permissions')->nullable()->after('role');
        });

        DB::table('users')
            ->where('email', User::SUPER_ADMIN_EMAIL)
            ->update([
                'role' => 'superadmin',
                'permissions' => json_encode(['*']),
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'permissions']);
        });
    }
};
