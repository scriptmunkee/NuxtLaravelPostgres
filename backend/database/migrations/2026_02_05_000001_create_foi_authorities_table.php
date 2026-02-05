<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foi_authorities', function (Blueprint $table) {
            $table->id();
            $table->string('wdtk_slug')->unique();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('url')->nullable();
            $table->text('notes')->nullable();
            $table->json('raw_json')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foi_authorities');
    }
};
