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
        Schema::table('customers', function (Blueprint $table) {
            $table->date('birth_or_constitution_date')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('economic_activity', 255)->nullable();
            $table->string('economic_activity_code', 50)->nullable();
            $table->string('phone_mobile', 50)->nullable();
            $table->string('phone_alt', 50)->nullable();
        });

        Schema::table('credit_requests', function (Blueprint $table) {
            $table->string('public_entity_name', 150)->nullable();
            // Garantías
            $table->boolean('has_guarantee')->default(false);
            $table->string('guarantee_type', 50)->nullable();
            $table->string('guarantor_doc_type', 5)->nullable();
            $table->string('guarantor_doc_number', 30)->nullable();
            $table->string('guarantor_name', 200)->nullable();
            $table->string('guarantor_phone', 50)->nullable();
            $table->string('guarantor_email', 150)->nullable();
            // Datos de Vinculación del cliente
            $table->boolean('has_exchange_intervention')->default(false);
            $table->boolean('pays_taxes')->default(false);
            $table->boolean('has_credit_card')->default(false);
            $table->boolean('has_foreign_currency_account')->default(false);
            $table->decimal('average_balance', 18, 2)->default(0);
            $table->boolean('has_pos')->default(false);
            $table->boolean('has_payroll')->default(false);
            $table->json('physical_checklists')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'birth_or_constitution_date',
                'gender',
                'economic_activity',
                'economic_activity_code',
                'phone_mobile',
                'phone_alt',
            ]);
        });

        Schema::table('credit_requests', function (Blueprint $table) {
            $table->dropColumn([
                'public_entity_name',
                'has_guarantee',
                'guarantee_type',
                'guarantor_doc_type',
                'guarantor_doc_number',
                'guarantor_name',
                'guarantor_phone',
                'guarantor_email',
                'has_exchange_intervention',
                'pays_taxes',
                'has_credit_card',
                'has_foreign_currency_account',
                'average_balance',
                'has_pos',
                'has_payroll',
                'physical_checklists',
            ]);
        });
    }
};
