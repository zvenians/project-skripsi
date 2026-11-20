<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code', 30)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('facility_managers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 30)->default('organization')->index();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('facility_manager_id')->nullable()->constrained()->nullOnDelete();
        });
        Schema::table('facilities', function (Blueprint $table): void {
            $table->foreignId('manager_id')->nullable()->constrained('facility_managers')->nullOnDelete();
            $table->boolean('is_master')->default(true);
        });
        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->text('decision_reason')->nullable();
            $table->index(['facility_id', 'date_end', 'selesai'], 'orders_facility_window_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropIndex('orders_facility_window_index');
            $table->dropConstrainedForeignId('organization_id');
            $table->dropColumn('decision_reason');
        });
        Schema::table('facilities', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('manager_id');
            $table->dropColumn('is_master');
        });
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('organization_id');
            $table->dropConstrainedForeignId('facility_manager_id');
            $table->dropColumn('role');
        });
        Schema::dropIfExists('facility_managers');
        Schema::dropIfExists('organizations');
    }
};
