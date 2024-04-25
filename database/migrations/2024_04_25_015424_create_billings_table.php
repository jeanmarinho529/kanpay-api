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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_file_id');
            $table->integer('row_number');
            $table->string('name', 80);
            $table->string('government_id');
            $table->string('email');
            $table->float('debt_amount', 8, 2);
            $table->date('debt_due_date');
            $table->uuid('debt_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('batch_file_id')
                ->references('id')
                ->on('batch_files');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
