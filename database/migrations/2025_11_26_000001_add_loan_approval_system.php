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
        // Add approval_status to prestamos table
        if (!Schema::hasColumn('prestamos', 'approval_status')) {
            Schema::table('prestamos', function (Blueprint $table) {
                $table->enum('approval_status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending')->after('status');
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
                $table->text('approval_reason')->nullable()->after('approved_by');
                $table->dateTime('approval_date')->nullable()->after('approval_reason');
            });
        }

        // Create approval_logs table
        if (!Schema::hasTable('approval_logs')) {
            Schema::create('approval_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('prestamo_id');
                $table->unsignedBigInteger('reviewer_id');
                $table->enum('action', ['requested', 'approved', 'rejected', 'cancelled']);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('prestamo_id')->references('id')->on('prestamos')->onDelete('cascade');
                $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prestamos', function (Blueprint $table) {
            if (Schema::hasColumn('prestamos', 'approval_status')) {
                $table->dropColumn('approval_status');
            }
            if (Schema::hasColumn('prestamos', 'approved_by')) {
                $table->dropColumn('approved_by');
            }
            if (Schema::hasColumn('prestamos', 'approval_reason')) {
                $table->dropColumn('approval_reason');
            }
            if (Schema::hasColumn('prestamos', 'approval_date')) {
                $table->dropColumn('approval_date');
            }
        });

        Schema::dropIfExists('approval_logs');
    }
};
