@extends('layouts.app')

@section('title', 'Apertura de Solicitud de Crédito')

@section('content')
<div x-data="creditForm()" class="space-y-6">

    <!-- Header & Breadcrumbs con Branding Institucional -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-600 via-yellow-600 to-amber-500 flex items-center justify-center text-white shadow-md shadow-amber-500/20">
                <i data-lucide="file-plus-2" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">BANFANB • Custodia de Expedientes</span>
                    <span class="text-xs font-mono font-bold text-slate-500">Ref: {{ $suggestedCode }}</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 mt-1">Apertura y Registro de Expediente</h1>
                <p class="text-xs text-slate-500">Ingreso inicial de carpeta física en agencia y activación de tracking bancario.</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('workflow.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition-colors shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Volver a Bandejas</span>
            </a>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i>
                <span>Por favor corrige los siguientes errores:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('workflow.store') }}" class="space-y-6" id="creditForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- ========================================================================= -->
            <!-- COLUMNA IZQUIERDA (2 COLS): DATOS DEL CLIENTE Y OPERACIÓN CREDITICIA -->
            <!-- ========================================================================= -->
            <div class="lg:col-span-2 space-y-6">

                <!-- 1. IDENTIFICACIÓN DEL CLIENTE / SOLICITANTE -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="user-check" class="w-5 h-5 text-brand-600"></i>
                            <h2 class="text-base font-bold text-slate-900">1. Identificación del Solicitante</h2>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Búsqueda automática en padrón</span>
                    </div>

                    <!-- Tipo y Cédula/RIF con Búsqueda -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tipo Doc.</label>
                            <select name="document_type" x-model="docType" class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500">
                                <option value="V">V - Venezolano</option>
                                <option value="J">J - Jurídico / Empresa</option>
                                <option value="E">E - Extranjero</option>
                                <option value="G">G - Ente Gubernamental</option>
                            </select>
                        </div>

                        <div class="sm:col-span-6">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Número de Cédula o RIF</label>
                            <div class="relative">
                                <input type="text" name="document_number" x-model="docNumber" @keydown.enter.prevent="lookupCustomer()"
                                       placeholder="Ej: 12345678 o 301234567" 
                                       class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500 font-mono pr-20" required>
                                <button type="button" @click="lookupCustomer()" 
                                        class="absolute right-1.5 top-1.5 px-3 py-1 bg-brand-600 text-white text-xs font-bold rounded-lg hover:bg-brand-700 transition flex items-center gap-1 shadow-sm">
                                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                                    <span x-text="searching ? '...' : 'Buscar'"></span>
                                </button>
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tipo Cliente</label>
                            <select name="customer_type" x-model="customerType" class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500 font-semibold">
                                <option value="NATURAL">Persona Natural</option>
                                <option value="JURIDICO">Persona Jurídica</option>
                            </select>
                        </div>
                    </div>

                    <!-- Mensaje de cliente encontrado o nuevo -->
                    <div x-show="customerFound" x-cloak class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs text-emerald-800">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                            <span><strong>Cliente registrado en el sistema:</strong> Expediente previo localizado.</span>
                        </div>
                        <span class="font-mono text-[11px] bg-white px-2 py-0.5 rounded border border-emerald-300 font-semibold" x-text="docType + '-' + docNumber"></span>
                    </div>

                    <!-- Razón Social o Nombre Completo -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nombre Completo o Razón Social</label>
                        <input type="text" name="business_name" x-model="businessName" placeholder="Ej: AGROPECUARIA LOS MONOLITOS C.A. o JUAN PÉREZ" 
                               class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500 uppercase font-semibold" required>
                    </div>

                    <!-- Contacto: Teléfono, Email y Estado -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Teléfono Principal</label>
                            <input type="text" name="phone" x-model="phone" placeholder="0414-1234567 / 0212-..." 
                                   class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                            <input type="email" name="email" x-model="email" placeholder="cliente@correo.com" 
                                   class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Estado Geográfico</label>
                            <select name="state_id" x-model="stateId" class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500">
                                @foreach($states as $st)
                                    <option value="{{ $st->id }}" {{ $st->id == 2 ? 'selected' : '' }}>{{ $st->name }} ({{ $st->region->name ?? 'Nacional' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Dirección Fiscal / Habitación</label>
                        <input type="text" name="address" x-model="address" placeholder="Av. Principal, Edif. / Casa..." 
                               class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500">
                    </div>
                </div>

                <!-- 2. DATOS FINANCIEROS Y CREDITICIOS -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="badge-dollar-sign" class="w-5 h-5 text-amber-600"></i>
                            <h2 class="text-base font-bold text-slate-900">2. Información Financiera del Crédito</h2>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Parámetros de colocación</span>
                    </div>

                    <!-- Producto y Subproducto Dinámico -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Producto Bancario</label>
                            <select name="product_id" x-model="productId" @change="onProductChange()" class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500 font-semibold" required>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Subproducto Especializado</label>
                            <select name="subproduct_id" x-model="subproductId" class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500">
                                <option value="">-- Sin subproducto específico --</option>
                                <template x-for="sp in filteredSubproducts" :key="sp.id">
                                    <option :value="sp.id" x-text="sp.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Modalidad y Destino -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Modalidad de Financiamiento</label>
                            <select name="modality_id" class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500">
                                @foreach($modalities as $mod)
                                    <option value="{{ $mod->id }}">{{ $mod->name }} ({{ $mod->type }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Destino Específico del Crédito</label>
                            <input type="text" name="destination" placeholder="Ej: Adquisición de materia prima, inventario o equipos" 
                                   class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    </div>

                    <!-- Monto, Moneda y Cálculo UVC -->
                    <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                            <div class="sm:col-span-5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Monto Solicitado</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-2.5 text-slate-400 font-bold text-sm" x-text="currency === 'USD' ? '$' : 'Bs.'"></span>
                                    <input type="number" step="0.01" min="1" name="requested_amount" x-model.number="requestedAmount" @input="calculateUvc()"
                                           placeholder="0.00" class="w-full pl-10 text-base font-extrabold rounded-xl border-slate-200 bg-white focus:border-brand-500 focus:ring-brand-500 font-mono text-slate-900" required>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Moneda</label>
                                <select name="currency" x-model="currency" @change="calculateUvc()" class="w-full text-sm rounded-xl border-slate-200 bg-white focus:border-brand-500 focus:ring-brand-500 font-bold">
                                    <option value="VES">VES (Bolívares)</option>
                                    <option value="USD">USD (Dólares)</option>
                                </select>
                            </div>

                            <div class="sm:col-span-4">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tasa Cambio BCV</label>
                                <input type="number" step="0.0001" min="0.0001" name="exchange_rate" x-model.number="exchangeRate" @input="calculateUvc()"
                                       class="w-full text-sm rounded-xl border-slate-200 bg-white focus:border-brand-500 focus:ring-brand-500 font-mono">
                            </div>
                        </div>

                        <!-- Card Informativa del Contravalor UVC -->
                        <div class="flex items-center justify-between p-3.5 bg-gradient-to-r from-slate-900 to-slate-800 rounded-xl text-white">
                            <div class="flex items-center gap-2">
                                <i data-lucide="calculator" class="w-4 h-4 text-amber-400"></i>
                                <span class="text-xs font-medium text-slate-300">Equivalente Indexado UVC:</span>
                            </div>
                            <div class="text-right">
                                <span class="text-base font-mono font-extrabold text-amber-400" x-text="uvcFormatted"></span>
                                <span class="text-[11px] text-slate-400 block">Unidades de Valor de Crédito</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <input type="checkbox" id="public_entity" name="public_entity" value="1" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 h-4 w-4">
                            <label for="public_entity" class="text-xs font-semibold text-slate-700 select-none">El solicitante es un Ente Público, Alcaldía, Gobernación o Empresa del Estado</label>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- COLUMNA DERECHA (1 COL): CUSTODIA FÍSICA, AGENCIA Y LISTA DE CHEQUEO -->
            <!-- ========================================================================= -->
            <div class="space-y-6">

                <!-- 3. CUSTODIA Y DESTINO DE LA CARPETA FÍSICA -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
                    <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                        <i data-lucide="archive" class="w-5 h-5 text-indigo-600"></i>
                        <h2 class="text-base font-bold text-slate-900">3. Custodia en Agencia</h2>
                    </div>

                    <!-- Agencia de Recepción -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Agencia Receptora</label>
                        <select name="agency_id" class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500 font-semibold" required>
                            @foreach($agencies as $ag)
                                <option value="{{ $ag->id }}" {{ $ag->id == 1 ? 'selected' : '' }}>{{ $ag->name }} ({{ $ag->code }})</option>
                            @endforeach
                        </select>
                        <span class="text-[11px] text-slate-400 mt-1 block">Sucursal donde reposa el expediente original.</span>
                    </div>

                    <!-- Bandeja de Destino Inicial -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bandeja de Destino Inicial</label>
                        <select name="initial_status_id" x-model="initialStatusId" class="w-full text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500 font-semibold" required>
                            <option value="46">G.G. Clientes Carterizados (Estatus 46)</option>
                            <option value="41">G.G. Personas y PYMES (Estatus 41)</option>
                            <option value="1">Recepción en Agencia / Custodia Local (Estatus 1)</option>
                        </select>
                    </div>

                    <!-- Funcionario Revisor -->
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1">
                        <span class="font-bold text-slate-700 block">Funcionario Receptor:</span>
                        <div class="flex items-center gap-2 text-slate-600">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-brand-600"></i>
                            <span>{{ Auth::user()->name ?? 'Ejecutivo de Negocios' }}</span>
                        </div>
                        <span class="text-[11px] text-slate-400 block">{{ Auth::user()->email ?? '' }}</span>
                    </div>

                    <!-- Checklist de Recaudos Físicos -->
                    <div class="space-y-2 pt-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Recaudos Físicos Consignados</label>
                        <div class="space-y-2 text-xs text-slate-600">
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-slate-50 border border-slate-100">
                                <input type="checkbox" name="physical_checklists[]" value="Cédula/RIF y Poderes Vigentes" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span>Cédula de Identidad / RIF y Poderes Vigentes</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-slate-50 border border-slate-100">
                                <input type="checkbox" name="physical_checklists[]" value="Balances Personales o Estados Financieros" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span>Estados Financieros o Balances Visados</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-slate-50 border border-slate-100">
                                <input type="checkbox" name="physical_checklists[]" value="Declaración de ISLR" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span>Última Declaración de ISLR</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-slate-50 border border-slate-100">
                                <input type="checkbox" name="physical_checklists[]" value="Plan de Inversión y Presupuestos" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span>Plan de Inversión / Cotizaciones</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-slate-50 border border-slate-100">
                                <input type="checkbox" name="physical_checklists[]" value="Documentos de Garantía" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span>Títulos o Documentos de Garantía</span>
                            </label>
                        </div>
                    </div>

                    <!-- Observaciones de apertura -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Observaciones de Apertura</label>
                        <textarea name="observations" rows="3" placeholder="Detalles de la carpeta física, sellos, folios o notas del cliente..." 
                                  class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500"></textarea>
                    </div>

                    <!-- Botón de Envío -->
                    <div class="pt-3">
                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-bold text-sm shadow-lg shadow-amber-600/30 transition-all flex items-center justify-center gap-2">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            <span>Registrar Solicitud en Tracking</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>

<script>
    function creditForm() {
        return {
            docType: 'J',
            docNumber: '',
            customerType: 'JURIDICO',
            businessName: '',
            phone: '',
            email: '',
            address: '',
            stateId: 2,
            customerFound: false,
            searching: false,

            productId: 1,
            subproductId: '',
            allSubproducts: @json($subproducts),
            filteredSubproducts: [],
            initialStatusId: 46,

            requestedAmount: 50000,
            currency: 'USD',
            exchangeRate: 36.50,
            uvcFormatted: '0.00',

            init() {
                this.onProductChange();
                this.calculateUvc();
            },

            onProductChange() {
                const pid = parseInt(this.productId);
                this.filteredSubproducts = this.allSubproducts.filter(sp => sp.product_id === pid);
                this.subproductId = this.filteredSubproducts.length > 0 ? this.filteredSubproducts[0].id : '';

                // Autoseleccionar bandeja recomendada según el producto
                if (pid === 5) {
                    this.initialStatusId = 46; // Carterizados
                    this.customerType = 'JURIDICO';
                    this.docType = 'J';
                } else if (pid === 2 || pid === 3) {
                    this.initialStatusId = 41; // PYME
                    this.customerType = 'NATURAL';
                    this.docType = 'V';
                }
            },

            calculateUvc() {
                const amount = parseFloat(this.requestedAmount) || 0;
                const rate = parseFloat(this.exchangeRate) || 1;
                const uvc = rate > 0 ? (amount / rate) : amount;
                this.uvcFormatted = new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(uvc);
            },

            async lookupCustomer() {
                if (!this.docNumber) return;
                this.searching = true;
                try {
                    const res = await fetch(`{{ route('api.customer.search') }}?type=${this.docType}&number=${encodeURIComponent(this.docNumber.trim())}`);
                    const data = await res.json();
                    if (data.found && data.customer) {
                        const c = data.customer;
                        this.businessName = c.business_name;
                        this.customerType = c.customer_type;
                        this.phone = c.phone || '';
                        this.email = c.email || '';
                        this.address = c.address || '';
                        if (c.state_id) this.stateId = c.state_id;
                        this.customerFound = true;
                    } else {
                        this.customerFound = false;
                    }
                } catch (e) {
                    console.error('Error buscando cliente:', e);
                } finally {
                    this.searching = false;
                }
            }
        }
    }
</script>
@endsection
