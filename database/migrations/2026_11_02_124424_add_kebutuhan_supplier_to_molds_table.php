<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('molds', function (Blueprint $table) {
            if (! Schema::hasColumn('molds', 'kebutuhan')) {
                $table->unsignedInteger('kebutuhan')->nullable();
            }
            if (! Schema::hasColumn('molds', 'supplier')) {
                $table->string('supplier')->nullable();
            }
        });
    }

    public function down(): void
    {
        // dibiarkan kosong karena kolom supplier mungkin berasal dari migration lain
    }
};