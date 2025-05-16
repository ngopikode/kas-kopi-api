<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('request_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_authenticated')->default(false);
            $table->text('url')->nullable();
            $table->string('method', 10)->nullable();
            $table->json('headers')->nullable();
            $table->json('parameters')->nullable();
            $table->string('file')->nullable();
            $table->integer('line')->nullable();
            $table->text('message')->nullable();
            $table->json('trace')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
