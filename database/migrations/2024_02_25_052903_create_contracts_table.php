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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('date')->nullable();
            $table->string("vehicle_number");
            $table->string("vehicle_name")->nullable();
            $table->string('bility')->nullable();
            $table->integer('quantity')->nullable(); // Qty
            $table->string('item')->nullable();
            $table->decimal('freight', 10, 2)->default(0);
            $table->string('purchase_status');
            $table->decimal('charge_per_day', 10,2)->default(0);
            $table->decimal('stop_charges', 10,2)->default(0);  
            $table->decimal('labour_charges', 10, 2)->default(0); 
            $table->decimal('purchase_total', 10, 2)->default(0); // SUB TOTAL
            $table->decimal('sale_total', 10, 2)->default(0); // TOTAL
            $table->float('tax_percent')->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->string('img')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
