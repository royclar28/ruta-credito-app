@extends('layouts.app')

@section('title', 'Apertura de Solicitud de Crédito - BANFANB')

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
                <h1 class="text-2xl font-black tracking-tight text-slate-900 mt-1">Formulario de Registro de Solicitud</h1>
                <p class="text-xs text-slate-500">Apertura y custodia de carpeta física de crédito según normativa institucional SISPROA.</p>
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
            <!-- COLUMNA IZQUIERDA (2 COLS): DATOS CLIENTE, CRÉDITO, GARANTÍAS Y VINCULACIÓN -->
            <!-- ========================================================================= -->
            <div class="lg:col-span-2 space-y-6">

                <!-- 1. IDENTIFICACIÓN Y DATOS DEL SOLICITANTE -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                                <i data-lucide="user-check" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900">1. Identificación del Solicitante</h2>
                                <p class="text-[11px] text-slate-500">Ficha del cliente titular (Persona Natural / Jurídica)</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] text-slate-400 font-medium block">Fecha Planilla</span>
                            <span class="text-xs font-bold text-slate-700 font-mono">{{ date('d-m-Y') }}</span>
                        </div>
                    </div>

                    <!-- Tipo Doc, Cédula/RIF y Tipo Cliente -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tipo Identificación</label>
                            <select name="document_type" x-model="docType" @change="onDocTypeChange()" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                                <option value="J">Jurídico RIF (J)</option>
                                <option value="V">Venezolano (V)</option>
                                <option value="E">Extranjero (E)</option>
                                <option value="R">Firma Personal (R)</option>
                                <option value="G">Gubernamental (G)</option>
                            </select>
                        </div>

                        <div class="sm:col-span-5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" x-text="docType === 'J' || docType === 'G' || docType === 'R' ? 'Número de RIF' : 'Cédula de Identidad'"></label>
                            <input type="text" name="document_number" x-model="docNumber"
                                   placeholder="Ej: 301234567 o 12345678" 
                                   class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder:text-slate-400 placeholder:font-normal font-mono focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all" required>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tipo de Cliente</label>
                            <select name="customer_type" x-model="customerType" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                                <option value="JURIDICO">Persona Jurídica (Empresa)</option>
                                <option value="NATURAL">Persona Natural</option>
                            </select>
                        </div>
                    </div>

                    <!-- Razón Social o Nombre Completo -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" x-text="customerType === 'JURIDICO' ? 'Razón Social de la Empresa' : 'Nombres y Apellidos Completos'"></label>
                        <input type="text" name="business_name" x-model="businessName" placeholder="EJ: INVERSIONES MONOLITOS C.A. O CARLOS ALBERTO MENDEZ" 
                               class="w-full text-sm font-bold rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder:text-slate-400 placeholder:font-normal uppercase focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all" required>
                    </div>

                    <!-- Fecha de Constitución/Nacimiento, Antigüedad/Edad y Género -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" x-text="customerType === 'JURIDICO' ? 'Fecha de Constitución' : 'Fecha de Nacimiento'"></label>
                            <input type="date" name="birth_or_constitution_date" x-model="birthOrConstDate" @change="calculateAge()"
                                   class="w-full text-sm font-medium rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" x-text="customerType === 'JURIDICO' ? 'Años Antigüedad' : 'Edad'"></label>
                            <input type="text" readonly x-model="ageOrYears" placeholder="0" 
                                   class="w-full text-sm font-bold text-center rounded-xl border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-slate-700">
                        </div>

                        <div class="sm:col-span-4" x-show="customerType === 'NATURAL'">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Género</label>
                            <select name="gender" class="w-full text-sm font-medium rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                                <option value="M">Masculino (M)</option>
                                <option value="F">Femenino (F)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Actividad Económica y Código -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-8">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Actividad Económica</label>
                            <input type="text" name="economic_activity" placeholder="Ej: Comercio al por mayor de alimentos, manufactura o servicios" 
                                   class="w-full text-sm font-medium rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Cód. Actividad Económica</label>
                            <input type="text" name="economic_activity_code" placeholder="Ej: 512290" 
                                   class="w-full text-sm font-mono font-medium rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                        </div>
                    </div>

                    <!-- Teléfonos y Correo Electrónico -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Teléfono Local / Oficina</label>
                            <div class="relative">
                                <i data-lucide="phone" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                                <input type="text" name="phone" placeholder="0212-1234567" 
                                       class="w-full text-sm font-medium rounded-xl border border-slate-300 bg-white pl-10 pr-3.5 py-2.5 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                            </div>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Teléfono Celular</label>
                            <div class="relative">
                                <i data-lucide="smartphone" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                                <input type="text" name="phone_mobile" placeholder="0414-1234567" 
                                       class="w-full text-sm font-medium rounded-xl border border-slate-300 bg-white pl-10 pr-3.5 py-2.5 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                            </div>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                            <div class="relative">
                                <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                                <input type="email" name="email" placeholder="cliente@correo.com" 
                                       class="w-full text-sm font-medium rounded-xl border border-slate-300 bg-white pl-10 pr-3.5 py-2.5 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Estado Geográfico y Dirección Fiscal -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Estado Geográfico</label>
                            <select name="state_id" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                                @foreach($states as $st)
                                    <option value="{{ $st->id }}" {{ $st->id == 2 ? 'selected' : '' }}>{{ $st->name }} ({{ $st->region->name ?? 'Nacional' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-8">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Dirección Fiscal / Domicilio</label>
                            <div class="relative">
                                <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                                <input type="text" name="address" placeholder="Av. Principal, Edificio / Casa, Municipio, Ciudad..." 
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
                                <p class="text-[11px] text-slate-500">Parámetros de colocación y cálculo de indexación UVC (BCV / SUDEBAN)</p>
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

                    <!-- Modalidad, Destino y Ente Postulante -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Modalidad de Financiamiento</label>
                            <select name="modality_id" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                                @foreach($modalities as $mod)
                                    <option value="{{ $mod->id }}">{{ $mod->name }} ({{ $mod->type }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Destino del Crédito</label>
                            <input type="text" name="destination" placeholder="Ej: Capital de trabajo, materia prima" 
                                   class="w-full text-sm font-medium rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all" required>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ente que lo Postula</label>
                            <input type="text" name="public_entity_name" placeholder="Ej: MPPD, Gobernación, Ninguno" 
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
                            <p class="text-[11px] text-slate-400 mt-2.5 pt-2.5 border-t border-slate-800/80">Unidad de Valor de Crédito calculada según tipo de cambio de referencia para la cartera bancaria nacional.</p>
                        </div>
                    </div>
                </div>

                <!-- 3. MÓDULO DE GARANTÍAS (SISPROA54) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                                <i data-lucide="shield-alert" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900">3. Garantías de la Operación</h2>
                                <p class="text-[11px] text-slate-500">Esquema de fianza, aval o garantía colateral consignada</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">¿Tiene Garantía?</label>
                            <select name="has_guarantee" x-model="hasGuarantee" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                                <option value="N">No Posee Garantía</option>
                                <option value="S">Sí Posee Garantía</option>
                            </select>
                        </div>

                        <div class="sm:col-span-8" x-show="hasGuarantee === 'S'">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tipo de Garantía</label>
                            <select name="guarantee_type" x-model="guaranteeType" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                                <option value="Fianza Personal">Fianza Personal</option>
                                <option value="Fianza Jurídica">Fianza Jurídica</option>
                                <option value="Avales">Avales</option>
                                <option value="Inmobiliario">Inmobiliario (Hipoteca)</option>
                                <option value="Mobiliario">Mobiliario (Prenda)</option>
                                <option value="Colateral en Divisas">Colateral en Divisas</option>
                            </select>
                        </div>
                    </div>

                    <!-- Datos del Fiador / Garante (si aplica) -->
                    <div x-show="hasGuarantee === 'S' && (guaranteeType === 'Fianza Personal' || guaranteeType === 'Fianza Jurídica')" class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700 block">Datos del Fiador / Garante</span>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tipo Doc.</label>
                                <select name="guarantor_doc_type" class="w-full text-xs font-semibold rounded-lg border border-slate-300 bg-white px-2.5 py-2">
                                    <option value="V">V - Cédula</option>
                                    <option value="J">J - RIF Empresa</option>
                                    <option value="E">E - Extranjero</option>
                                    <option value="R">R - Firma Pers.</option>
                                </select>
                            </div>

                            <div class="sm:col-span-4">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Cédula / RIF Fiador</label>
                                <input type="text" name="guarantor_doc_number" placeholder="Ej: 12345678" 
                                       class="w-full text-xs font-mono rounded-lg border border-slate-300 bg-white px-3 py-2">
                            </div>

                            <div class="sm:col-span-5">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Nombre Completo o Empresa Fiadora</label>
                                <input type="text" name="guarantor_name" placeholder="Nombre de la persona o empresa fiadora" 
                                       class="w-full text-xs uppercase rounded-lg border border-slate-300 bg-white px-3 py-2">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Teléfono Fiador</label>
                                <input type="text" name="guarantor_phone" placeholder="0414-1234567" 
                                       class="w-full text-xs rounded-lg border border-slate-300 bg-white px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Correo Electrónico Fiador</label>
                                <input type="email" name="guarantor_email" placeholder="fiador@correo.com" 
                                       class="w-full text-xs rounded-lg border border-slate-300 bg-white px-3 py-2">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. DATOS DE VINCULACIÓN DEL CLIENTE (SISPROA54) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center font-bold">
                                <i data-lucide="link-2" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900">4. Datos de Vinculación del Cliente con el Banco</h2>
                                <p class="text-[11px] text-slate-500">Reciprocidad comercial, cuentas y productos BANFANB</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Intervención</label>
                            <select name="has_exchange_intervention" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-800">
                                <option value="N">No</option>
                                <option value="S">Sí</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pago Impuestos</label>
                            <select name="pays_taxes" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-800">
                                <option value="N">No</option>
                                <option value="S">Sí</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Posee TDC</label>
                            <select name="has_credit_card" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-800">
                                <option value="N">No</option>
                                <option value="S">Sí</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Cuenta Divisas</label>
                            <select name="has_foreign_currency_account" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-800">
                                <option value="N">No</option>
                                <option value="S">Sí</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end pt-2">
                        <div class="sm:col-span-6">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Saldo Promedio en Cuenta (Bs. / USD)</label>
                            <input type="number" step="0.01" min="0" name="average_balance" placeholder="0.00" 
                                   class="w-full text-sm font-bold font-mono rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 shadow-sm transition-all">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Punto Venta POS</label>
                            <select name="has_pos" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800">
                                <option value="N">No</option>
                                <option value="S">Sí</option>
                            </select>
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pago Nóminas</label>
                            <select name="has_payroll" class="w-full text-sm font-semibold rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-800">
                                <option value="N">No</option>
                                <option value="S">Sí</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- COLUMNA DERECHA (1 COL): CUSTODIA FÍSICA, AGENCIA Y LISTA DE CHEQUEO -->
            <!-- ========================================================================= -->
            <div class="space-y-6">

                <!-- 5. CUSTODIA Y DESTINO DE LA CARPETA FÍSICA -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm space-y-5">
                    <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold">
                            <i data-lucide="archive" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">5. Custodia en Agencia</h2>
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
                            <span>Registrar y Aperturar Solicitud</span>
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
            birthOrConstDate: '',
            ageOrYears: '',

            productId: 1,
            subproductId: '',
            allSubproducts: @json($subproducts),
            filteredSubproducts: [],
            initialStatusId: 46,

            requestedAmount: 50000,
            currency: 'USD',
            exchangeRate: 36.50,
            uvcFormatted: '0.00',

            hasGuarantee: 'N',
            guaranteeType: 'Fianza Personal',

            init() {
                this.onProductChange();
                this.calculateUvc();
            },

            onDocTypeChange() {
                if (this.docType === 'J' || this.docType === 'G' || this.docType === 'R') {
                    this.customerType = 'JURIDICO';
                } else {
                    this.customerType = 'NATURAL';
                }
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
            },

            calculateAge() {
                if (!this.birthOrConstDate) {
                    this.ageOrYears = '';
                    return;
                }
                const d = new Date(this.birthOrConstDate);
                const today = new Date();
                let diff = today.getFullYear() - d.getFullYear();
                const m = today.getMonth() - d.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < d.getDate())) {
                    diff--;
                }
                this.ageOrYears = diff >= 0 ? diff : 0;
            }
        }
    }
</script>
@endsection
