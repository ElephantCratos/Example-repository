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
        Schema::create('logs_requests', function (Blueprint $table) {
            $table->id();
            $table->string('api_method');
            $table->string('http_method');
            $table->string('controller_path');
            $table->string('controller_method');
            $table->text('request_body');
            $table->text('request_headers');
            $table->string('user_id')->nullable();
            $table->string('user_ip');
            $table->string('user_agent');
            $table->integer('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->text('response_headers')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_requests');
    }
};
