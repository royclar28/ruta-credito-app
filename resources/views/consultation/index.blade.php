@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    
    <!-- Hero / Buscador -->
    <div class="bg-gradient-to-r from-slate-900 to-brand-950 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
        <div class="max-w-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-brand-400 block mb-2">Trazabilidad y Cumplimiento</span>
            <h2 class="text-2xl font-extrabold tracking-tight mb-2">Consulta de Solicitud de Crédito</h2>
            <p class="text-xs text-slate-300 mb-6">Ingresa el número de expediente o la identificación del cliente para consultar la auditoría cronológica inmutable.</p>
        </div>

        <form method="GET" action="{{ route('consultation.index') }}" class="relative max-w-2xl">
            <div class="relative flex items-center">
                <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-4"></i>
                <input type="text" name="search" value="{{ $search }}" required
                       placeholder="Ejemplo: SOL-2026-000101 o Cédula/RIF (301234567)..."
                       class="w-full pl-12 pr-32 py-3.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400 transition-all">
                <button type="submit" 
                        class="absolute right-2 px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs transition-all shadow-md">
                    Buscar
                </button>
            </div>
        </form>
    </div>

    <!-- Si se encontró una solicitud -->
    @if($creditRequest)
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <!-- Header de Solicitud -->
            <div class="p-6 bg-slate-50 border-b border-slate-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="text-lg font-extrabold font-mono text-brand-600">{{ $creditRequest->request_code }}</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $creditRequest->currentStatus?->badge_color }}-50 text-{{ $creditRequest->currentStatus?->badge_color }}-700 border border-{{ $creditRequest->currentStatus?->badge_color }}-200">
                            {{ $creditRequest->currentStatus?->name }}
                        </span>
                    </div>
                    <span class="text-xs text-slate-500 mt-1 block">Radicada en: {{ $creditRequest->agency?->name }} &bull; Fecha: {{ $creditRequest->request_date?->format('d/m/Y') }}</span>
                </div>

                <a href="{{ route('reports.print_summary', $creditRequest->id) }}" target="_blank"
                   class="px-4 py-2 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700 shadow-sm transition-all flex items-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4 text-brand-600"></i>
                    <span>Imprimir Expediente</span>
                </a>
            </div>

            <!-- Ficha Resumen -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 border-b border-slate-100">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Cliente Solicitante</span>
                    <span class="text-sm font-bold text-slate-900 block mt-1">{{ $creditRequest->customer?->business_name }}</span>
                    <span class="text-xs text-slate-500">{{ $creditRequest->customer?->formatted_document }} ({{ $creditRequest->customer?->customer_type }})</span>
                </div>

                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Gerencia General y Producto</span>
                    <span class="text-xs font-bold text-brand-700 block mt-1">{{ $creditRequest->gerencia_general }}</span>
                    <span class="text-xs text-slate-500">{{ $creditRequest->product?->name }} - {{ $creditRequest->subproduct?->name ?? 'Estándar' }}</span>
                </div>

                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Condiciones Financieras</span>
                    <span class="text-base font-extrabold text-slate-900 block mt-1">${{ number_format($creditRequest->requested_amount, 2) }}</span>
                    <span class="text-xs text-slate-500 font-mono">{{ number_format($creditRequest->amount_uvc, 2) }} UVC</span>
                </div>
            </div>

            <!-- Línea de Tiempo de Auditoría Cronológica -->
            <div class="p-6 md:p-8">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-6 flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-brand-600"></i>
                    <span>Trazabilidad y Registro de Decisiones</span>
                </h3>

                <div class="relative pl-6 space-y-6 before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @foreach($creditRequest->histories as $idx => $hist)
                        <div class="relative">
                            <span class="absolute -left-6 top-1.5 w-3.5 h-3.5 rounded-full {{ $idx === 0 ? 'bg-brand-600 ring-4 ring-brand-100' : 'bg-slate-300' }} border-2 border-white shadow"></span>
                            
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-sm">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2">
                                    <span class="text-xs font-bold text-slate-900">{{ $hist->action_performed }}</span>
                                    <span class="text-[11px] text-slate-400 font-mono">{{ $hist->created_at->format('d/m/Y h:i A') }}</span>
                                </div>

                                <p class="text-xs text-slate-600">{{ $hist->observations }}</p>

                                <div class="mt-3 pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-400">
                                    <span>Operador: <strong class="text-slate-600">{{ $hist->user?->name ?? 'Sistema' }}</strong> ({{ $hist->user?->role?->name ?? 'N/A' }})</span>
                                    <span>IP: {{ $hist->ip_address ?? '127.0.0.1' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @elseif(!empty($search))
        <div class="p-8 rounded-3xl bg-white border border-slate-200 text-center">
            <i data-lucide="alert-circle" class="w-10 h-10 text-amber-500 mx-auto mb-3"></i>
            <h3 class="text-sm font-bold text-slate-900">No se encontró ninguna solicitud</h3>
            <p class="text-xs text-slate-500 mt-1">No existe ningún expediente con el código o identificación "{{ $search }}".</p>
        </div>
    @else
        <!-- Expedientes Recientes Sugeridos -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Expedientes Recientemente Actualizados</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recentRequests as $rec)
                    <a href="{{ route('consultation.index', ['search' => $rec->request_code]) }}" 
                       class="p-4 rounded-2xl bg-slate-50 hover:bg-brand-50/50 border border-slate-200/80 hover:border-brand-200 transition-all group block">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold font-mono text-brand-600 group-hover:text-brand-700">{{ $rec->request_code }}</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">
                                {{ $rec->currentStatus?->name }}
                            </span>
                        </div>
                        <span class="text-xs font-bold text-slate-800 block truncate">{{ $rec->customer?->business_name }}</span>
                        <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2 pt-2 border-t border-slate-200/60">
                            <span>${{ number_format($rec->requested_amount, 2) }}</span>
                            <span>{{ $rec->agency?->name }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
