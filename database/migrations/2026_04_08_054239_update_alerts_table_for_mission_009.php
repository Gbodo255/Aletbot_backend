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
        Schema::table('alerts', function (Blueprint $table) {
            $table->renameColumn('name', 'reporter_name');
            // Change enums - Using change() requires doctrine/dbal or Laravel 10+ native support
            // Here we use native Laravel support for changing columns
            $table->string('type')->default('Alerte')->change(); // From Urgence, Information, Alerte, Autre
            $table->string('urgency_level')->default('Moyen')->change(); // From Faible, Moyen, Critique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->renameColumn('reporter_name', 'name');
            $table->enum('type', ['telegram'])->default('telegram')->change();
            $table->enum('urgency_level', ['low', 'medium', 'high', 'critical'])->default('medium')->change();
        });
    }
};
