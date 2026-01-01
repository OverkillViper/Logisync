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
        Schema::create('project_tracking_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_tracking_stage_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('value', 4, 2)->default(0); // numeric progress value (e.g. 0.25)
            $table->timestamps();
            $table->enum('type', ['expected', 'actual']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_tracking_entries');
    }
};
