<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foi_status_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('foi_request_id')->constrained('foi_requests')->cascadeOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->string('display_status')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_acknowledged')->default(false);
            $table->timestamp('detected_at');
            $table->timestamps();

            $table->index('foi_request_id');
            $table->index('is_acknowledged');
            $table->index('detected_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foi_status_updates');
    }
};
