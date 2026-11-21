<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('molds', function (Blueprint $table) {
            $table->string('spec')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('molds', function (Blueprint $table) {
            $table->dropColumn('spec');
        });
    }
};