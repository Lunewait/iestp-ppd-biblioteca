<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE approval_logs 
                MODIFY COLUMN action 
                ENUM('requested', 'approved', 'rejected', 'cancelled', 'collected', 'expired', 'returned') 
                NOT NULL
            ");
        } elseif ($driver === 'pgsql') {
            // PostgreSQL logic would go here
            DB::statement("ALTER TABLE approval_logs DROP CONSTRAINT IF EXISTS approval_logs_action_check");
            DB::statement("
                ALTER TABLE approval_logs 
                ADD CONSTRAINT approval_logs_action_check 
                CHECK (action IN ('requested', 'approved', 'rejected', 'cancelled', 'collected', 'expired', 'returned'))
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE approval_logs 
                MODIFY COLUMN action 
                ENUM('requested', 'approved', 'rejected', 'cancelled') 
                NOT NULL
            ");
        }
    }
};
