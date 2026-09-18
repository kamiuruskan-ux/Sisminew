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
        // 1. Add NIP to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('phone');
        });

        // 2. Add homeroom_teacher_id to classes table
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('homeroom_teacher_id')->nullable()->after('major_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['homeroom_teacher_id']);
            $table->dropColumn('homeroom_teacher_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nip');
        });
    }
};
