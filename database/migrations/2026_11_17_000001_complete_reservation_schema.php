<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'no_hp')) {
            Schema::table('users', fn (Blueprint $table) => $table->string('no_hp', 30)->nullable());
        }
        if (!Schema::hasColumn('orders', 'date_end')) {
            Schema::table('orders', fn (Blueprint $table) => $table->dateTime('date_end')->nullable());
        }
        if (!Schema::hasColumn('orders', 'selesai')) {
            Schema::table('orders', fn (Blueprint $table) => $table->dateTime('selesai')->nullable());
        }
        if (!Schema::hasColumn('orders', 'status_order')) {
            Schema::table('orders', fn (Blueprint $table) => $table->unsignedTinyInteger('status_order')->nullable());
        }
        if (!Schema::hasColumn('orders', 'pesan')) {
            Schema::table('orders', fn (Blueprint $table) => $table->text('pesan')->nullable());
        }
        if (!Schema::hasColumn('schedules', 'time_end')) {
            Schema::table('schedules', fn (Blueprint $table) => $table->string('time_end')->nullable());
        }
    }

    public function down(): void
    {
        // Intentional no-op: do not delete pre-existing order/user data during rollback.
    }
};
