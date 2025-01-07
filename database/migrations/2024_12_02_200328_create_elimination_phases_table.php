<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('phases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tournament_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['elimination']);
            $table->timestamps();

            $table->unique(['tournament_id', 'type']);
        });

        Schema::create('elimination_phase_details', function (Blueprint $table) {
            $table->uuid('phase_id')->primary();
            $table->foreign('phase_id')->references('id')->on('phases')->cascadeOnDelete();
            $table->unsignedSmallInteger('number_of_contestants');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phases');
        Schema::dropIfExists('elimination_phases_details');
    }
};
