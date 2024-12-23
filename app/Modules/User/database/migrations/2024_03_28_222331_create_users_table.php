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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->unique();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->integer('isactive')->default(1);
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->unsignedBigInteger('profile_group_id')->nullable();
            $table->foreign('profile_group_id')->references('id')->on('profilegroups')->onDelete('cascade');
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('cascade');
            $table->double('workingHours')->nullable();
            $table->integer('sby_workingHours')->default(0)->nullable();
            $table->integer('checker_workingHours')->default(0)->nullable();
            $table->integer('deckman_workingHours')->default(0)->nullable();
            $table->integer('assistant_workingHours')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
