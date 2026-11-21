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
            $table->string('replacement_location', 150)->nullable()->after('replacement_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('replacement_history', function (Blueprint $table) {
            $table->dropColumn('replacement_location');
        });
    }
};