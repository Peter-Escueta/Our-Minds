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
        Schema::create('therapies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
            $table->string('type');
            $table->boolean('is_received');
            $table->string('therapy_center')->nullable();
            $table->string('therapist_name')->nullable();
            $table->string('therapist_email')->nullable();
            $table->string('therapist_contact_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapy');
    }
};
