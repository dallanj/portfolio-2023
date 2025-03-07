<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('hash')->unique();
            // Original File Name and Path to the File
            $table->string('filename');
            $table->string('path');
            // Image, video, audio
            $table->string('type');
            // Optional to rename the file to be more specific
            $table->string('name')->nullable();
            // Order heirarchy
            $table->unsignedInteger('order')->default(0);
            // Project, blog, etc...
            $table->nullableMorphs('mediaable');
            // Created by authed user
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            // Timestamps with soft delete
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
