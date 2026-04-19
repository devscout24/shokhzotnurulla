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
        Schema::create('incentives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->string('title', 150);
            $table->enum('type', ['cash', 'finance', 'ivc_dvc', 'lease', 'percentage_off']);
            $table->enum('category', ['all', 'used', 'new', 'cpo'])->default('all');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->enum('amount_type', ['fixed', 'percent'])->nullable();
            $table->string('program_code', 100)->nullable();
            $table->boolean('is_guaranteed')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->date('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);

            $table->index(['dealer_id', 'is_enabled']);
            $table->index(['dealer_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incentives');
    }
};
