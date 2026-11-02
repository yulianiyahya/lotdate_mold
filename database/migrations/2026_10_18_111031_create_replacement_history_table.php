<?php

use Illuminate\Support\Facades\DB;
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
        Schema::create('replacement_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mold_id')->index('idx_rh_mold_id');
            $table->date('old_due_date');
            $table->date('new_due_date');
            $table->date('replacement_date');
            $table->string('before_photo')->nullable();
            $table->string('after_photo')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index('idx_rh_status');
            $table->unsignedBigInteger('approved_by')->nullable()->index('idx_rh_approved_by');
            $table->dateTime('approved_at')->nullable();
            $table->text('reject_reason')->nullable();
            $table->unsignedBigInteger('changed_by')->index('idx_rh_changed_by');
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
        DB::statement('ALTER TABLE replacement_history ADD CONSTRAINT chk_rh_new_due CHECK (new_due_date > old_due_date)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('replacement_history');
    }
};
