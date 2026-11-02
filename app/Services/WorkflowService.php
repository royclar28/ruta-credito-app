<?php

namespace App\Services;

use App\Models\CreditRequest;
use App\Models\CreditRequestHistory;
use App\Models\CreditStatus;
use App\Models\WorkflowTransition;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class WorkflowService
{
    /**
     * Obtiene las transiciones y acciones disponibles para una solicitud según el rol del usuario.
     */
    public function getAvailableTransitions(CreditRequest $request, ?User $user = null): Collection
    {
        $query = WorkflowTransition::with('toStatus')
            ->where('from_status_id', $request->current_status_id);

        if ($user && $user->role && !$user->hasRole('admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('allowed_role_id', $user->role_id)
                  ->orWhereNull('allowed_role_id');
            });
        }

        return $query->get();
    }

    /**
     * Ejecuta una transición de estatus de forma atómica con registro de auditoría.
     */
    public function transition(
        CreditRequest $request,
        int $toStatusId,
        User $user,
        ?string $observation = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): CreditRequest {
        $toStatus = CreditStatus::findOrFail($toStatusId);
        $fromStatusId = $request->current_status_id;

        return DB::transaction(function () use ($request, $toStatus, $fromStatusId, $user, $observation, $ip, $userAgent) {
            // Actualizar estado de la solicitud
            $request->current_status_id = $toStatus->id;

            // Si es un estado de aprobación final
            if ($toStatus->code === 'APROBADO_VP') {
                $request->approved_amount = $request->requested_amount;
                $request->decision_date = now()->toDateString();
            }

            $request->save();

            // Registrar trazabilidad inmutable
            CreditRequestHistory::create([
                'credit_request_id' => $request->id,
                'from_status_id' => $fromStatusId,
                'to_status_id' => $toStatus->id,
                'user_id' => $user->id,
                'action_performed' => 'Transición a: ' . $toStatus->name,
                'observations' => $observation ?? 'Transición de workflow ejecutada.',
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'created_at' => now(),
            ]);

            return $request->fresh(['currentStatus', 'customer', 'product', 'agency']);
        });
    }

    /**
     * Ejecución masiva de transiciones (para bandejas de trabajo: Recibir Masivo / Avanzar Masivo).
     */
    public function batchTransition(
        array $requestIds,
        int $toStatusId,
        User $user,
        ?string $observation = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): array {
        $processed = 0;
        $failed = 0;
        $errors = [];

        foreach ($requestIds as $id) {
            try {
                $req = CreditRequest::findOrFail($id);
                $this->transition($req, $toStatusId, $user, $observation, $ip, $userAgent);
                $processed++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = "Error en solicitud #{$id}: " . $e->getMessage();
            }
        }

        return [
            'success' => $failed === 0,
            'processed' => $processed,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }
}
