<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dataset_id')->constrained()->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->json('data');
            $table->timestamps();

            $table->index(['dataset_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_records');
    }
};
