<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_forms', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
            $table->boolean('is_public')->default(true)->after('is_active');
            $table->string('submit_label')->default('Enviar respuesta')->after('is_public');
            $table->string('success_message')->default('Respuesta enviada correctamente.')->after('submit_label');
        });

        DB::table('custom_forms')
            ->orderBy('id')
            ->get(['id', 'title'])
            ->each(function ($form): void {
                $base = Str::slug($form->title) ?: 'formulario';
                $slug = $base;
                $count = 2;

                while (DB::table('custom_forms')->where('slug', $slug)->where('id', '!=', $form->id)->exists()) {
                    $slug = "{$base}-{$count}";
                    $count++;
                }

                DB::table('custom_forms')->where('id', $form->id)->update(['slug' => $slug]);
            });

        Schema::table('custom_forms', function (Blueprint $table) {
            $table->unique('slug');
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('Noticias');
            $table->string('platform')->nullable();
            $table->string('source_url')->nullable();
            $table->string('image_url')->nullable();
            $table->string('excerpt', 500);
            $table->longText('body')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');

        Schema::table('custom_forms', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'is_public', 'submit_label', 'success_message']);
        });
    }
};
