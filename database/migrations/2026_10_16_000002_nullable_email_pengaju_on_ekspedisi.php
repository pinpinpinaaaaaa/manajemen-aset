<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ekspedisi', function (Blueprint $table) {
            $table->string('email_pengaju')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ekspedisi', function (Blueprint $table) {
            $table->string('email_pengaju')->nullable(false)->change();
        });
    }
};
