<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tournament_id')->index()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('match_contestant', function (Blueprint $table) {
            $table->foreignUuid('match_id')->constrained()->cascadeOnDelete();
            $table->uuidMorphs('contestant');
            $table->timestamps();

            $table->primary(['match_id', 'contestant_type', 'contestant_id']);
            $table->index('match_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
        Schema::dropIfExists('match_contestant');
    }
};
