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
        Schema::table('molds', function (Blueprint $table) {
            $table->foreign(['pic_id'], 'fk_molds_pic')->references(['id'])->on('users')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('molds', function (Blueprint $table) {
            $table->dropForeign('fk_molds_pic');
        });
    }
};
