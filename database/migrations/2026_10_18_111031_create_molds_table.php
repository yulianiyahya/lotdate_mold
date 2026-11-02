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
        Schema::create('molds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('part_name', 100);
            $table->string('part_number', 100)->unique('uq_molds_part_number');
            $table->string('category', 100)->nullable();
            $table->string('location', 100)->nullable();
            $table->string('type', 100)->nullable();
            $table->string('installed_by', 100)->nullable();
            $table->string('supplier', 100)->nullable();
            $table->unsignedBigInteger('pic_id')->nullable()->index('fk_molds_pic');
            $table->unsignedInteger('frequency_value');
            $table->enum('frequency_unit', ['day', 'week', 'month', 'year']);
            $table->date('last_replaced_date')->nullable();
            $table->date('next_due_date')->index('idx_molds_next_due_date');
            $table->dateTime('reminder_sent_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('molds');
    }
};
