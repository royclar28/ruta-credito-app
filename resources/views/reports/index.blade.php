@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Centro de Reportes y Descargas</h2>
            <p class="text-xs text-slate-500 mt-1">Generación de archivos consolidados en formato CSV (compatible Excel UTF-8) y expedientes.</p>
        </div>
    </div>

    <!-- Panel de Filtros para Descarga -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-200/80 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-2">Parámetros del Reporte</h3>
        <p class="text-xs text-slate-500 mb-6">Selecciona los criterios para exportar la data histórica con clasificación canónica de Gerencia General.</p>

        <form method="GET" action="{{ route('reports.export_csv') }}" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Estatus -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Fase / Estatus</label>
                    <select name="status_id" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Todos los Estatus</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Producto -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Línea de Crédito</label>
                    <select name="product_id" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Todos los Productos</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Fecha Desde -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Desde</label>
                    <input type="date" name="date_from" value="{{ date('Y-m-d', strtotime('-30 days')) }}"
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <!-- Fecha Hasta -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Hasta</label>
                    <input type="date" name="date_to" value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-400">Exportación directa en streaming de alta velocidad.</span>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Descargar Reporte Excel / CSV</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Vista Previa de Registros Recientes -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Muestra de Datos Listos para Exportación</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase text-slate-500 border-b border-slate-100">
                        <th class="py-3 px-6">N° Solicitud</th>
                        <th class="py-3 px-6">Cliente</th>
                        <th class="py-3 px-6">Gerencia General</th>
                        <th class="py-3 px-6">Producto</th>
                        <th class="py-3 px-6 text-right">Monto Solicitado</th>
                        <th class="py-3 px-6">Estatus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($previewRequests as $pr)
                        <tr>
                            <td class="py-3 px-6 font-mono font-bold text-brand-600">{{ $pr->request_code }}</td>
                            <td class="py-3 px-6">{{ $pr->customer?->business_name }}</td>
                            <td class="py-3 px-6">
                                <span class="px-2 py-0.5 rounded text-[10px] bg-slate-100 text-slate-700">
                                    {{ $pr->gerencia_general }}
                                </span>
                            </td>
                            <td class="py-3 px-6">{{ $pr->product?->name }}</td>
                            <td class="py-3 px-6 text-right font-bold">${{ number_format($pr->requested_amount, 2) }}</td>
                            <td class="py-3 px-6">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $pr->currentStatus?->badge_color }}-50 text-{{ $pr->currentStatus?->badge_color }}-700">
                                    {{ $pr->currentStatus?->name }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
