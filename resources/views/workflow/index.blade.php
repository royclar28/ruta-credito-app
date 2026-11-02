@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="workflowManager()">
    
    <!-- Header de la Bandeja -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">{{ $currentQueueTitle }}</h2>
            <p class="text-xs text-slate-500 mt-1">Gestión operativa, transiciones de workflow y acciones masivas.</p>
        </div>

        <!-- Pestañas de Bandejas -->
        <div class="flex items-center gap-1.5 p-1 bg-white border border-slate-200/80 rounded-2xl shadow-sm text-xs font-semibold overflow-x-auto max-w-full">
            <a href="{{ route('workflow.index', 'carterizados') }}" 
               class="px-3 py-1.5 rounded-xl transition-all {{ $queue === 'carterizados' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Carterizados
            </a>
            <a href="{{ route('workflow.index', 'pyme') }}" 
               class="px-3 py-1.5 rounded-xl transition-all {{ $queue === 'pyme' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                PYME
            </a>
            <a href="{{ route('workflow.index', 'comite') }}" 
               class="px-3 py-1.5 rounded-xl transition-all {{ $queue === 'comite' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Comité
            </a>
            <a href="{{ route('workflow.index', 'vp') }}" 
               class="px-3 py-1.5 rounded-xl transition-all {{ $queue === 'vp' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                VP
            </a>
            <a href="{{ route('workflow.index', 'aprobados') }}" 
               class="px-3 py-1.5 rounded-xl transition-all {{ $queue === 'aprobados' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Aprobados
            </a>
            <a href="{{ route('workflow.index', 'todas') }}" 
               class="px-3 py-1.5 rounded-xl transition-all {{ $queue === 'todas' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Todas
            </a>
        </div>
    </div>

    <!-- Barra Flotante de Acciones Masivas -->
    <div x-show="selectedIds.length > 0" x-cloak
         class="p-4 rounded-2xl bg-slate-900 text-white shadow-xl flex items-center justify-between animate-in slide-in-from-top duration-200">
        <div class="flex items-center gap-3">
            <span class="w-7 h-7 rounded-lg bg-brand-600 text-white font-bold text-xs flex items-center justify-center" x-text="selectedIds.length"></span>
            <span class="text-xs font-medium text-slate-200">solicitud(es) seleccionada(s) para procesamiento masivo</span>
        </div>

        <div class="flex items-center gap-3">
            <!-- Botón Recibir Masivo (a Estatus 57) -->
            <button @click="executeBatchTransition(57, 'Recepción masiva confirmada')" 
                    class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm transition-all flex items-center gap-2">
                <i data-lucide="inbox" class="w-4 h-4"></i>
                <span>Recibir Masivo</span>
            </button>

            <!-- Botón Avanzar a Comité (a Estatus 58) -->
            <button @click="executeBatchTransition(58, 'Aprobación masiva y remisión a comité')" 
                    class="px-3.5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-semibold shadow-sm transition-all flex items-center gap-2">
                <i data-lucide="send" class="w-4 h-4"></i>
                <span>Enviar a Comité Masivo</span>
            </button>

            <button @click="selectedIds = []" class="text-xs text-slate-400 hover:text-white px-2 py-1">
                Desmarcar
            </button>
        </div>
    </div>

    <!-- Tabla de Bandeja -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <th class="py-3.5 px-4 text-center w-10">
                            <input type="checkbox" @change="toggleSelectAll($event)" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        </th>
                        <th class="py-3.5 px-4">N° Solicitud</th>
                        <th class="py-3.5 px-4">Cliente / RIF</th>
                        <th class="py-3.5 px-4">Gerencia General</th>
                        <th class="py-3.5 px-4">Producto / Modalidad</th>
                        <th class="py-3.5 px-4">Agencia</th>
                        <th class="py-3.5 px-4 text-right">Monto</th>
                        <th class="py-3.5 px-4">Estatus Actual</th>
                        <th class="py-3.5 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-4 text-center">
                                <input type="checkbox" value="{{ $req->id }}" x-model="selectedIds" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            </td>
                            <td class="py-4 px-4 font-mono font-bold text-brand-600">
                                {{ $req->request_code }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-900 block">{{ $req->customer?->business_name }}</span>
                                <span class="text-[11px] text-slate-400">{{ $req->customer?->formatted_document }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $req->gerencia_general }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-medium text-slate-800 block">{{ $req->product?->name }}</span>
                                <span class="text-[11px] text-slate-400">{{ $req->modality?->name ?? 'Estándar' }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-slate-800 block">{{ $req->agency?->name }}</span>
                                <span class="text-[11px] text-slate-400">{{ $req->agency?->state?->name }}</span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <span class="font-bold text-slate-900 block">${{ number_format($req->requested_amount, 2) }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">{{ number_format($req->amount_uvc, 2) }} UVC</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-{{ $req->currentStatus?->badge_color }}-50 text-{{ $req->currentStatus?->badge_color }}-700 border border-{{ $req->currentStatus?->badge_color }}-200 inline-block">
                                    {{ $req->currentStatus?->name }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <button @click="openModal({{ $req->id }})" 
                                        class="px-3 py-1.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs shadow-sm shadow-brand-500/20 transition-all flex items-center gap-1.5 mx-auto">
                                    <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                                    <span>Gestionar</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400 text-xs">
                                No hay expedientes pendientes en esta bandeja operativa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL DE GESTIÓN CON STEP WIZARD Y TRANSICIÓN DE ESTADOS -->
    <!-- ========================================================================= -->
    <div x-show="modalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div class="bg-white rounded-3xl max-w-4xl w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <!-- Modal Header -->
            <div class="px-6 py-5 bg-gradient-to-r from-slate-900 to-brand-900 text-white flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold tracking-wider uppercase text-brand-300 block">Gestión de Expediente</span>
                    <h3 class="text-lg font-bold" x-text="selectedRequest?.request_code"></h3>
                </div>
                <button @click="modalOpen = false" class="text-white/80 hover:text-white p-2 rounded-lg hover:bg-white/10 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- STEP WIZARD DE ETAPAS BANCARIAS -->
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 overflow-x-auto">
                <div class="flex items-center justify-between min-w-[550px]">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center shadow">1</span>
                        <span class="text-xs font-semibold text-slate-700">Recepción</span>
                    </div>
                    <div class="h-0.5 flex-1 bg-slate-200 mx-3"></div>

                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center shadow">2</span>
                        <span class="text-xs font-semibold text-slate-700">Análisis</span>
                    </div>
                    <div class="h-0.5 flex-1 bg-slate-200 mx-3"></div>

                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center shadow">3</span>
                        <span class="text-xs font-semibold text-slate-700">Comité</span>
                    </div>
                    <div class="h-0.5 flex-1 bg-slate-200 mx-3"></div>

                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center shadow">4</span>
                        <span class="text-xs font-semibold text-slate-700">VP</span>
                    </div>
                    <div class="h-0.5 flex-1 bg-slate-200 mx-3"></div>

                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shadow">5</span>
                        <span class="text-xs font-semibold text-slate-700">Liquidación</span>
                    </div>
                </div>
            </div>

            <!-- Contenido Modal -->
            <div class="p-6 max-h-[65vh] overflow-y-auto space-y-6">
                <!-- Estado y Acciones Rápidas Disponibles -->
                <div class="p-5 rounded-2xl bg-gradient-to-br from-brand-50/60 to-indigo-50/40 border border-brand-100">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Estatus Actual</span>
                            <span class="text-sm font-extrabold text-brand-900" x-text="selectedRequest?.current_status?.name"></span>
                        </div>
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-white border border-brand-200 text-brand-700" x-text="selectedGerencia"></span>
                    </div>

                    <!-- Botones de Transición Disponibles para este Rol -->
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Acciones de Workflow Permitidas:</span>
                        <div class="flex flex-wrap gap-2.5">
                            <template x-for="t in availableTransitions" :key="t.id">
                                <button type="button" 
                                        @click="triggerTransition(t.to_status_id, t.action_name)"
                                        class="px-4 py-2 rounded-xl text-xs font-bold text-white shadow-md transition-all flex items-center gap-2"
                                        :class="{
                                            'bg-emerald-600 hover:bg-emerald-500': t.action_color === 'success',
                                            'bg-brand-600 hover:bg-brand-500': t.action_color === 'primary',
                                            'bg-indigo-600 hover:bg-indigo-500': t.action_color === 'info',
                                            'bg-amber-600 hover:bg-amber-500': t.action_color === 'warning',
                                            'bg-rose-600 hover:bg-rose-500': t.action_color === 'danger',
                                        }">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    <span x-text="t.action_name"></span>
                                </button>
                            </template>
                            <span x-show="availableTransitions.length === 0" class="text-xs text-slate-400 italic">
                                No hay transiciones disponibles para tu rol en este estado.
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Datos del Cliente y Financieros -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-white border border-slate-200">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Datos del Solicitante</span>
                        <p class="text-sm font-bold text-slate-900" x-text="selectedRequest?.customer?.business_name"></p>
                        <p class="text-xs text-slate-600 mt-1" x-text="'Documento: ' + selectedRequest?.customer?.document_type + '-' + selectedRequest?.customer?.document_number"></p>
                        <p class="text-xs text-slate-600" x-text="'Email: ' + (selectedRequest?.customer?.email || 'N/A')"></p>
                        <p class="text-xs text-slate-600" x-text="'Teléfono: ' + (selectedRequest?.customer?.phone || 'N/A')"></p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white border border-slate-200">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Detalle del Crédito</span>
                        <p class="text-xs text-slate-600"><strong class="text-slate-800">Producto:</strong> <span x-text="selectedRequest?.product?.name"></span></p>
                        <p class="text-xs text-slate-600"><strong class="text-slate-800">Modalidad:</strong> <span x-text="selectedRequest?.modality?.name || 'Estándar'"></span></p>
                        <p class="text-xs text-slate-600"><strong class="text-slate-800">Destino:</strong> <span x-text="selectedRequest?.destination || 'N/A'"></span></p>
                        <p class="text-xs text-slate-600"><strong class="text-slate-800">Agencia:</strong> <span x-text="selectedRequest?.agency?.name"></span></p>
                    </div>
                </div>

                <!-- Historial Cronológico de Auditoría -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">Trazabilidad Cronológica</h4>
                    <div class="space-y-2.5 max-h-48 overflow-y-auto pr-1">
                        <template x-for="h in selectedRequest?.histories" :key="h.id">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-slate-900" x-text="h.action_performed"></span>
                                    <span class="text-[11px] text-slate-400" x-text="new Date(h.created_at).toLocaleString()"></span>
                                </div>
                                <p class="text-slate-600" x-text="h.observations"></p>
                                <span class="text-[10px] text-slate-400 mt-1 block" x-text="'Usuario: ' + (h.user?.name || 'Sistema') + ' (' + (h.user?.role?.name || '') + ')'"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
                <a :href="'/reportes/expediente/' + selectedRequest?.id" target="_blank"
                   class="text-xs font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1.5">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Imprimir Expediente</span>
                </a>
                <button @click="modalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function workflowManager() {
        return {
            selectedIds: [],
            modalOpen: false,
            selectedRequest: null,
            selectedGerencia: '',
            availableTransitions: [],

            toggleSelectAll(e) {
                if (e.target.checked) {
                    this.selectedIds = @json($requests->pluck('id')->toArray());
                } else {
                    this.selectedIds = [];
                }
            },

            openModal(requestId) {
                fetch(`/solicitudes/${requestId}/detalles`)
                    .then(r => r.json())
                    .then(data => {
                        this.selectedRequest = data.request;
                        this.selectedGerencia = data.gerencia_general;
                        this.availableTransitions = data.transitions;
                        this.modalOpen = true;
                        this.$nextTick(() => lucide.createIcons());
                    })
                    .catch(err => {
                        Swal.fire('Error', 'No se pudieron cargar los datos: ' + err.message, 'error');
                    });
            },

            triggerTransition(toStatusId, actionName) {
                Swal.fire({
                    title: actionName,
                    text: 'Ingresa una observación para registrar en la auditoría inmutable:',
                    input: 'textarea',
                    inputPlaceholder: 'Escribe las observaciones de la decisión...',
                    showCancelButton: true,
                    confirmButtonText: 'Confirmar y Ejecutar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#2563eb',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Es obligatorio ingresar una observación';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("workflow.transition") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                credit_request_id: this.selectedRequest.id,
                                to_status_id: toStatusId,
                                observation: result.value
                            })
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success) {
                                Swal.fire('¡Éxito!', res.message, 'success').then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        })
                        .catch(err => {
                            Swal.fire('Error', 'Fallo al procesar: ' + err.message, 'error');
                        });
                    }
                });
            },

            executeBatchTransition(toStatusId, defaultObs) {
                Swal.fire({
                    title: '¿Confirmar acción masiva?',
                    text: `Se aplicará la transición a ${this.selectedIds.length} solicitudes.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, procesar lote',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#2563eb'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("workflow.batch_transition") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                ids: this.selectedIds,
                                to_status_id: toStatusId,
                                observation: defaultObs
                            })
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success) {
                                Swal.fire('¡Lote Procesado!', `Se actualizaron ${res.processed} solicitudes correctamente.`, 'success').then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Atención', `Procesadas: ${res.processed}, Fallidas: ${res.failed}`, 'warning').then(() => {
                                    window.location.reload();
                                });
                            }
                        })
                        .catch(err => {
                            Swal.fire('Error', 'Fallo de red: ' + err.message, 'error');
                        });
                    }
                });
            }
        };
    }
</script>
@endpush
@endsection
