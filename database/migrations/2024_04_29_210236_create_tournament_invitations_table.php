<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tournament_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tournament_id')->constrained()->cascadeOnDelete();
            $table->unique(['tournament_id']);
            $table->string('code', 6)->unique();
            $table->dateTime('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_invitations');
    }
};
