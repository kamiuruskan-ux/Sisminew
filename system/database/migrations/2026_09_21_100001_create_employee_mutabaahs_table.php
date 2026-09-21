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
        if (!Schema::hasTable('employee_mutabaahs')) {
            Schema::create('employee_mutabaahs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->date('date');

                // Shalat Fardhu Berjamaah
                $table->boolean('subuh_jamaah')->default(false);
                $table->boolean('dzuhur_jamaah')->default(false);
                $table->boolean('ashar_jamaah')->default(false);
                $table->boolean('maghrib_jamaah')->default(false);
                $table->boolean('isya_jamaah')->default(false);

                // Ibadah Sunnah
                $table->unsignedTinyInteger('rawatib_count')->default(0); // rakaat (target 10-12)
                $table->boolean('dhuha')->default(false);
                $table->boolean('tahajjud_witir')->default(false);
                
                // Tilawah & Dzikir
                $table->unsignedSmallInteger('tilawah_pages')->default(0); // target min 2 lembar / 4 halaman
                $table->boolean('dzikir_pagi_petang')->default(false);

                // Sunnah Lainnya
                $table->boolean('puasa_sunnah')->default(false);
                $table->boolean('sedekah')->default(false);

                // Daily score (0-100) & notes
                $table->decimal('daily_score', 5, 2)->default(0);
                $table->text('notes')->nullable();

                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['user_id', 'date'], 'employee_mutabaah_unique');
                $table->index(['date', 'daily_score']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_mutabaahs');
    }
};
