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
        Schema::create('batch_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_file_status_id');
            $table->unsignedBigInteger('batch_file_type_id');
            $table->string('name');
            $table->text('path');
            $table->integer('total_items');
            $table->integer('total_done')->default(0);
            $table->integer('total_failed')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('batch_file_status_id')
                ->references('id')
                ->on('batch_file_statuses');

            $table->foreign('batch_file_type_id')
                ->references('id')
                ->on('batch_file_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_files');
    }
};
