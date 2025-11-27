<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Base materials table
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('author');
            $table->enum('type', ['fisico', 'digital', 'hibrido']);
            $table->string('code')->unique();
            $table->text('keywords')->nullable();
            $table->timestamps();
        });

        // Physical materials extension
        Schema::create('material_fisicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->string('isbn')->unique()->nullable();
            $table->integer('stock')->default(0);
            $table->integer('available')->default(0);
            $table->string('publisher')->nullable();
            $table->year('publication_year')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });

        // Digital materials extension
        Schema::create('material_digitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->text('url');
            $table->boolean('downloadable')->default(false);
            $table->string('file_type')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('access_count')->default(0);
            $table->string('license')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_digitals');
        Schema::dropIfExists('material_fisicos');
        Schema::dropIfExists('materials');
    }
};
