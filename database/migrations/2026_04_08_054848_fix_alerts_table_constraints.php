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
        // Drop PostgreSQL check constraints that were created by the original ENUMs
        if (config('database.default') === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE alerts DROP CONSTRAINT IF EXISTS alerts_type_check');
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE alerts DROP CONSTRAINT IF EXISTS alerts_urgency_level_check');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-adding constraints would require knowing the exact values, 
        // but since we moved to string, we can leave it or re-add them if needed.
    }
};
