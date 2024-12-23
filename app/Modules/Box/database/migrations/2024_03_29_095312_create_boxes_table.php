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
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('equipement_id')->nullable()->constrained('equipements')->onDelete('cascade');
            $table->foreignId('planning_id')->constrained('plannings')->onDelete('cascade');
            $table->string('start_time');
            $table->string('ends_time');
            $table->boolean('role')->nullable();
            $table->boolean('break')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
