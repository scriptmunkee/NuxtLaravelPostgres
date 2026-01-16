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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->boolean('is_verified')->default(false)->after('phone');
            $table->enum('id_verification_status', ['none', 'pending', 'verified', 'rejected'])->default('none')->after('is_verified');
            $table->string('profile_image')->nullable()->after('id_verification_status');
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null')->after('profile_image');
            $table->text('bio')->nullable()->after('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn(['phone', 'is_verified', 'id_verification_status', 'profile_image', 'location_id', 'bio']);
        });
    }
};
