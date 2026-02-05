<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foi_requests', function (Blueprint $table) {
            $table->id();
            $table->string('wdtk_url_title')->unique();
            $table->string('title');
            $table->foreignId('foi_authority_id')->nullable()->constrained('foi_authorities')->nullOnDelete();
            $table->string('authority_name')->nullable();
            $table->string('status');
            $table->string('display_status')->nullable();
            $table->text('description')->nullable();
            $table->string('wdtk_url');
            $table->string('requester_name')->nullable();
            $table->timestamp('request_created_at')->nullable();
            $table->timestamp('request_updated_at')->nullable();
            $table->boolean('is_tracked')->default(true);
            $table->text('internal_notes')->nullable();
            $table->json('tags')->nullable();
            $table->json('raw_json')->nullable();
            $table->timestamp('last_polled_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('is_tracked');
            $table->index(['status', 'is_tracked']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foi_requests');
    }
};
