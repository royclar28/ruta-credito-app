<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Region;
use App\Models\State;
use App\Models\Agency;
use App\Models\CreditProduct;
use App\Models\CreditSubproduct;
use App\Models\CreditModality;
use App\Models\GuaranteeType;
use App\Models\Customer;
use App\Models\CreditStatus;
use App\Models\WorkflowTransition;
use App\Models\CreditRequest;
use App\Models\CreditRequestHistory;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================================
        // 1. ROLES DEL SISTEMA (RBAC AUTÓNOMO)
        // =========================================================================
        $roles = [
            'admin' => ['name' => 'Administrador del Sistema', 'desc' => 'Control total de usuarios, catálogos y gobernanza.'],
            'analista_carterizados' => ['name' => 'Analista Clientes Carterizados', 'desc' => 'Gestión de bandeja especializada de clientes carterizados.'],
            'analista_pyme' => ['name' => 'Analista Personas y PYME', 'desc' => 'Evaluación de créditos PYME y Consumo.'],
            'analista_consumo' => ['name' => 'Analista Crédito Consumo', 'desc' => 'Gestión de créditos personales y nómina.'],
            'gerente_agencia' => ['name' => 'Gerente de Agencia', 'desc' => 'Recepción y radicación inicial en sucursal bancaria.'],
            'comunal' => ['name' => 'Coordinador Red Comunal', 'desc' => 'Gestión de microcréditos comunales y emprendedores.'],
            'comite' => ['name' => 'Miembro Comité de Crédito', 'desc' => 'Aprobación o diferimiento colegiado en comité.'],
            'vp_credito' => ['name' => 'Vicepresidencia de Crédito', 'desc' => 'Aprobación ejecutiva de alto nivel.'],
            'auditor' => ['name' => 'Auditor / Control y Seguimiento', 'desc' => 'Supervisión de trazabilidad, KPIs y cumplimiento.'],
        ];

        $roleModels = [];
        foreach ($roles as $slug => $data) {
            $roleModels[$slug] = Role::updateOrCreate(
                ['slug' => $slug],
                ['name' => $data['name'], 'description' => $data['desc'], 'is_active' => true]
            );
        }

        // =========================================================================
        // 2. ESTRUCTURA TERRITORIAL Y AGÊNCIAS
        // =========================================================================
        $regCapital = Region::updateOrCreate(['code' => 'REG-CAP'], ['name' => 'Región Capital']);
        $regCentral = Region::updateOrCreate(['code' => 'REG-CEN'], ['name' => 'Región Central']);
        $regOccidente = Region::updateOrCreate(['code' => 'REG-OCC'], ['name' => 'Región Occidental']);

        $edoMiranda = State::updateOrCreate(['code' => 'MIR'], ['region_id' => $regCapital->id, 'name' => 'Miranda']);
        $edoDtto = State::updateOrCreate(['code' => 'DC'], ['region_id' => $regCapital->id, 'name' => 'Distrito Capital']);
        $edoCarabobo = State::updateOrCreate(['code' => 'CAR'], ['region_id' => $regCentral->id, 'name' => 'Carabobo']);
        $edoZulia = State::updateOrCreate(['code' => 'ZUL'], ['region_id' => $regOccidente->id, 'name' => 'Zulia']);

        $agPrincipal = Agency::updateOrCreate(['code' => '0101'], [
            'state_id' => $edoDtto->id,
            'name' => 'Agencia Sede Principal (Caracas)',
            'address' => 'Av. Universidad, Esq. Sociedad',
            'phone' => '0212-5001111',
            'is_active' => true,
        ]);

        $agChacao = Agency::updateOrCreate(['code' => '0102'], [
            'state_id' => $edoMiranda->id,
            'name' => 'Agencia Chacao',
            'address' => 'Av. Francisco de Miranda, Chacao',
            'phone' => '0212-2634455',
            'is_active' => true,
        ]);

        $agValencia = Agency::updateOrCreate(['code' => '0201'], [
            'state_id' => $edoCarabobo->id,
            'name' => 'Agencia Valencia Centro',
            'address' => 'Av. Bolívar Norte, Valencia',
            'phone' => '0241-8223344',
            'is_active' => true,
        ]);

        $agMaracaibo = Agency::updateOrCreate(['code' => '0301'], [
            'state_id' => $edoZulia->id,
            'name' => 'Agencia Maracaibo Bella Vista',
            'address' => 'Calle 72 con Av. Bella Vista',
            'phone' => '0261-7918822',
            'is_active' => true,
        ]);

        // =========================================================================
        // 3. USUARIOS OPERATIVOS Y ADMINISTRATIVOS
        // =========================================================================
        $password = Hash::make('password123');

        $usersData = [
            [
                'name' => 'Administrador General',
                'email' => 'admin@empresa.com',
                'role_id' => $roleModels['admin']->id,
                'document_id' => 'V10111222',
                'agency_id' => $agPrincipal->id,
            ],
            [
                'name' => 'Lic. Carlos Mendez (Carterizados)',
                'email' => 'analista.carterizados@empresa.com',
                'role_id' => $roleModels['analista_carterizados']->id,
                'document_id' => 'V14555888',
                'agency_id' => $agPrincipal->id,
            ],
            [
                'name' => 'Ing. Mariana Gomez (PYME)',
                'email' => 'analista.pyme@empresa.com',
                'role_id' => $roleModels['analista_pyme']->id,
                'document_id' => 'V16888999',
                'agency_id' => $agChacao->id,
            ],
            [
                'name' => 'Tsu. Roberto Silva (Consumo)',
                'email' => 'analista.consumo@empresa.com',
                'role_id' => $roleModels['analista_consumo']->id,
                'document_id' => 'V18222333',
                'agency_id' => $agChacao->id,
            ],
            [
                'name' => 'Dr. Fernando Arreaza (Comité)',
                'email' => 'comite@empresa.com',
                'role_id' => $roleModels['comite']->id,
                'document_id' => 'V12444555',
                'agency_id' => $agPrincipal->id,
            ],
            [
                'name' => 'Dra. Elena Rojas (VP Crédito)',
                'email' => 'vp.credito@empresa.com',
                'role_id' => $roleModels['vp_credito']->id,
                'document_id' => 'V11777888',
                'agency_id' => $agPrincipal->id,
            ],
            [
                'name' => 'Gerente Sucursal Valencia',
                'email' => 'gerente.agencia@empresa.com',
                'role_id' => $roleModels['gerente_agencia']->id,
                'document_id' => 'V13999000',
                'agency_id' => $agValencia->id,
            ],
            [
                'name' => 'Auditor de Riesgo y Cumplimiento',
                'email' => 'auditor@empresa.com',
                'role_id' => $roleModels['auditor']->id,
                'document_id' => 'V15333444',
                'agency_id' => $agPrincipal->id,
            ],
        ];

        $userModels = [];
        foreach ($usersData as $ud) {
            $userModels[$ud['email']] = User::updateOrCreate(
                ['email' => $ud['email']],
                array_merge($ud, ['password' => $password, 'is_active' => true])
            );
        }

        // =========================================================================
        // 4. CATÁLOGOS DE PRODUCTOS, SUBPRODUCTOS Y MODALIDADES
        // =========================================================================
        $prodComercial = CreditProduct::updateOrCreate(['code' => 'COM'], [
            'name' => 'Crédito Comercial',
            'category' => 'COMERCIAL',
            'description' => 'Financiamiento empresarial para capital de trabajo e inversión.',
            'is_active' => true,
        ]);

        $prodConsumo = CreditProduct::updateOrCreate(['code' => 'CON'], [
            'name' => 'Crédito al Consumo',
            'category' => 'CONSUMO',
            'description' => 'Préstamos personales, nómina y vehículos para personas naturales.',
            'is_active' => true,
        ]);

        $prodMicro = CreditProduct::updateOrCreate(['code' => 'MIC'], [
            'name' => 'Microcrédito Productivo',
            'category' => 'MICROCREDITO',
            'description' => 'Apoyo financiero a pequeños productores y emprendedores.',
            'is_active' => true,
        ]);

        $prodHipotecario = CreditProduct::updateOrCreate(['code' => 'HIP'], [
            'name' => 'Crédito Hipotecario',
            'category' => 'HIPOTECARIO',
            'description' => 'Adquisición y mejoras de vivienda o locales comerciales.',
            'is_active' => true,
        ]);

        $prodGrandesEmp = CreditProduct::updateOrCreate(['code' => 'GRA'], [
            'name' => 'Grandes Empresas y Corporativo',
            'category' => 'COMERCIAL',
            'description' => 'Grandes líneas de crédito para corporaciones e industrias.',
            'is_active' => true,
        ]);

        // Subproductos
        CreditSubproduct::updateOrCreate(['code' => 'SP-CAP-TRAB'], ['product_id' => $prodComercial->id, 'name' => 'Línea de Capital de Trabajo']);
        CreditSubproduct::updateOrCreate(['code' => 'SP-CREDIMUJER'], ['product_id' => $prodMicro->id, 'name' => 'Credimujer Productiva']);
        CreditSubproduct::updateOrCreate(['code' => 'SP-CREDIFUTURO'], ['product_id' => $prodMicro->id, 'name' => 'Credifuturo Emprendedor']);
        CreditSubproduct::updateOrCreate(['code' => 'SP-CREDIAGRO'], ['product_id' => $prodMicro->id, 'name' => 'Crediagro y Conuco']);
        CreditSubproduct::updateOrCreate(['code' => 'SP-CREDIPERSONAL'], ['product_id' => $prodConsumo->id, 'name' => 'Credipersonal Inmediato']);
        CreditSubproduct::updateOrCreate(['code' => 'SP-CREDINOMINA'], ['product_id' => $prodConsumo->id, 'name' => 'Crédito Nómina Empleado']);
        CreditSubproduct::updateOrCreate(['code' => 'SP-HIP-VIV'], ['product_id' => $prodHipotecario->id, 'name' => 'Adquisición de Vivienda Principal']);

        // Modalidades y Garantías
        $modPlazo = CreditModality::updateOrCreate(['code' => 'MOD-PLAZO'], ['name' => 'Préstamo a Plazo Fijo']);
        $modRotativa = CreditModality::updateOrCreate(['code' => 'MOD-ROT'], ['name' => 'Línea de Crédito Rotativa']);
        GuaranteeType::updateOrCreate(['code' => 'GAR-HIP'], ['name' => 'Garantía Hipotecaria']);
        GuaranteeType::updateOrCreate(['code' => 'GAR-PREN'], ['name' => 'Garantía Prendaria / Fianza']);
        GuaranteeType::updateOrCreate(['code' => 'GAR-QUIRO'], ['name' => 'Firma Personal / Quirografaria']);

        // =========================================================================
        // 5. CATÁLOGO DE ESTATUS Y MÁQUINA DE ESTADOS (STATE MACHINE)
        // =========================================================================
        $statusesData = [
            1 => ['code' => 'RECIBIDO_AGENCIA', 'name' => 'RECIBIDO EN AGENCIA', 'stage' => 'RECEPCION', 'color' => 'blue', 'term' => false],
            2 => ['code' => 'ENVIADO_CONTROL', 'name' => 'ENVIADO A CONTROL Y SEGUIMIENTO', 'stage' => 'RECEPCION', 'color' => 'blue', 'term' => false],
            33 => ['code' => 'RECIBIDO_CONTROL', 'name' => 'RECIBIDO EN CONTROL Y SEGUIMIENTO', 'stage' => 'RECEPCION', 'color' => 'indigo', 'term' => false],
            46 => ['code' => 'ENVIADO_CARTERIZADOS', 'name' => 'ENVIADO A CARTERIZADOS POR CONTROL Y SEGUIMIENTO', 'stage' => 'RECEPCION', 'color' => 'amber', 'term' => false],
            57 => ['code' => 'RECIBIDO_CARTERIZADOS', 'name' => 'RECIBIDO GG CARTERIZADOS', 'stage' => 'ANALISIS', 'color' => 'indigo', 'term' => false],
            62 => ['code' => 'EN_ANALISIS_CARTERIZADOS', 'name' => 'EN ANALISIS POR CARTERIZADOS', 'stage' => 'ANALISIS', 'color' => 'blue', 'term' => false],
            60 => ['code' => 'ESPERA_RECAUDOS_CARTERIZADOS', 'name' => 'EN ESPERA POR RECAUDOS POR CARTERIZADOS', 'stage' => 'DIFERIDO', 'color' => 'amber', 'term' => false],
            61 => ['code' => 'DEVUELTO_CONTROL_CARTERIZADOS', 'name' => 'DEVUELTO A CONTROL Y SEGUIMIENTO POR CARTERIZADOS', 'stage' => 'DEVUELTO', 'color' => 'rose', 'term' => false],
            58 => ['code' => 'ENVIADO_COMITE_CARTERIZADOS', 'name' => 'ENVIADO A COMITE POR CARTERIZADOS', 'stage' => 'COMITE', 'color' => 'purple', 'term' => false],
            41 => ['code' => 'RECIBIDO_PYME', 'name' => 'RECIBIDO EN GG PERSONAS Y PYME', 'stage' => 'ANALISIS', 'color' => 'indigo', 'term' => false],
            42 => ['code' => 'EN_ANALISIS_PYME', 'name' => 'EN ANALISIS POR GG PERSONAS Y PYME', 'stage' => 'ANALISIS', 'color' => 'blue', 'term' => false],
            49 => ['code' => 'EN_ANALISIS_COMITE', 'name' => 'EN ANALISIS POR COMITÉ DE CRÉDITO', 'stage' => 'COMITE', 'color' => 'purple', 'term' => false],
            74 => ['code' => 'DIFERIDO_COMITE', 'name' => 'DIFERIDO A CARTERIZADO POR COMITE', 'stage' => 'DIFERIDO', 'color' => 'amber', 'term' => false],
            162 => ['code' => 'DEVUELTO_COMITE', 'name' => 'DEVUELTO A CARTERIZADOS POR COMITE', 'stage' => 'DEVUELTO', 'color' => 'rose', 'term' => false],
            87 => ['code' => 'ENVIADO_VP', 'name' => 'ENVIADO A VP DE CRÉDITO', 'stage' => 'VP', 'color' => 'purple', 'term' => false],
            90 => ['code' => 'APROBADO_VP', 'name' => 'APROBADO POR VP DE CRÉDITO', 'stage' => 'PROTOCOLIZACION', 'color' => 'emerald', 'term' => false],
            63 => ['code' => 'EN_LIQUIDACION', 'name' => 'EN PROCESO DE LIQUIDACIÓN', 'stage' => 'LIQUIDACION', 'color' => 'emerald', 'term' => false],
            64 => ['code' => 'LIQUIDADO_ARCHIVADO', 'name' => 'LIQUIDADO Y ARCHIVADO', 'stage' => 'ARCHIVADO', 'color' => 'gray', 'term' => true],
            99 => ['code' => 'RECHAZADO', 'name' => 'SOLICITUD NEGADA', 'stage' => 'RECHAZADO', 'color' => 'rose', 'term' => true],
        ];

        $statusModels = [];
        foreach ($statusesData as $id => $s) {
            $statusModels[$id] = CreditStatus::updateOrCreate(
                ['id' => $id],
                [
                    'code' => $s['code'],
                    'name' => $s['name'],
                    'stage' => $s['stage'],
                    'badge_color' => $s['color'],
                    'is_terminal' => $s['term'],
                ]
            );
        }

        // Transiciones del flujo bancario
        $transitions = [
            // Control a Carterizados
            ['from' => 46, 'to' => 57, 'role' => 'analista_carterizados', 'name' => 'Recibir en Carterizados', 'type' => 'RECIBIR', 'color' => 'primary', 'icon' => 'folder-open', 'batch' => true],
            ['from' => 57, 'to' => 62, 'role' => 'analista_carterizados', 'name' => 'Pasar a Análisis', 'type' => 'AVANZAR', 'color' => 'info', 'icon' => 'arrow-right', 'batch' => true],
            ['from' => 62, 'to' => 60, 'role' => 'analista_carterizados', 'name' => 'Solicitar Recaudos Faltantes', 'type' => 'DIFERIR', 'color' => 'warning', 'icon' => 'clock', 'batch' => false],
            ['from' => 60, 'to' => 62, 'role' => 'analista_carterizados', 'name' => 'Reanudar Análisis', 'type' => 'AVANZAR', 'color' => 'info', 'icon' => 'play', 'batch' => false],
            ['from' => 62, 'to' => 61, 'role' => 'analista_carterizados', 'name' => 'Devolver a Control y Seguimiento', 'type' => 'DEVOLVER', 'color' => 'danger', 'icon' => 'undo', 'batch' => false],
            ['from' => 62, 'to' => 58, 'role' => 'analista_carterizados', 'name' => 'Enviar a Comité de Crédito', 'type' => 'AVANZAR', 'color' => 'success', 'icon' => 'send', 'batch' => true],

            // Comité de Crédito
            ['from' => 58, 'to' => 49, 'role' => 'comite', 'name' => 'Recibir en Comité', 'type' => 'RECIBIR', 'color' => 'primary', 'icon' => 'folder-open', 'batch' => true],
            ['from' => 49, 'to' => 74, 'role' => 'comite', 'name' => 'Diferir en Comité', 'type' => 'DIFERIR', 'color' => 'warning', 'icon' => 'clock', 'batch' => false],
            ['from' => 74, 'to' => 49, 'role' => 'comite', 'name' => 'Reconsiderar en Comité', 'type' => 'AVANZAR', 'color' => 'info', 'icon' => 'refresh-cw', 'batch' => false],
            ['from' => 49, 'to' => 162, 'role' => 'comite', 'name' => 'Devolver a Carterizados', 'type' => 'DEVOLVER', 'color' => 'danger', 'icon' => 'undo', 'batch' => false],
            ['from' => 49, 'to' => 87, 'role' => 'comite', 'name' => 'Aprobar y Enviar a VP', 'type' => 'APROBAR', 'color' => 'success', 'icon' => 'check-circle', 'batch' => true],
            ['from' => 49, 'to' => 99, 'role' => 'comite', 'name' => 'Negar en Comité', 'type' => 'NEGAR', 'color' => 'danger', 'icon' => 'x-circle', 'batch' => false],

            // Vicepresidencia de Crédito
            ['from' => 87, 'to' => 90, 'role' => 'vp_credito', 'name' => 'Aprobación Final VP', 'type' => 'APROBAR', 'color' => 'success', 'icon' => 'award', 'batch' => true],
            ['from' => 87, 'to' => 49, 'role' => 'vp_credito', 'name' => 'Regresar a Comité para Revisión', 'type' => 'DEVOLVER', 'color' => 'warning', 'icon' => 'undo', 'batch' => false],
            ['from' => 87, 'to' => 99, 'role' => 'vp_credito', 'name' => 'Negar por Vicepresidencia', 'type' => 'NEGAR', 'color' => 'danger', 'icon' => 'x-circle', 'batch' => false],

            // Liquidación y Archivo
            ['from' => 90, 'to' => 63, 'role' => 'admin', 'name' => 'Iniciar Liquidación', 'type' => 'LIQUIDAR', 'color' => 'primary', 'icon' => 'dollar-sign', 'batch' => true],
            ['from' => 63, 'to' => 64, 'role' => 'admin', 'name' => 'Liquidar y Archivar Expediente', 'type' => 'AVANZAR', 'color' => 'success', 'icon' => 'archive', 'batch' => true],
        ];

        foreach ($transitions as $t) {
            WorkflowTransition::updateOrCreate(
                [
                    'from_status_id' => $t['from'],
                    'to_status_id' => $t['to'],
                ],
                [
                    'allowed_role_id' => isset($roleModels[$t['role']]) ? $roleModels[$t['role']]->id : null,
                    'action_name' => $t['name'],
                    'action_type' => $t['type'],
                    'action_color' => $t['color'],
                    'icon' => $t['icon'],
                    'requires_observation' => true,
                    'is_batch_allowed' => $t['batch'],
                ]
            );
        }

        // =========================================================================
        // 6. CLIENTES (PERSONAS NATURALES Y JURÍDICAS)
        // =========================================================================
        $custEmpresa1 = Customer::updateOrCreate(
            ['document_type' => 'J', 'document_number' => '301234567'],
            [
                'customer_type' => 'JURIDICO',
                'business_name' => 'DISTRIBUIDORA AGROALIMENTARIA ANDINA C.A.',
                'email' => 'contacto@agroandina.com',
                'phone' => '0212-9998877',
                'address' => 'Zona Industrial La Yaguara, Edif. Central Piso 2',
                'state_id' => $edoDtto->id,
                'agency_id' => $agPrincipal->id,
            ]
        );

        $custEmpresa2 = Customer::updateOrCreate(
            ['document_type' => 'J', 'document_number' => '409876543'],
            [
                'customer_type' => 'JURIDICO',
                'business_name' => 'INVERSIONES Y SERVICIOS TECNOLÓGICOS DEL CENTRO S.A.',
                'email' => 'finanzas@invercentro.com',
                'phone' => '0241-5556677',
                'address' => 'C.C. Cristal, Nivel 3, Ofic. 312, Valencia',
                'state_id' => $edoCarabobo->id,
                'agency_id' => $agValencia->id,
            ]
        );

        $custPersona1 = Customer::updateOrCreate(
            ['document_type' => 'V', 'document_number' => '17456789'],
            [
                'customer_type' => 'NATURAL',
                'business_name' => 'FRANCISCO JAVIER RIVAS SILVA',
                'first_name' => 'Francisco Javier',
                'last_name' => 'Rivas Silva',
                'email' => 'francisco.rivas@gmail.com',
                'phone' => '0414-1234567',
                'address' => 'Residencias Parque Central, Torre Este',
                'state_id' => $edoDtto->id,
                'agency_id' => $agPrincipal->id,
            ]
        );

        $custPersona2 = Customer::updateOrCreate(
            ['document_type' => 'V', 'document_number' => '20111222'],
            [
                'customer_type' => 'NATURAL',
                'business_name' => 'CARMEN ROSA PARRA BLANCO',
                'first_name' => 'Carmen Rosa',
                'last_name' => 'Parra Blanco',
                'email' => 'carmen.parra@hotmail.com',
                'phone' => '0424-9876543',
                'address' => 'Sector 5 de Julio, Maracaibo',
                'state_id' => $edoZulia->id,
                'agency_id' => $agMaracaibo->id,
            ]
        );

        $custEmpresa3 = Customer::updateOrCreate(
            ['document_type' => 'J', 'document_number' => '501112223'],
            [
                'customer_type' => 'JURIDICO',
                'business_name' => 'CONSORCIO TEXTIL LATINOAMERICANO C.A.',
                'email' => 'admon@textillatino.com',
                'phone' => '0212-3334455',
                'address' => 'Boleita Norte, Calle Sanatorio',
                'state_id' => $edoMiranda->id,
                'agency_id' => $agChacao->id,
            ]
        );

        // =========================================================================
        // 7. SOLICITUDES DE CRÉDITO Y TRAZABILIDAD (AUDIT TRAIL)
        // =========================================================================
        $sampleRequests = [
            [
                'code' => 'SOL-2026-000101',
                'customer' => $custEmpresa1,
                'product' => $prodComercial,
                'subproduct' => $prodComercial->subproducts->first(),
                'modality' => $modRotativa,
                'agency' => $agPrincipal,
                'status_id' => 57, // RECIBIDO GG CARTERIZADOS
                'user' => $userModels['analista.carterizados@empresa.com'],
                'amount' => 450000.00,
                'currency' => 'USD',
                'rate' => 36.50,
                'uvc' => 12328.76,
                'dest' => 'Adquisición de Materia Prima y Maquinaria',
                'date' => Carbon::now()->subDays(12),
                'history' => [
                    ['status' => 1, 'action' => 'Registro de Solicitud en Agencia', 'obs' => 'Cliente presenta expediente completo en taquilla.'],
                    ['status' => 2, 'action' => 'Remisión a Control y Seguimiento', 'obs' => 'Expediente digitalizado y enviado a mesa de control.'],
                    ['status' => 46, 'action' => 'Asignación a Cartera de Clientes', 'obs' => 'Califica para Gerencia General de Clientes Carterizados.'],
                    ['status' => 57, 'action' => 'Recepción en Bandeja de Carterizados', 'obs' => 'Recibido por analista para informe técnico preliminar.'],
                ],
            ],
            [
                'code' => 'SOL-2026-000102',
                'customer' => $custEmpresa2,
                'product' => $prodGrandesEmp,
                'subproduct' => null,
                'modality' => $modPlazo,
                'agency' => $agValencia,
                'status_id' => 58, // ENVIADO A COMITE POR CARTERIZADOS
                'user' => $userModels['analista.carterizados@empresa.com'],
                'amount' => 850000.00,
                'currency' => 'USD',
                'rate' => 36.50,
                'uvc' => 23287.67,
                'dest' => 'Expansión de Centro de Datos y Fibra Óptica',
                'date' => Carbon::now()->subDays(20),
                'history' => [
                    ['status' => 46, 'action' => 'Asignado a Carterizados', 'obs' => 'Cliente con historial clase A.'],
                    ['status' => 57, 'action' => 'Recibido en Carterizados', 'obs' => 'Verificación financiera conforme.'],
                    ['status' => 62, 'action' => 'Análisis de Riesgo Aprobado', 'obs' => 'Ratios de solvencia y cobertura de deuda óptimos.'],
                    ['status' => 58, 'action' => 'Elevado a Comité de Crédito', 'obs' => 'Se remite dictamen favorable para aprobación del comité.'],
                ],
            ],
            [
                'code' => 'SOL-2026-000103',
                'customer' => $custPersona1,
                'product' => $prodConsumo,
                'subproduct' => $prodConsumo->subproducts->first(),
                'modality' => $modPlazo,
                'agency' => $agPrincipal,
                'status_id' => 42, // EN ANALISIS POR GG PERSONAS Y PYME
                'user' => $userModels['analista.pyme@empresa.com'],
                'amount' => 15000.00,
                'currency' => 'USD',
                'rate' => 36.50,
                'uvc' => 410.95,
                'dest' => 'Mejoras de Vivienda y Equipamiento',
                'date' => Carbon::now()->subDays(6),
                'history' => [
                    ['status' => 1, 'action' => 'Solicitud Ingresada', 'obs' => 'Recaudos de ingresos y nómina verificados.'],
                    ['status' => 41, 'action' => 'Recibido en PYME y Consumo', 'obs' => 'Asignado a analista.'],
                    ['status' => 42, 'action' => 'En Análisis de Capacidad de Pago', 'obs' => 'Validando historial en central de riesgos.'],
                ],
            ],
            [
                'code' => 'SOL-2026-000104',
                'customer' => $custPersona2,
                'product' => $prodMicro,
                'subproduct' => $prodMicro->subproducts->first(),
                'modality' => $modPlazo,
                'agency' => $agMaracaibo,
                'status_id' => 90, // APROBADO POR VP DE CRÉDITO
                'user' => $userModels['vp.credito@empresa.com'],
                'amount' => 25000.00,
                'currency' => 'USD',
                'rate' => 36.50,
                'uvc' => 684.93,
                'dest' => 'Emprendimiento Panadería Artesanal',
                'date' => Carbon::now()->subDays(30),
                'history' => [
                    ['status' => 1, 'action' => 'Recepción Maracaibo', 'obs' => 'Proyecto comunitario con aval social.'],
                    ['status' => 41, 'action' => 'Análisis Microcrédito', 'obs' => 'Flujo de caja proyectado positivo.'],
                    ['status' => 49, 'action' => 'Comité Aprobatorio', 'obs' => 'Aprobado por unanimidad en acta N° 45-2026.'],
                    ['status' => 87, 'action' => 'Remitido a VP', 'obs' => 'Enviado para firma digital de Vicepresidencia.'],
                    ['status' => 90, 'action' => 'Aprobado por Vicepresidencia', 'obs' => 'Aprobado formalmente. Listo para protocolización.'],
                ],
            ],
            [
                'code' => 'SOL-2026-000105',
                'customer' => $custEmpresa3,
                'product' => $prodComercial,
                'subproduct' => $prodComercial->subproducts->first(),
                'modality' => $modRotativa,
                'agency' => $agChacao,
                'status_id' => 46, // ENVIADO A CARTERIZADOS POR CONTROL (Pendiente por Recibir)
                'user' => $userModels['analista.carterizados@empresa.com'],
                'amount' => 320000.00,
                'currency' => 'USD',
                'rate' => 36.50,
                'uvc' => 8767.12,
                'dest' => 'Exportación de Prendas Textiles',
                'date' => Carbon::now()->subDays(2),
                'history' => [
                    ['status' => 1, 'action' => 'Recepción en Sucursal Chacao', 'obs' => 'Presentó recaudos y balance auditado.'],
                    ['status' => 46, 'action' => 'Derivado a Clientes Carterizados', 'obs' => 'En espera de recepción en bandeja del analista.'],
                ],
            ],
            [
                'code' => 'SOL-2026-000106',
                'customer' => $custEmpresa1,
                'product' => $prodComercial,
                'subproduct' => $prodComercial->subproducts->first(),
                'modality' => $modPlazo,
                'agency' => $agPrincipal,
                'status_id' => 64, // LIQUIDADO Y ARCHIVADO
                'user' => $userModels['admin@empresa.com'],
                'amount' => 120000.00,
                'currency' => 'USD',
                'rate' => 36.50,
                'uvc' => 3287.67,
                'dest' => 'Mantenimiento Preventivo de Planta',
                'date' => Carbon::now()->subDays(60),
                'history' => [
                    ['status' => 1, 'action' => 'Recepción', 'obs' => 'Expediente inicial.'],
                    ['status' => 57, 'action' => 'Recibido Carterizados', 'obs' => 'Conforme.'],
                    ['status' => 58, 'action' => 'Comité', 'obs' => 'Aprobado.'],
                    ['status' => 90, 'action' => 'Aprobado VP', 'obs' => 'Visto bueno legal.'],
                    ['status' => 63, 'action' => 'Liquidación', 'obs' => 'Abono realizado en cuenta bancaria.'],
                    ['status' => 64, 'action' => 'Archivado', 'obs' => 'Expediente digital resguardado con código de archivo AR-0092.'],
                ],
            ],
        ];

        foreach ($sampleRequests as $s) {
            $cr = CreditRequest::updateOrCreate(
                ['request_code' => $s['code']],
                [
                    'customer_id' => $s['customer']->id,
                    'product_id' => $s['product']->id,
                    'subproduct_id' => $s['subproduct']?->id,
                    'modality_id' => $s['modality']?->id,
                    'agency_id' => $s['agency']->id,
                    'current_status_id' => $s['status_id'],
                    'assigned_user_id' => $s['user']->id,
                    'registered_by_user_id' => $userModels['admin@empresa.com']->id,
                    'requested_amount' => $s['amount'],
                    'approved_amount' => in_array($s['status_id'], [90, 63, 64]) ? $s['amount'] : null,
                    'currency' => $s['currency'],
                    'exchange_rate' => $s['rate'],
                    'amount_uvc' => $s['uvc'],
                    'destination' => $s['dest'],
                    'public_entity' => false,
                    'request_date' => $s['date'],
                    'observations' => 'Solicitud procesada bajo el nuevo motor de Ruta de Crédito.',
                    'is_active' => true,
                ]
            );

            // Poblar historial
            $histCount = count($s['history']);
            foreach ($s['history'] as $idx => $h) {
                $histDate = Carbon::parse($s['date'])->addDays($idx * 2);
                CreditRequestHistory::create([
                    'credit_request_id' => $cr->id,
                    'from_status_id' => $idx > 0 ? $s['history'][$idx - 1]['status'] : null,
                    'to_status_id' => $h['status'],
                    'user_id' => $s['user']->id,
                    'action_performed' => $h['action'],
                    'observations' => $h['obs'],
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64)',
                    'created_at' => $histDate,
                ]);
            }
        }
    }
}
