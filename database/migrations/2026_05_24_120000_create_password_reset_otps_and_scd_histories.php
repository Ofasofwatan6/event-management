<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('password_reset_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('otp', 6);
            $table->enum('context', ['user', 'organization'])->default('user');
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->json('snapshot');
            $table->timestamp('valid_from');
            $table->timestamp('valid_to')->nullable();
            $table->timestamps();
        });

        Schema::create('event_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->json('snapshot');
            $table->timestamp('valid_from');
            $table->timestamp('valid_to')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_histories');
        Schema::dropIfExists('user_histories');
        Schema::dropIfExists('password_reset_otps');
    }
};
