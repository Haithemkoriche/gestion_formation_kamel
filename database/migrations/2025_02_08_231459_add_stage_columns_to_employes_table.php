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
        Schema::table('employes', function (Blueprint $table) {
            //
            $table->foreignId('school_id')->nullable()->constrained('schools');
            $table->foreignId('formation_id')->nullable()->constrained('formations');
            $table->integer('stage_duration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            //
            $table->dropForeign(['school_id']);
            $table->dropForeign(['formation_id']);
            $table->dropColumn(['school_id', 'formation_id', 'stage_duration']);
        });
    }
};
