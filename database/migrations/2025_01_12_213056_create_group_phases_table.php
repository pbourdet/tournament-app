<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('group_phases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tournament_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('number_of_groups');
            $table->unsignedSmallInteger('qualifying_per_group');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_phases');
    }
};
