<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code', 30)->unique(); // Ej: 'SOL-2026-000101'
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('credit_products')->cascadeOnDelete();
            $table->foreignId('subproduct_id')->nullable()->constrained('credit_subproducts')->nullOnDelete();
            $table->foreignId('modality_id')->nullable()->constrained('credit_modalities')->nullOnDelete();
            $table->foreignId('agency_id')->constrained('agencies')->cascadeOnDelete();
            $table->foreignId('current_status_id')->constrained('credit_statuses')->cascadeOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('registered_by_user_id')->constrained('users')->cascadeOnDelete();

            $table->decimal('requested_amount', 18, 2);
            $table->decimal('approved_amount', 18, 2)->nullable();
            $table->string('currency', 10)->default('VES'); // VES, USD, UVC
            $table->decimal('exchange_rate', 18, 4)->default(1.0000);
            $table->decimal('amount_uvc', 18, 4)->nullable();

            $table->string('destination', 150)->nullable();
            $table->boolean('public_entity')->default(false);
            $table->date('request_date');
            $table->date('decision_date')->nullable();
            $table->text('observations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['agency_id', 'current_status_id']);
            $table->index(['product_id', 'current_status_id']);
        });

        Schema::create('credit_request_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_request_id')->constrained('credit_requests')->cascadeOnDelete();
            $table->foreignId('from_status_id')->nullable()->constrained('credit_statuses')->nullOnDelete();
            $table->foreignId('to_status_id')->constrained('credit_statuses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action_performed', 100);
            $table->text('observations')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['credit_request_id', 'created_at']);
        });

        Schema::create('credit_legal_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_request_id')->constrained('credit_requests')->cascadeOnDelete();
            $table->string('document_name', 150);
            $table->string('file_path', 255);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->foreignId('uploaded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_legal_documents');
        Schema::dropIfExists('credit_request_histories');
        Schema::dropIfExists('credit_requests');
    }
};
