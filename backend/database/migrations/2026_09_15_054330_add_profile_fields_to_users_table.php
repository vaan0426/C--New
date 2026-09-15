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
            $table->string('role')->default('user')->after('email'); // admin | editor | user
            $table->string('status')->default('active')->after('role'); // active | frozen
            $table->string('phone')->nullable()->after('status');
            $table->string('profile_photo_path')->nullable()->after('phone');
            $table->text('internal_note')->nullable()->after('profile_photo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'phone', 'profile_photo_path', 'internal_note']);
        });
    }
};
