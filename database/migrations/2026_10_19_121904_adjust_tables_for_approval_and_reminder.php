<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // users: tingkat approval (1 = Checked, 2 = Approved), hanya untuk role Atasan
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('approval_level')->nullable()->after('role');
        });

        // replacement_history: simpan frekuensi saat penggantian dilakukan
        Schema::table('replacement_history', function (Blueprint $table) {
            $table->unsignedInteger('frequency_value')->nullable()->after('replacement_date');
        });

        // molds: part_name opsional, type jadi enum, frekuensi default tahun
        DB::statement('ALTER TABLE molds MODIFY part_name VARCHAR(100) NULL');
        DB::statement('UPDATE molds SET type = UPPER(type)');
        DB::statement("UPDATE molds SET type = NULL WHERE type NOT IN ('BLOW','INJECTION')");
        DB::statement("ALTER TABLE molds MODIFY type ENUM('BLOW','INJECTION') NULL");
        DB::statement("ALTER TABLE molds MODIFY frequency_unit ENUM('day','week','month','year') NOT NULL DEFAULT 'year'");

        // reminder_emails: nama opsional
        DB::statement('ALTER TABLE reminder_emails MODIFY name VARCHAR(100) NULL');

        // replacement_history: tanggal baru boleh sama dengan tanggal lama
        DB::statement('ALTER TABLE replacement_history DROP CONSTRAINT chk_rh_new_due');
        DB::statement('ALTER TABLE replacement_history ADD CONSTRAINT chk_rh_new_due CHECK (new_due_date >= old_due_date)');

        // Tabel baru: approval bertingkat
        Schema::create('replacement_approvals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('replacement_id');
            $table->unsignedTinyInteger('level'); // 1 atau 2
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('note')->nullable();
            $table->dateTime('acted_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->unique(['replacement_id', 'level'], 'uq_ra_replacement_level');
            $table->foreign('replacement_id', 'fk_ra_replacement')
                ->references('id')->on('replacement_history')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('approver_id', 'fk_ra_approver')
                ->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('restrict');
        });

        // Tabel baru: riwayat pengiriman email reminder
        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mold_id');
            $table->string('email', 100);
            $table->date('due_date');
            $table->dateTime('sent_at');

            $table->foreign('mold_id', 'fk_rl_mold')
                ->references('id')->on('molds')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
        Schema::dropIfExists('replacement_approvals');

        DB::statement('ALTER TABLE replacement_history DROP CONSTRAINT chk_rh_new_due');
        DB::statement('ALTER TABLE replacement_history ADD CONSTRAINT chk_rh_new_due CHECK (new_due_date > old_due_date)');

        Schema::table('replacement_history', function (Blueprint $table) {
            $table->dropColumn('frequency_value');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('approval_level');
        });
    }
};