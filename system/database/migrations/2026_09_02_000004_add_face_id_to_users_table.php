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
            $table->longText('face_embedding')->nullable()->after('avatar');
            $table->string('face_photo')->nullable()->after('face_embedding');
            $table->timestamp('face_registered_at')->nullable()->after('face_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['face_embedding', 'face_photo', 'face_registered_at']);
        });
    }
};
