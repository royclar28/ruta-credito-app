<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente - {{ $creditRequest->request_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; background: white; }
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 p-6 text-slate-800">
    <div class="max-w-3xl mx-auto bg-white p-8 md:p-12 rounded-2xl shadow border border-slate-200">
        
        <!-- Botón Imprimir -->
        <div class="no-print flex justify-end mb-6">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-500 shadow">
                Imprimir Expediente
            </button>
        </div>

        <!-- Encabezado Oficial -->
        <div class="border-b-2 border-slate-900 pb-6 mb-6 flex items-start justify-between">
            <div>
                <h1 class="text-xl font-extrabold uppercase tracking-tight text-slate-900">Ruta de Crédito</h1>
                <p class="text-xs text-slate-500">Expediente Oficial de Crédito y Dictamen de Riesgo</p>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold font-mono text-blue-600 block text-base">{{ $creditRequest->request_code }}</span>
                <span class="text-[11px] text-slate-400">Emisión: {{ date('d/m/Y H:i') }}</span>
            </div>
        </div>

        <!-- Ficha de Datos -->
        <div class="grid grid-cols-2 gap-6 text-xs mb-8">
            <div class="space-y-2">
                <h3 class="font-bold uppercase tracking-wider text-slate-400 border-b pb-1">1. Información del Solicitante</h3>
                <p><strong>Cliente:</strong> {{ $creditRequest->customer?->business_name }}</p>
                <p><strong>Identificación:</strong> {{ $creditRequest->customer?->formatted_document }}</p>
                <p><strong>Tipo:</strong> {{ $creditRequest->customer?->customer_type }}</p>
                <p><strong>Teléfono:</strong> {{ $creditRequest->customer?->phone ?? 'N/A' }}</p>
                <p><strong>Correo:</strong> {{ $creditRequest->customer?->email ?? 'N/A' }}</p>
            </div>

            <div class="space-y-2">
                <h3 class="font-bold uppercase tracking-wider text-slate-400 border-b pb-1">2. Estructura Financiera</h3>
                <p><strong>Producto:</strong> {{ $creditRequest->product?->name }}</p>
                <p><strong>Subproducto:</strong> {{ $creditRequest->subproduct?->name ?? 'Estándar' }}</p>
                <p><strong>Modalidad:</strong> {{ $creditRequest->modality?->name ?? 'Préstamo' }}</p>
                <p><strong>Monto Solicitado:</strong> ${{ number_format($creditRequest->requested_amount, 2) }}</p>
                <p><strong>Monto en UVC:</strong> {{ number_format($creditRequest->amount_uvc, 2) }} UVC</p>
                <p><strong>Destino:</strong> {{ $creditRequest->destination }}</p>
            </div>
        </div>

        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs mb-8 flex justify-between items-center">
            <div>
                <span class="text-slate-400 uppercase font-bold text-[10px] block">Gerencia General Responsable:</span>
                <strong class="text-slate-900 text-sm">{{ $creditRequest->gerencia_general }}</strong>
            </div>
            <div>
                <span class="text-slate-400 uppercase font-bold text-[10px] block">Estatus Actual:</span>
                <span class="px-2.5 py-1 rounded-full font-bold bg-blue-100 text-blue-800">{{ $creditRequest->currentStatus?->name }}</span>
            </div>
        </div>

        <!-- Trazabilidad Histórica Inmutable -->
        <div class="mb-10">
            <h3 class="font-bold uppercase tracking-wider text-slate-400 text-xs border-b pb-2 mb-4">3. Registro Cronológico de Auditoría</h3>
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-[10px] font-bold uppercase text-slate-600">
                        <th class="p-2">Fecha y Hora</th>
                        <th class="p-2">Acción / Estatus</th>
                        <th class="p-2">Operador / Rol</th>
                        <th class="p-2">Observaciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($creditRequest->histories as $h)
                        <tr>
                            <td class="p-2 font-mono text-[11px] whitespace-nowrap">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-2 font-semibold text-slate-900">{{ $h->action_performed }}</td>
                            <td class="p-2">{{ $h->user?->name }} ({{ $h->user?->role?->name }})</td>
                            <td class="p-2 text-slate-600">{{ $h->observations }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Firmas de Autorización -->
        <div class="grid grid-cols-3 gap-6 pt-10 border-t border-slate-200 text-center text-xs text-slate-500">
            <div>
                <div class="border-b border-slate-400 mb-2 h-12"></div>
                <p class="font-bold text-slate-800">Analista de Crédito</p>
                <p class="text-[10px]">Evaluación Técnica</p>
            </div>
            <div>
                <div class="border-b border-slate-400 mb-2 h-12"></div>
                <p class="font-bold text-slate-800">Comité de Crédito</p>
                <p class="text-[10px]">Aprobación Colegiada</p>
            </div>
            <div>
                <div class="border-b border-slate-400 mb-2 h-12"></div>
                <p class="font-bold text-slate-800">Vicepresidencia</p>
                <p class="text-[10px]">Autorización Final</p>
            </div>
        </div>

    </div>
</body>
</html>
