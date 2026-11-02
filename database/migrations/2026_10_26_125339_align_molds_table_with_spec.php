<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kode lama (MLD-01) jadi part_name
        DB::table('molds')
            ->where(fn ($q) => $q->whereNull('part_name')->orWhere('part_name', ''))
            ->update(['part_name' => DB::raw('part_number')]);

        // 2. Kolom baru
        Schema::table('molds', function (Blueprint $table) {
            $table->string('part_no', 150)->nullable()->after('part_name');
            $table->unsignedInteger('lead_time_value')->default(2)->after('frequency_unit');
            $table->enum('lead_time_unit', ['Day', 'Month', 'Year'])->default('Month')->after('lead_time_value');
        });
        if (! Schema::hasColumn('molds', 'category')) {
            Schema::table('molds', fn (Blueprint $t) => $t->string('category', 20)->nullable());
        }

        // 3. Hapus part_number (unique index ikut hilang)
        try {
            Schema::table('molds', fn (Blueprint $t) => $t->dropUnique('molds_part_number_unique'));
        } catch (\Throwable $e) {
            // index tidak ada, lanjut
        }
        Schema::table('molds', fn (Blueprint $t) => $t->dropColumn('part_number'));

        // 4. type (Blow/Injection) pindah ke category (Blow/Inject), type jadi teks bebas
        DB::statement("ALTER TABLE molds MODIFY category VARCHAR(20) NULL");
        DB::statement("UPDATE molds SET category = CASE UPPER(type) WHEN 'INJECTION' THEN 'Inject' WHEN 'INJECT' THEN 'Inject' WHEN 'BLOW' THEN 'Blow' ELSE NULL END");
        DB::statement("ALTER TABLE molds MODIFY category ENUM('Blow','Inject') NULL");
        DB::statement("ALTER TABLE molds MODIFY type VARCHAR(100) NULL");
        DB::table('molds')->update(['type' => null]);

        // 5. frequency_unit jadi ENUM Day/Month/Year (data lama semuanya tahunan)
        DB::statement("ALTER TABLE molds MODIFY frequency_unit VARCHAR(10) NOT NULL DEFAULT 'Year'");
        DB::table('molds')->update(['frequency_unit' => 'Year']);
        DB::statement("ALTER TABLE molds MODIFY frequency_unit ENUM('Day','Month','Year') NOT NULL DEFAULT 'Year'");

        // 6. Ukuran kolom lain
        DB::statement("ALTER TABLE molds MODIFY part_name VARCHAR(150) NOT NULL");
        DB::statement("ALTER TABLE molds MODIFY location VARCHAR(100) NULL");
    }

    public function down(): void
    {
        // Tidak dapat dikembalikan otomatis.
    }
};