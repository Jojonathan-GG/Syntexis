<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pipeline_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pipeline_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('running');
            $table->unsignedInteger('rows_processed')->default(0);
            $table->unsignedInteger('rows_failed')->default(0);
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['pipeline_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipeline_runs');
    }
};
