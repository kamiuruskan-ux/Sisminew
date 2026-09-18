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
        if (!Schema::hasTable('login_security_logs')) {
            Schema::create('login_security_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('identifier')->index();
                $table->string('ip_address', 45)->index();
                $table->text('user_agent')->nullable();
                $table->string('status', 30)->index(); // success, failed_password, failed_otp, captcha_failed, locked_account, blocked_ip
                $table->text('failure_reason')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('login_security_logs', function (Blueprint $table) {
                if (Schema::hasColumn('login_security_logs', 'identity') && !Schema::hasColumn('login_security_logs', 'identifier')) {
                    $table->renameColumn('identity', 'identifier');
                } elseif (!Schema::hasColumn('login_security_logs', 'identifier')) {
                    $table->string('identifier')->index()->after('user_id');
                }
                if (!Schema::hasColumn('login_security_logs', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                }
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->integer('failed_login_attempts')->default(0)->after('status');
            }
            if (!Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('locked_until');
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_security_logs');

        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('users', 'failed_login_attempts')) {
                $columnsToDrop[] = 'failed_login_attempts';
            }
            if (Schema::hasColumn('users', 'locked_until')) {
                $columnsToDrop[] = 'locked_until';
            }
            if (Schema::hasColumn('users', 'last_login_at')) {
                $columnsToDrop[] = 'last_login_at';
            }
            if (Schema::hasColumn('users', 'last_login_ip')) {
                $columnsToDrop[] = 'last_login_ip';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
