<?php

use App\Enums\EvaluationStatus;
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
        Schema::create('assessment_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->text('background_information')->nullable();
            $table->json('recommendations')->nullable();
            $table->text('websites')->nullable();
            $values = array_column(EvaluationStatus::cases(), 'value');
            $table->enum('status', $values)
                ->default(EvaluationStatus::READY_FOR_EVALUATION->value);
            $table->timestamps();

            $table->index('assessment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_evaluations');
    }
};
