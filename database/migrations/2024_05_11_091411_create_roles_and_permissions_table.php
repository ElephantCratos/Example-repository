<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles_and_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->references('id')->on('roles');
            $table->foreignId('permission_id')->references('id')->on('permissions');
            $table->timestamps();
            $table->foreignId('created_by')->references('id')->on('users');
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_and_permissions');
    }
};