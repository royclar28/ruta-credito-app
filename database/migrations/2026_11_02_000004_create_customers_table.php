<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->char('document_type', 1)->default('V'); // V, E, J, G, F
            $table->string('document_number', 20); // Número de cédula o RIF
            $table->string('customer_type', 30)->default('NATURAL'); // NATURAL, JURIDICO, FIRMA_PERSONAL
            $table->string('business_name', 150); // Razón social o Nombre Completo
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->nullOnDelete();
            $table->timestamps();

            $table->unique(['document_type', 'document_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
