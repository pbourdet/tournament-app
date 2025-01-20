<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('phase_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('size');
            $table->string('name');
            $table->timestamps();

            $table->index('phase_id');
        });

        Schema::create('group_contestant', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('group_id')->constrained()->cascadeOnDelete();
            $table->uuidMorphs('contestant');
            $table->timestamps();

            $table->unique(['group_id', 'contestant_type', 'contestant_id']);
            $table->index('group_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
