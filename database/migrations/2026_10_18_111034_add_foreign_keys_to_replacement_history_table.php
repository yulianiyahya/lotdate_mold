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
        Schema::table('replacement_history', function (Blueprint $table) {
            $table->foreign(['approved_by'], 'fk_rh_approver')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['mold_id'], 'fk_rh_mold')->references(['id'])->on('molds')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['changed_by'], 'fk_rh_user')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('replacement_history', function (Blueprint $table) {
            $table->dropForeign('fk_rh_approver');
            $table->dropForeign('fk_rh_mold');
            $table->dropForeign('fk_rh_user');
        });
    }
};
