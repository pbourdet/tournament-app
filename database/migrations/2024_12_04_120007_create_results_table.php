<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('match_id')->index()->constrained()->cascadeOnDelete();
            $table->string('contestant_type');
            $table->uuid('winner_id');
            $table->uuid('loser_id');
            $table->integer('winner_score');
            $table->integer('loser_score');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
