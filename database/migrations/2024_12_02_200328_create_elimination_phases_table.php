<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('phases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tournament_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['group', 'elimination']);
            $table->jsonb('configuration');
            $table->timestamps();

            $table->unique(['tournament_id', 'type']);
        });

        DB::statement("
            ALTER TABLE phases
            ADD CONSTRAINT check_configuration_keys
            CHECK (
                configuration->'numberOfContestants' IS NULL OR
                (
                    jsonb_typeof(configuration->'numberOfContestants') = 'number' AND
                    (configuration->>'numberOfContestants')::integer > 1
                )
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('phases');
    }
};
