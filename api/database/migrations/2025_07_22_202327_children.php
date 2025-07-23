<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('educational_placement')->nullable();
            $table->boolean('is_initial_assessment')->default(false);
            $table->boolean('is_follow_up')->default(false);
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->date('date_of_birth');
            $table->date('date_of_assessment');
            $table->string('age_at_consult');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->text('siblings')->nullable();
            $table->string('mother_name');
            $table->string('mother_occupation')->nullable();
            $table->string('mother_contact');
            $table->string('father_name');
            $table->string('father_occupation')->nullable();
            $table->string('father_contact');
            $table->text('medical_diagnosis')->nullable();
            $table->string('referring_doctor')->nullable();
            $table->date('last_assessment_date')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->boolean('occupational_therapy')->default(false);
            $table->boolean('physical_therapy')->default(false);
            $table->boolean('behavioral_therapy')->default(false);
            $table->boolean('speech_therapy')->default(false);
            $table->string('school')->nullable();
            $table->string('grade')->nullable();
            $table->string('placement')->nullable();
            $table->string('year')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('children');
    }
};