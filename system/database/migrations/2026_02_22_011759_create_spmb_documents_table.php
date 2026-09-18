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
        Schema::create('spmb_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_registration_id')->constrained()->onDelete('cascade');
            $table->string('type'); // ktp, kk, photo, etc.
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_mime');
            $table->integer('file_size');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_documents');
    }
};
