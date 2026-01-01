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
        Schema::create('evaluation_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
            $table->foreignId('criteria_id')->constrained('evaluation_criterias')->cascadeOnDelete();
            $table->foreignId('trainee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->default(0);
            $table->timestamps();

            $table->unique(['evaluation_id', 'criteria_id', 'trainee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_scores');
    }
};
