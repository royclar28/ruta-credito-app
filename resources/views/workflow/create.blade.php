@extends('layouts.app')

@section('title', 'Apertura de Solicitud de Crédito')

@section('content')
<div x-data="creditForm()" class="space-y-6">

    <!-- Header & Breadcrumbs con Branding Institucional BANFANB -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-amber-500 via-amber-600 to-yellow-600 flex items-center justify-center text-slate-950 font-bold shadow-lg shadow-amber-500/20 shrink-0 p-3">
                <i data-lucide="file-plus-2" class="w-7 h-7 text-slate-950"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-amber-800 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full">BANFANB • AGENCIA BANCARIA</span>
                    <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">Ref: {{ $suggestedCode }}</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 mt-1">Apertura y Registro de Expediente</h1>
                <p class="text-xs text-slate-500">Ingreso de carpeta física en taquilla bancaria y activación de tracking con trazabilidad inmutable.</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('workflow.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 transition-colors shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4 text-slate-500"></i>
                <span>Volver a Bandejas</span>
            </a>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
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
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                                <i data-lucide="user-check" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900">1. Identificación del Solicitante</h2>
                                <p class="text-[11px] text-slate-500">Datos del titular de la solicitud de crédito</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo Doc, Cédula/RIF y Tipo Cliente -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tipo Doc.</label>
                            <select name="document_type" x-model="docType" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                                <option value="V">V - Venezolano</option>
                                <option value="J">J - Jurídico / Empresa</option>
                                <option value="E">E - Extranjero</option>
                                <option value="G">G - Ente Gubernamental</option>
                            </select>
                        </div>

                        <div class="sm:col-span-5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Número de Cédula o RIF</label>
                            <input type="text" name="document_number" x-model="docNumber"
                                   placeholder="Ej: 12345678 o 301234567" 
                                   class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder:text-slate-400 placeholder:font-normal font-mono focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all" required>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tipo Cliente</label>
                            <select name="customer_type" x-model="customerType" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                                <option value="JURIDICO">Persona Jurídica (Empresa)</option>
                                <option value="NATURAL">Persona Natural (Firma / Persona)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Razón Social o Nombre Completo -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nombre Completo o Razón Social</label>
                        <input type="text" name="business_name" x-model="businessName" placeholder="EJ: INVERSIONES BANFANB C.A. O JUAN CARLOS PÉREZ" 
                               class="w-full text-sm font-bold rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder:text-slate-400 placeholder:font-normal uppercase focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all" required>
                    </div>

                    <!-- Contacto: Teléfono, Email y Estado -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-6">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                            <div class="relative">
                                <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                                <input type="email" name="email" x-model="email" placeholder="cliente@correo.com" 
                                       class="w-full text-sm font-medium rounded-xl border border-slate-300 bg-white pl-10 pr-3.5 py-2.5 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Teléfono Principal</label>
                            <div class="relative">
                                <i data-lucide="phone" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                                <input type="text" name="phone" x-model="phone" placeholder="0414-1234567 / 0212-..." 
                                       class="w-full text-sm font-medium rounded-xl border border-slate-300 bg-white pl-10 pr-3.5 py-2.5 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Estado Geográfico y Dirección Fiscal -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Estado Geográfico</label>
                            <select name="state_id" x-model="stateId" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                                @foreach($states as $st)
                                    <option value="{{ $st->id }}" {{ $st->id == 2 ? 'selected' : '' }}>{{ $st->name }} ({{ $st->region->name ?? 'Nacional' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-8">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Dirección Fiscal / Domicilio</label>
                            <div class="relative">
                                <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                                <input type="text" name="address" x-model="address" placeholder="Av. Principal, Edificio / Casa, Municipio, Ciudad..." 
                                       class="w-full text-sm font-medium rounded-xl border border-slate-300 bg-white pl-10 pr-3.5 py-2.5 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. DATOS FINANCIEROS Y CREDITICIOS -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-bold">
                                <i data-lucide="badge-dollar-sign" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900">2. Información Financiera del Crédito</h2>
                                <p class="text-[11px] text-slate-500">Parámetros de colocación y cálculo de indexación UVC</p>
                            </div>
                        </div>
                    </div>

                    <!-- Producto y Subproducto Dinámico -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Producto Bancario</label>
                            <select name="product_id" x-model="productId" @change="onProductChange()" class="w-full text-sm font-bold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all" required>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Subproducto Especializado</label>
                            <select name="subproduct_id" x-model="subproductId" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
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
                            <select name="modality_id" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                                @foreach($modalities as $mod)
                                    <option value="{{ $mod->id }}">{{ $mod->name }} ({{ $mod->type }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Destino Específico del Crédito</label>
                            <input type="text" name="destination" placeholder="Ej: Adquisición de materia prima, inventario o equipos" 
                                   class="w-full text-sm font-medium rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                        </div>
                    </div>

                    <!-- Contenedor Financiero: Monto, Moneda y Conversión UVC -->
                    <div class="bg-slate-50/80 rounded-2xl border border-slate-200/90 p-5 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                            <div class="sm:col-span-5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Monto Solicitado</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-2.5 text-slate-400 font-bold text-base" x-text="currency === 'USD' ? '$' : 'Bs.'"></span>
                                    <input type="number" step="0.01" min="1" name="requested_amount" x-model.number="requestedAmount" @input="calculateUvc()"
                                           placeholder="0.00" class="w-full pl-10 pr-3 py-2 text-lg font-black rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 font-mono text-slate-900 shadow-sm" required>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Moneda</label>
                                <select name="currency" x-model="currency" @change="calculateUvc()" class="w-full text-sm font-bold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                                    <option value="USD">USD (Dólares)</option>
                                    <option value="VES">VES (Bolívares)</option>
                                </select>
                            </div>

                            <div class="sm:col-span-4">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tasa Cambio BCV / Ref.</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-2.5 text-slate-400 font-bold text-xs">Bs.</span>
                                    <input type="number" step="0.0001" min="0.0001" name="exchange_rate" x-model.number="exchangeRate" @input="calculateUvc()"
                                           class="w-full pl-10 pr-3 py-2 text-sm font-bold rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 font-mono text-slate-900 shadow-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Card Financiera Ejecutiva: Contravalor UVC -->
                        <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-900 rounded-2xl p-4 sm:p-5 text-white border border-slate-800 shadow-lg">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-3">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="calculator" class="w-4 h-4 text-amber-400"></i>
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Indexación SUDEBAN / BCV</span>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider bg-amber-400/10 text-amber-400 border border-amber-400/30 px-2 py-0.5 rounded">Cartera Productiva</span>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <span class="text-xs text-slate-400 block font-medium">Monto Base Solicitado:</span>
                                    <span class="text-lg font-bold font-mono text-white" x-text="(currency === 'USD' ? '$ ' : 'Bs. ') + Number(requestedAmount || 0).toLocaleString('es-VE', { minimumFractionDigits: 2 })"></span>
                                </div>

                                <div class="sm:text-right">
                                    <span class="text-xs text-slate-400 block font-medium">Equivalente en UVC:</span>
                                    <div class="flex items-baseline sm:justify-end gap-1.5">
                                        <span class="text-2xl font-black font-mono text-amber-400 tracking-tight" x-text="uvcFormatted"></span>
                                        <span class="text-xs font-bold text-amber-500 font-mono">UVC</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-2.5 pt-2.5 border-t border-slate-800/80">Unidad de Valor de Crédito indexada automáticamente según normativa bancaria nacional vigente.</p>
                        </div>

                        <div class="flex items-center gap-2.5 pt-1">
                            <input type="checkbox" id="public_entity" name="public_entity" value="1" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500 h-4 w-4">
                            <label for="public_entity" class="text-xs font-semibold text-slate-700 select-none cursor-pointer">El solicitante es un Ente Público, Alcaldía, Gobernación o Empresa del Estado</label>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- COLUMNA DERECHA (1 COL): CUSTODIA FÍSICA, AGENCIA Y LISTA DE CHEQUEO -->
            <!-- ========================================================================= -->
            <div class="space-y-6">

                <!-- 3. CUSTODIA Y DESTINO DE LA CARPETA FÍSICA -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm space-y-5">
                    <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold">
                            <i data-lucide="archive" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">3. Custodia en Agencia</h2>
                            <p class="text-[11px] text-slate-500">Recepción de carpeta física y enrutamiento</p>
                        </div>
                    </div>

                    <!-- Agencia de Recepción -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Agencia Receptora</label>
                        <select name="agency_id" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all" required>
                            @foreach($agencies as $ag)
                                <option value="{{ $ag->id }}" {{ $ag->id == 1 ? 'selected' : '' }}>{{ $ag->name }} ({{ $ag->code }})</option>
                            @endforeach
                        </select>
                        <span class="text-[11px] text-slate-400 mt-1 block">Sucursal donde reposa la carpeta física original.</span>
                    </div>

                    <!-- Bandeja de Destino Inicial -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bandeja de Destino Inicial</label>
                        <select name="initial_status_id" x-model="initialStatusId" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all" required>
                            <option value="46">G.G. Clientes Carterizados (Estatus 46)</option>
                            <option value="41">G.G. Personas y PYMES (Estatus 41)</option>
                            <option value="1">Recepción en Agencia / Custodia Local (Estatus 1)</option>
                        </select>
                    </div>

                    <!-- Funcionario Revisor Institucional -->
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/90 text-xs space-y-1.5">
                        <span class="font-extrabold text-slate-700 block uppercase tracking-wider text-[10px]">Funcionario Receptor:</span>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <span class="font-bold text-slate-900 block truncate">{{ Auth::user()->name ?? 'Funcionario Bancario' }}</span>
                                <span class="text-[11px] text-slate-500 truncate block">{{ Auth::user()->role?->name ?? 'Oficial de Crédito' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Checklist de Recaudos Físicos -->
                    <div class="space-y-2 pt-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Recaudos Físicos Consignados</label>
                        <div class="space-y-2 text-xs text-slate-700 font-medium">
                            <label class="flex items-center gap-2.5 cursor-pointer p-2.5 rounded-xl hover:bg-slate-50 border border-slate-200 bg-white transition-colors">
                                <input type="checkbox" name="physical_checklists[]" value="Cédula/RIF y Poderes Vigentes" checked class="rounded border-slate-300 text-amber-600 focus:ring-amber-500 h-4 w-4">
                                <span>Cédula de Identidad / RIF y Poderes Vigentes</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer p-2.5 rounded-xl hover:bg-slate-50 border border-slate-200 bg-white transition-colors">
                                <input type="checkbox" name="physical_checklists[]" value="Balances Personales o Estados Financieros" checked class="rounded border-slate-300 text-amber-600 focus:ring-amber-500 h-4 w-4">
                                <span>Estados Financieros o Balances Visados</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer p-2.5 rounded-xl hover:bg-slate-50 border border-slate-200 bg-white transition-colors">
                                <input type="checkbox" name="physical_checklists[]" value="Declaración de ISLR" checked class="rounded border-slate-300 text-amber-600 focus:ring-amber-500 h-4 w-4">
                                <span>Última Declaración de ISLR</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer p-2.5 rounded-xl hover:bg-slate-50 border border-slate-200 bg-white transition-colors">
                                <input type="checkbox" name="physical_checklists[]" value="Plan de Inversión y Presupuestos" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500 h-4 w-4">
                                <span>Plan de Inversión / Cotizaciones</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer p-2.5 rounded-xl hover:bg-slate-50 border border-slate-200 bg-white transition-colors">
                                <input type="checkbox" name="physical_checklists[]" value="Documentos de Garantía" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500 h-4 w-4">
                                <span>Títulos o Documentos de Garantía</span>
                            </label>
                        </div>
                    </div>

                    <!-- Observaciones de apertura -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Observaciones de Apertura</label>
                        <textarea name="observations" rows="3" placeholder="Detalles de la carpeta física, sellos, folios o notas del cliente..." 
                                  class="w-full text-xs font-medium rounded-xl border border-slate-300 bg-white p-3 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all"></textarea>
                    </div>

                    <!-- Botón de Envío BANFANB -->
                    <div class="pt-2">
                        <button type="submit" class="w-full py-4 px-5 rounded-2xl bg-gradient-to-r from-amber-500 via-amber-600 to-yellow-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-sm shadow-xl shadow-amber-500/25 border border-amber-400/50 flex items-center justify-center gap-2.5 transition-all transform active:scale-[0.99] cursor-pointer">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-slate-950"></i>
                            <span>Registrar y Aperturar Expediente</span>
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

                // Ajuste inteligente según el producto
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
            }
        }
    }
</script>
@endsection
