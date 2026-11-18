<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->string('name');
            $table->string('category')->default('operations');
            $table->decimal('value', 20, 4)->default(0);
            $table->string('unit')->nullable();
            $table->decimal('change_percent', 8, 2)->default(0);
            $table->json('series')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metrics');
    }
};
