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
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('duration'); // Duration in days/weeks/months
            $table->unsignedBigInteger('school_id'); // Foreign key for schools
            $table->unsignedBigInteger('category_id'); // Foreign key for categories
            $table->timestamps();

                // Foreign key constraints
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
