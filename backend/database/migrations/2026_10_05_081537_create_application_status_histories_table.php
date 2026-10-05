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
        Schema::create('application_status_histories', function (Blueprint $table) {
            $table->id();

            // The application whose status changed.
            $table->foreignId('application_id')
                ->constrained()
                ->cascadeOnDelete();

            // Status before the change.
            // Nullable because a newly created application has no previous status.
            $table->string('from_status')->nullable();

            // New/current status after the change.
            $table->string('to_status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_status_histories');
    }
};
