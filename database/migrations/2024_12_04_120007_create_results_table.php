<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('match_id')->index()->constrained()->cascadeOnDelete();
            $table->uuidMorphs('contestant');
            $table->enum('outcome', ['Win', 'Loss', 'Tie']);
            $table->integer('score');
            $table->timestamps();

            $table->unique(['match_id', 'contestant_type', 'contestant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
