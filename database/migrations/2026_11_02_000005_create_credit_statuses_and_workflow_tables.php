<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 120);
            $table->string('stage', 40); // RECEPCION, ANALISIS, COMITE, VP, LIQUIDACION, ARCHIVADO, DEVUELTO, DIFERIDO, RECHAZADO
            $table->string('badge_color', 30)->default('blue');
            $table->boolean('is_terminal')->default(false);
            $table->timestamps();
        });

        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_status_id')->constrained('credit_statuses')->cascadeOnDelete();
            $table->foreignId('to_status_id')->constrained('credit_statuses')->cascadeOnDelete();
            $table->foreignId('allowed_role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->string('action_name', 100);
            $table->string('action_type', 30)->default('AVANZAR'); // RECIBIR, AVANZAR, DEVOLVER, DIFERIR, APROBAR, NEGAR, LIQUIDAR
            $table->string('action_color', 30)->default('primary'); // primary, success, warning, danger, info
            $table->string('icon', 50)->default('arrow-right');
            $table->boolean('requires_observation')->default(true);
            $table->boolean('is_batch_allowed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_transitions');
        Schema::dropIfExists('credit_statuses');
    }
};
