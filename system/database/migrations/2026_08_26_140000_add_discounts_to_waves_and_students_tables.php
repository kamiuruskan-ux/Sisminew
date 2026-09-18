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
        Schema::table('waves', function (Blueprint $table) {
            $table->decimal('spp_discount', 12, 2)->default(0)->after('registration_fee');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->decimal('spp_discount', 12, 2)->default(0)->after('photo');
            $table->string('discount_description')->nullable()->after('spp_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('waves', function (Blueprint $table) {
            $table->dropColumn('spp_discount');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['spp_discount', 'discount_description']);
        });
    }
};
