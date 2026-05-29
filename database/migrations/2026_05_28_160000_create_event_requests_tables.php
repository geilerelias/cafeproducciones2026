<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_request_stages', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('color', 20)->default('#a8322b');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_terminal')->default(false);
            $table->boolean('visible_to_client')->default(true);
            $table->timestamps();
        });

        Schema::create('event_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('client_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('event_type', 40)->default('corporativo');
            $table->date('desired_date')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('guest_count')->nullable();
            $table->string('budget_notes')->nullable();
            $table->string('stage_key')->default('recibida');
            $table->text('client_message')->nullable();
            $table->text('internal_notes')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['stage_key', 'position']);
            $table->index('client_user_id');
        });

        Schema::create('event_request_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_request_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('pendiente');
            $table->boolean('visible_to_client')->default(true);
            $table->date('due_date')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['event_request_id', 'status', 'position']);
        });

        Schema::create('event_request_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 40);
            $table->text('body');
            $table->json('meta')->nullable();
            $table->boolean('visible_to_client')->default(true);
            $table->timestamps();
        });

        (new \Database\Seeders\EventRequestStageSeeder)->run();
    }

    public function down(): void
    {
        Schema::dropIfExists('event_request_activities');
        Schema::dropIfExists('event_request_tasks');
        Schema::dropIfExists('event_requests');
        Schema::dropIfExists('event_request_stages');
    }
};
