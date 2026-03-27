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
        Schema::create('parts', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('category');
        $table->text('description')->nullable();
        $table->string('image');
        // Compatibility fields
        $table->json('compatibility')->nullable(); // Stores requirements like min_speeds, max_speeds, frame_type, etc.
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
