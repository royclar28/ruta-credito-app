@extends('layouts.app')

@section('content')
<div class="space-y-8" x-data="dashboardManager()">
    
    <!-- ========================================================================= -->
    <!-- 1. BARRA DE FILTROS TERRITORIALES Y DE PRODUCTO (CASCADA) -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <i data-lucide="sliders-horizontal" class="w-4 h-4 text-brand-600"></i>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Filtros Territoriales y Operativos</h3>
            </div>
            <a href="{{ route('dashboard') }}" class="text-xs text-slate-400 hover:text-brand-600 flex items-center gap-1 font-medium transition-colors">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                <span>Restablecer</span>
            </a>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Región -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Región Territorial</label>
                <select name="region_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Todas las Regiones</option>
                    @foreach($regions as $reg)
                        <option value="{{ $reg->id }}" {{ request('region_id') == $reg->id ? 'selected' : '' }}>{{ $reg->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Estado Geográfico</label>
                <select name="state_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Todos los Estados</option>
                    @foreach($states as $st)
                        <option value="{{ $st->id }}" {{ request('state_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Producto -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Línea de Producto</label>
                <select name="product_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Todos los Productos</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Estatus -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Fase / Estatus</label>
                <select name="status_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Todos los Estatus</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->id }}" {{ request('status_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. SCORECARDS EJECUTIVOS (KPIs PRINCIPALES) -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Solicitudes -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Solicitudes</span>
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($scorecards['total_solicitudes']) }}</span>
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">100% Auditado</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Monto Total: <strong class="text-slate-800 font-bold">${{ number_format($scorecards['total_monto_usd'], 2) }}</strong></p>
        </div>

        <!-- Card 2: Monto UVC Consolidado -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Monto UVC Total</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="coins" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($scorecards['total_monto_uvc'], 2) }}</span>
                <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">UVC</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Indexación cambiaria oficial BDT</p>
        </div>

        <!-- Card 3: Aprobados / Tasa de Éxito -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Aprobados / Liquidación</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-emerald-600 tracking-tight">{{ number_format($scorecards['aprobados_count']) }}</span>
                <span class="text-xs font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">{{ $scorecards['tasa_aprobacion'] }}% Eficacia</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Monto Aprobado: <strong class="text-slate-800 font-bold">${{ number_format($scorecards['aprobados_monto'], 2) }}</strong></p>
        </div>

        <!-- Card 4: En Análisis / Comité -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">En Flujo Operativo</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-amber-600 tracking-tight">{{ number_format($scorecards['en_analisis_count'] + $scorecards['en_comite_count']) }}</span>
                <span class="text-xs font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">Activas</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">{{ $scorecards['en_analisis_count'] }} en Análisis &bull; {{ $scorecards['en_comite_count'] }} en Comité</p>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. GRÁFICOS INTERACTIVOS (APEXCHARTS) -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Gráfico 1: Estatus de Solicitudes (Donut) -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">Distribución por Estatus</h4>
                <p class="text-xs text-slate-500 mb-4">Proporción de créditos según etapa en el flujo</p>
            </div>
            <div id="statusChart" class="flex items-center justify-center min-h-[280px]"></div>
        </div>

        <!-- Gráfico 2: Monto por Producto (Bar) -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between lg:col-span-2">
            <div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">Volumen Financiero por Línea de Crédito</h4>
                <p class="text-xs text-slate-500 mb-4">Monto total solicitado (USD) por tipo de producto</p>
            </div>
            <div id="productChart" class="min-h-[280px]"></div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. TABLA OPERATIVA DE SEGUIMIENTO EN TIEMPO REAL -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Seguimiento Operativo Consolidado</h3>
                <p class="text-xs text-slate-500 mt-0.5">Expedientes activos con clasificación canónica de Gerencia General</p>
            </div>

            <!-- Búsqueda rápida -->
            <form method="GET" action="{{ route('dashboard') }}" class="w-full sm:w-72">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar N° de solicitud o cliente..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <th class="py-3.5 px-6">N° Solicitud</th>
                        <th class="py-3.5 px-6">Cliente / Solicitante</th>
                        <th class="py-3.5 px-6">Gerencia General</th>
                        <th class="py-3.5 px-6">Producto</th>
                        <th class="py-3.5 px-6">Agencia</th>
                        <th class="py-3.5 px-6 text-right">Monto</th>
                        <th class="py-3.5 px-6">Estatus Actual</th>
                        <th class="py-3.5 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-brand-600">
                                {{ $req->request_code }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block">{{ $req->customer?->business_name }}</span>
                                <span class="text-[11px] text-slate-400">{{ $req->customer?->formatted_document }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $req->gerencia_general }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-medium text-slate-800 block">{{ $req->product?->name }}</span>
                                <span class="text-[11px] text-slate-400">{{ $req->subproduct?->name ?? 'Estándar' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-slate-800 block">{{ $req->agency?->name }}</span>
                                <span class="text-[11px] text-slate-400">{{ $req->agency?->state?->name }}</span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <span class="font-bold text-slate-900 block">${{ number_format($req->requested_amount, 2) }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">{{ number_format($req->amount_uvc, 2) }} UVC</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-{{ $req->currentStatus?->badge_color }}-50 text-{{ $req->currentStatus?->badge_color }}-700 border border-{{ $req->currentStatus?->badge_color }}-200 inline-block">
                                    {{ $req->currentStatus?->name }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button @click="openDetails({{ $req->id }})" 
                                        class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-brand-600 hover:text-white text-slate-700 font-medium text-xs transition-all shadow-sm flex items-center gap-1 mx-auto">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    <span>Detalles</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                No se encontraron solicitudes que coincidan con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($requests->hasPages())
            <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- 5. MODAL DE AUDITORÍA Y DETALLES CON STEP WIZARD -->
    <!-- ========================================================================= -->
    <div x-show="modalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div class="bg-white rounded-3xl max-w-4xl w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <!-- Modal Header -->
            <div class="px-6 py-5 bg-gradient-to-r from-brand-700 to-indigo-700 text-white flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold tracking-wider uppercase text-brand-200 block">Expediente de Crédito</span>
                    <h3 class="text-lg font-bold" x-text="selectedRequest?.request_code"></h3>
                </div>
                <button @click="modalOpen = false" class="text-white/80 hover:text-white p-2 rounded-lg hover:bg-white/10 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 max-h-[75vh] overflow-y-auto space-y-6">
                <!-- Información General Card -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Cliente</span>
                        <span class="text-sm font-bold text-slate-900 block" x-text="selectedRequest?.customer?.business_name"></span>
                        <span class="text-xs text-slate-500" x-text="selectedRequest?.customer?.document_type + '-' + selectedRequest?.customer?.document_number"></span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Gerencia General</span>
                        <span class="text-xs font-bold text-brand-700 block mt-0.5" x-text="selectedGerencia"></span>
                        <span class="text-xs text-slate-500" x-text="selectedRequest?.product?.name"></span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Monto Solicitado</span>
                        <span class="text-sm font-extrabold text-slate-900 block" x-text="'$' + Number(selectedRequest?.requested_amount || 0).toLocaleString('es-VE', {minimumFractionDigits: 2})"></span>
                        <span class="text-xs text-slate-500" x-text="Number(selectedRequest?.amount_uvc || 0).toLocaleString('es-VE', {minimumFractionDigits: 2}) + ' UVC'"></span>
                    </div>
                </div>

                <!-- Trazabilidad Histórica (Timeline de Auditoría) -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-2">
                        <i data-lucide="history" class="w-4 h-4 text-brand-600"></i>
                        <span>Línea de Tiempo y Auditoría Inmutable</span>
                    </h4>

                    <div class="space-y-3 relative pl-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                        <template x-for="hist in selectedRequest?.histories" :key="hist.id">
                            <div class="relative">
                                <span class="absolute -left-6 top-1 w-3 h-3 rounded-full bg-brand-600 border-2 border-white shadow"></span>
                                <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-sm">
                                    <div class="flex items-center justify-between text-xs mb-1">
                                        <span class="font-bold text-slate-900" x-text="hist.action_performed"></span>
                                        <span class="text-[11px] text-slate-400" x-text="new Date(hist.created_at).toLocaleString()"></span>
                                    </div>
                                    <p class="text-xs text-slate-600" x-text="hist.observations"></p>
                                    <div class="mt-2 text-[10px] text-slate-400 flex items-center gap-3">
                                        <span x-text="'Operador: ' + (hist.user?.name || 'Sistema')"></span>
                                        <span x-text="'IP: ' + (hist.ip_address || '127.0.0.1')"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button @click="modalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function dashboardManager() {
        return {
            modalOpen: false,
            selectedRequest: null,
            selectedGerencia: '',

            openDetails(requestId) {
                fetch(`/solicitudes/${requestId}/detalles`)
                    .then(r => r.json())
                    .then(data => {
                        this.selectedRequest = data.request;
                        this.selectedGerencia = data.gerencia_general;
                        this.modalOpen = true;
                        this.$nextTick(() => lucide.createIcons());
                    })
                    .catch(err => {
                        Swal.fire('Error', 'No se pudieron cargar los detalles: ' + err.message, 'error');
                    });
            }
        };
    }

    // Inicializar Gráficos de ApexCharts
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Gráfico de Estatus (Donut)
        const statusData = @json($statusDistribution);
        if (document.getElementById('statusChart') && statusData.series.length > 0) {
            new ApexCharts(document.getElementById('statusChart'), {
                series: statusData.series,
                labels: statusData.labels,
                chart: { type: 'donut', height: 280, fontFamily: 'Plus Jakarta Sans, sans-serif' },
                colors: ['#3b82f6', '#6366f1', '#f59e0b', '#10b981', '#ec4899', '#8b5cf6'],
                legend: { position: 'bottom', fontSize: '11px' },
                dataLabels: { enabled: true },
                stroke: { width: 0 }
            }).render();
        }

        // 2. Gráfico de Productos (Barra)
        const productData = @json($productDistribution);
        if (document.getElementById('productChart') && productData.categories.length > 0) {
            new ApexCharts(document.getElementById('productChart'), {
                series: [{ name: 'Monto Solicitado ($)', data: productData.amounts }],
                chart: { type: 'bar', height: 280, fontFamily: 'Plus Jakarta Sans, sans-serif', toolbar: { show: false } },
                colors: ['#2563eb'],
                plotOptions: { bar: { borderRadius: 6, columnWidth: '45%' } },
                xaxis: { categories: productData.categories, labels: { style: { fontSize: '11px' } } },
                yaxis: { labels: { formatter: (val) => '$' + Number(val).toLocaleString() } },
                tooltip: { y: { formatter: (val) => '$' + Number(val).toLocaleString() } }
            }).render();
        }
    });
</script>
@endpush
@endsection
