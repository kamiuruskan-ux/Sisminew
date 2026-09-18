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
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('scanned_via_qr')->default(false)->after('status');
            $table->foreignId('scanner_id')->nullable()->constrained('users')->onDelete('set null')->after('scanned_via_qr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['scanner_id']);
            $table->dropColumn(['scanned_via_qr', 'scanner_id']);
        });
    }
};
