<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('location')->nullable();
            $table->boolean('is_remote')->default(false);
            $table->enum('type', ['full_time', 'part_time', 'contract', 'freelance', 'internship'])->default('full_time');
            $table->enum('experience_level', ['entry', 'mid', 'senior', 'lead'])->default('entry');
            $table->unsignedInteger('salary_min')->nullable();
            $table->unsignedInteger('salary_max')->nullable();
            $table->string('salary_currency', 10)->default('USD');
            $table->enum('status', ['draft', 'active', 'paused', 'closed'])->default('draft');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
