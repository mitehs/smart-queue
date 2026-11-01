<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('password'); // admin, staff, user
            $table->string('phone')->nullable()->after('role');
            $table->foreignId('place_id')->nullable()->after('phone')->constrained('places')->nullOnDelete();
            $table->string('avatar')->nullable()->after('place_id');
            $table->boolean('is_active')->default(true)->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('place_id');
            $table->dropColumn(['role', 'phone', 'avatar', 'is_active']);
        });
    }
};
