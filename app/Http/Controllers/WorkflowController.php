<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditRequest;
use App\Models\CreditStatus;
use App\Models\CreditProduct;
use App\Models\CreditSubproduct;
use App\Models\CreditModality;
use App\Models\Agency;
use App\Models\State;
use App\Models\Customer;
use App\Models\CreditRequestHistory;
use App\Services\WorkflowService;
use App\Services\MetricsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    /**
     * Muestra el formulario para crear/aperturar una nueva solicitud de crédito.
     */
    public function create()
    {
        $products = CreditProduct::where('is_active', true)->with('subproducts')->get();
        $subproducts = CreditSubproduct::where('is_active', true)->get();
        $modalities = CreditModality::where('is_active', true)->get();
        $agencies = Agency::where('is_active', true)->with('state')->get();
        $states = State::orderBy('name')->get();
        $statuses = CreditStatus::whereIn('id', [1, 41, 46])->get();

        // Código sugerido de tracking correlativo
        $year = date('Y');
        $lastRequest = CreditRequest::where('request_code', 'like', "SOL-{$year}-%")
            ->orderByDesc('id')
            ->first();
        $nextNum = 101;
        if ($lastRequest && preg_match("/SOL-{$year}-(\d+)/", $lastRequest->request_code, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        }
        $suggestedCode = sprintf("SOL-%s-%06d", $year, $nextNum);

        return view('workflow.create', compact(
            'products',
            'subproducts',
            'modalities',
            'agencies',
            'states',
            'statuses',
            'suggestedCode'
        ));
    }

    /**
     * Guarda la nueva solicitud y registra su trazabilidad inicial inmutable.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Datos del solicitante / cliente
            'document_type' => 'required|in:V,J,E,G',
            'document_number' => 'required|string|max:20',
            'customer_type' => 'required|in:NATURAL,JURIDICO',
            'business_name' => 'required|string|max:200',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'state_id' => 'required|exists:states,id',

            // Datos del crédito
            'product_id' => 'required|exists:credit_products,id',
            'subproduct_id' => 'nullable|exists:credit_subproducts,id',
            'modality_id' => 'nullable|exists:credit_modalities,id',
            'agency_id' => 'required|exists:agencies,id',
            'requested_amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:VES,USD',
            'exchange_rate' => 'nullable|numeric|min:0.0001',
            'destination' => 'nullable|string|max:150',
            'public_entity' => 'nullable|boolean',
            'initial_status_id' => 'required|exists:credit_statuses,id',
            'observations' => 'nullable|string|max:1000',
            'physical_checklists' => 'nullable|array',
        ]);

        $user = Auth::user();

        return DB::transaction(function () use ($validated, $user, $request) {
            // 1. Crear o actualizar datos del cliente
            $customer = Customer::updateOrCreate(
                [
                    'document_type' => $validated['document_type'],
                    'document_number' => $validated['document_number'],
                ],
                [
                    'customer_type' => $validated['customer_type'],
                    'business_name' => $validated['business_name'],
                    'email' => $validated['email'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'state_id' => $validated['state_id'],
                    'agency_id' => $validated['agency_id'],
                ]
            );

            // 2. Generar código correlativo de seguimiento
            $year = date('Y');
            $last = CreditRequest::where('request_code', 'like', "SOL-{$year}-%")
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();
            $next = 101;
            if ($last && preg_match("/SOL-{$year}-(\d+)/", $last->request_code, $matches)) {
                $next = intval($matches[1]) + 1;
            }
            $requestCode = sprintf("SOL-%s-%06d", $year, $next);

            // 3. Tasa y cálculo de UVC
            $rate = !empty($validated['exchange_rate']) ? (float)$validated['exchange_rate'] : 36.50;
            $amount = (float)$validated['requested_amount'];
            $uvc = $rate > 0 ? round($amount / $rate, 4) : $amount;

            // 4. Crear solicitud de crédito en BD
            $statusId = (int)$validated['initial_status_id'];
            $creditRequest = CreditRequest::create([
                'request_code' => $requestCode,
                'customer_id' => $customer->id,
                'product_id' => $validated['product_id'],
                'subproduct_id' => $validated['subproduct_id'] ?? null,
                'modality_id' => $validated['modality_id'] ?? null,
                'agency_id' => $validated['agency_id'],
                'current_status_id' => $statusId,
                'assigned_user_id' => $user->id,
                'registered_by_user_id' => $user->id,
                'requested_amount' => $amount,
                'currency' => $validated['currency'],
                'exchange_rate' => $rate,
                'amount_uvc' => $uvc,
                'destination' => $validated['destination'] ?? 'Destino comercial / Capital de trabajo',
                'public_entity' => !empty($validated['public_entity']),
                'request_date' => now()->toDateString(),
                'observations' => $validated['observations'] ?? null,
                'is_active' => true,
            ]);

            // 5. Historial inicial inmutable de apertura
            $checklists = !empty($validated['physical_checklists']) ? implode(', ', $validated['physical_checklists']) : 'Recaudos básicos consignados';
            $historyObs = "Apertura de expediente físico en agencia. Recaudos validados: [{$checklists}]. " . ($validated['observations'] ?? '');

            CreditRequestHistory::create([
                'credit_request_id' => $creditRequest->id,
                'from_status_id' => null,
                'to_status_id' => $statusId,
                'user_id' => $user->id,
                'action_performed' => 'Apertura y registro de expediente en agencia',
                'observations' => $historyObs,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return redirect()->route('workflow.index')
                ->with('success', "¡Solicitud {$requestCode} registrada exitosamente! Expediente creado en bandeja con estatus inicial.");
        });
    }

    /**
     * Búsqueda rápida de cliente por cédula o RIF para autocompletar el formulario.
     */
    public function searchCustomer(Request $request)
    {
        $type = $request->query('type');
        $number = $request->query('number');

        if (!$type || !$number) {
            return response()->json(['found' => false]);
        }

        $customer = Customer::where('document_type', $type)
            ->where('document_number', $number)
            ->with('state')
            ->first();

        if (!$customer) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'customer' => $customer,
        ]);
    }
}
