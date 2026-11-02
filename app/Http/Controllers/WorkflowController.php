<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditRequest;
use App\Models\CreditStatus;
use App\Services\WorkflowService;
use App\Services\MetricsService;
use Illuminate\Support\Facades\Auth;

class WorkflowController extends Controller
{
    protected WorkflowService $workflowService;
    protected MetricsService $metricsService;

    public function __construct(WorkflowService $workflowService, MetricsService $metricsService)
    {
        $this->workflowService = $workflowService;
        $this->metricsService = $metricsService;
    }

    /**
     * Bandejas de seguimiento operativo (Carterizados, PYMES, Consumo, Comité, VP).
     */
    public function index(Request $request, ?string $queue = 'carterizados')
    {
        $filters = $request->only(['search', 'status_id', 'date_from', 'date_to']);
        $filters['queue'] = $queue;

        $requests = $this->metricsService->getOperationalTable($filters, 15);
        $statuses = CreditStatus::orderBy('id')->get();

        $queueTitles = [
            'carterizados' => 'Bandeja de Clientes Carterizados (GG Carterizados)',
            'pyme' => 'Bandeja Personas y PYMES',
            'comite' => 'Bandeja de Comité de Crédito',
            'vp' => 'Bandeja de Vicepresidencia de Crédito',
            'aprobados' => 'Bandeja de Solicitudes Aprobadas y Liquidación',
            'todas' => 'Bandeja Consolidada General',
        ];

        $currentQueueTitle = $queueTitles[$queue] ?? 'Bandeja de Operaciones';

        return view('workflow.index', compact('requests', 'statuses', 'queue', 'currentQueueTitle', 'filters'));
    }

    /**
     * Devuelve los datos detallados de una solicitud con historial y acciones permitidas para el modal.
     */
    public function details(Request $request, int $id)
    {
        $creditRequest = CreditRequest::with([
            'customer.state.region',
            'product.subproducts',
            'subproduct',
            'modality',
            'agency.state',
            'currentStatus',
            'assignedUser.role',
            'registeredByUser',
            'histories.fromStatus',
            'histories.toStatus',
            'histories.user.role',
            'legalDocuments',
        ])->findOrFail($id);

        $currentUser = Auth::user();
        $transitions = $this->workflowService->getAvailableTransitions($creditRequest, $currentUser);

        return response()->json([
            'request' => $creditRequest,
            'gerencia_general' => $creditRequest->gerencia_general,
            'transitions' => $transitions,
        ]);
    }

    /**
     * Ejecuta una transición individual de estatus.
     */
    public function transition(Request $request)
    {
        $request->validate([
            'credit_request_id' => 'required|exists:credit_requests,id',
            'to_status_id' => 'required|exists:credit_statuses,id',
            'observation' => 'nullable|string|max:1000',
        ]);

        $creditRequest = CreditRequest::findOrFail($request->credit_request_id);
        $user = Auth::user();

        try {
            $updated = $this->workflowService->transition(
                $creditRequest,
                (int) $request->to_status_id,
                $user,
                $request->observation,
                $request->ip(),
                $request->userAgent()
            );

            return response()->json([
                'success' => true,
                'message' => "Estado actualizado exitosamente a: {$updated->currentStatus->name}",
                'new_status' => $updated->currentStatus,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la transición: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ejecuta una transición masiva (Recibir Masivo o Cambiar Estatus Masivo).
     */
    public function batchTransition(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:credit_requests,id',
            'to_status_id' => 'required|exists:credit_statuses,id',
            'observation' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        $result = $this->workflowService->batchTransition(
            $request->ids,
            (int) $request->to_status_id,
            $user,
            $request->observation ?? 'Acción masiva ejecutada desde bandeja de seguimiento.',
            $request->ip(),
            $request->userAgent()
        );

        return response()->json($result);
    }
}
