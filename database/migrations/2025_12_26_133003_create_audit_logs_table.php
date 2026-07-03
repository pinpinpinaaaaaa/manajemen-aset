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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            // siapa
            $table->string('id_user', 10)->nullable();

            // aksi
            $table->string('action', 30); 
            // contoh: create, update, delete, login, approve

            // objek
            $table->string('table_name', 50);
            $table->string('record_id', 50)->nullable();

            // data
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();

            // info tambahan
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
