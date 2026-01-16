<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('breed_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('listing_type', ['sale', 'adoption', 'stud'])->default('sale');
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('age_years')->nullable();
            $table->integer('age_months')->nullable();
            $table->enum('gender', ['male', 'female', 'unknown'])->default('unknown');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['draft', 'active', 'sold', 'inactive', 'flagged'])->default('draft');
            $table->boolean('featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('favorites_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'status', 'published_at']);
            $table->index(['location_id', 'status']);
            $table->index(['breed_id', 'status']);
            // Note: fullText index commented for SQLite compatibility
            // For PostgreSQL/MySQL, uncomment: $table->fullText(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
