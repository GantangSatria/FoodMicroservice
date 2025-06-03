<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_tokens', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid');
            $table->string('jwt_id')->unique();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('expired_at')->nullable();
            $table->boolean('is_revoked')->default(false);

            $table->foreign('user_uuid')->references('uuid')->on('auth_users')->onDelete('cascade');
            $table->index('user_uuid');
            $table->index('jwt_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tokens');
    }
};
