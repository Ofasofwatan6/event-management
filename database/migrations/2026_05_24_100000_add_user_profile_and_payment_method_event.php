<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('role');
            $table->string('nickname')->nullable()->after('profile_image');
            $table->string('volunteer_status')->nullable()->after('nickname');
            $table->string('city')->nullable()->after('volunteer_status');
            $table->string('phone', 20)->nullable()->after('city');
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->after('event_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->string('transfer_proof')->nullable()->after('admin_note');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE event_registrations MODIFY status ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE event_registrations MODIFY status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('refunds', function (Blueprint $table) {
            $table->dropColumn('transfer_proof');
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_id');
            $table->dropConstrainedForeignId('organization_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_image', 'nickname', 'volunteer_status', 'city', 'phone']);
        });
    }
};
