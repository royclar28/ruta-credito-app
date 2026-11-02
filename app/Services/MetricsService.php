<?php

namespace App\Services;

use App\Models\CreditRequest;
use App\Models\CreditStatus;
use App\Models\CreditProduct;
use App\Models\State;
use Illuminate\Support\Facades\DB;

class MetricsService
{
    /**
     * Aplica filtros territoriales y temporales a una query de solicitudes.
     */
    protected function applyFilters($query, array $filters = [])
    {
        if (!empty($filters['region_id'])) {
            $query->whereHas('agency.state', function ($q) use ($filters) {
                $q->where('region_id', $filters['region_id']);
            });
        }

        if (!empty($filters['state_id'])) {
            $query->whereHas('agency', function ($q) use ($filters) {
                $q->where('state_id', $filters['state_id']);
            });
        }

        if (!empty($filters['agency_id'])) {
            $query->where('agency_id', $filters['agency_id']);
        }

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (!empty($filters['status_id'])) {
            $query->where('current_status_id', $filters['status_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('request_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('request_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Scorecards ejecutivos principales del Dashboard.
     */
    public function getScorecards(array $filters = []): array
    {
        $baseQuery = $this->applyFilters(CreditRequest::query(), $filters);

        $totalSolicitudes = (clone $baseQuery)->count();
        $totalMontoUsd = (clone $baseQuery)->sum('requested_amount');
        $totalMontoUvc = (clone $baseQuery)->sum('amount_uvc');

        $aprobadosCount = (clone $baseQuery)->whereIn('current_status_id', [90, 63, 64])->count();
        $aprobadosMonto = (clone $baseQuery)->whereIn('current_status_id', [90, 63, 64])->sum('requested_amount');

        $enAnalisisCount = (clone $baseQuery)->whereIn('current_status_id', [57, 62, 41, 42])->count();
        $enComiteCount = (clone $baseQuery)->whereIn('current_status_id', [58, 49, 74, 87])->count();

        $tasaAprobacion = $totalSolicitudes > 0 ? round(($aprobadosCount / $totalSolicitudes) * 100, 1) : 0;

        return [
            'total_solicitudes' => $totalSolicitudes,
            'total_monto_usd' => $totalMontoUsd,
            'total_monto_uvc' => $totalMontoUvc,
            'aprobados_count' => $aprobadosCount,
            'aprobados_monto' => $aprobadosMonto,
            'en_analisis_count' => $enAnalisisCount,
            'en_comite_count' => $enComiteCount,
            'tasa_aprobacion' => $tasaAprobacion,
        ];
    }

    /**
     * Distribución por estatus para Gráfico Donut / Bar.
     */
    public function getStatusDistribution(array $filters = []): array
    {
        $query = $this->applyFilters(CreditRequest::query(), $filters);

        $results = $query->select('current_status_id', DB::raw('count(*) as count'), DB::raw('sum(requested_amount) as total_amount'))
            ->groupBy('current_status_id')
            ->with('currentStatus')
            ->get();

        $labels = [];
        $series = [];
        $colors = [];

        foreach ($results as $item) {
            $labels[] = $item->currentStatus?->name ?? 'Desconocido';
            $series[] = (int) $item->count;
            $colors[] = $item->currentStatus?->badge_color ?? '#3b82f6';
        }

        return [
            'labels' => $labels,
            'series' => $series,
            'raw' => $results,
        ];
    }

    /**
     * Distribución por categoría de producto para Gráfico de Barras.
     */
    public function getProductDistribution(array $filters = []): array
    {
        $query = $this->applyFilters(CreditRequest::query(), $filters);

        $results = $query->join('credit_products', 'credit_requests.product_id', '=', 'credit_products.id')
            ->select('credit_products.name as product_name', DB::raw('count(*) as count'), DB::raw('sum(credit_requests.requested_amount) as total_amount'))
            ->groupBy('credit_products.name')
            ->orderByDesc('count')
            ->get();

        return [
            'categories' => $results->pluck('product_name')->toArray(),
            'counts' => $results->pluck('count')->map(fn($v) => (int)$v)->toArray(),
            'amounts' => $results->pluck('total_amount')->map(fn($v) => (float)$v)->toArray(),
        ];
    }

    /**
     * Distribución territorial por Estado.
     */
    public function getGeographicDistribution(array $filters = []): array
    {
        $query = $this->applyFilters(CreditRequest::query(), $filters);

        $results = $query->join('agencies', 'credit_requests.agency_id', '=', 'agencies.id')
            ->join('states', 'agencies.state_id', '=', 'states.id')
            ->select('states.name as state_name', DB::raw('count(*) as count'), DB::raw('sum(credit_requests.requested_amount) as total_amount'))
            ->groupBy('states.name')
            ->orderByDesc('count')
            ->get();

        return [
            'states' => $results->pluck('state_name')->toArray(),
            'counts' => $results->pluck('count')->map(fn($v) => (int)$v)->toArray(),
            'amounts' => $results->pluck('total_amount')->map(fn($v) => (float)$v)->toArray(),
        ];
    }

    /**
     * Tabla operativa paginada con búsqueda y relaciones cargadas.
     */
    public function getOperationalTable(array $filters = [], int $perPage = 10)
    {
        $query = CreditRequest::with([
            'customer',
            'product',
            'subproduct',
            'modality',
            'agency.state.region',
            'currentStatus',
            'assignedUser',
        ]);

        $this->applyFilters($query, $filters);

        if (!empty($filters['search'])) {
            $term = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('request_code', 'ilike', $term)
                  ->orWhereHas('customer', function ($sub) use ($term) {
                      $sub->where('document_number', 'ilike', $term)
                          ->orWhere('business_name', 'ilike', $term);
                  });
            });
        }

        if (!empty($filters['queue'])) {
            // Bandejas especializadas
            switch ($filters['queue']) {
                case 'carterizados':
                    $query->whereIn('current_status_id', [46, 57, 62, 60, 61, 58, 74, 162]);
                    break;
                case 'pyme':
                    $query->whereIn('current_status_id', [41, 42]);
                    break;
                case 'comite':
                    $query->whereIn('current_status_id', [58, 49, 74, 162]);
                    break;
                case 'vp':
                    $query->whereIn('current_status_id', [87, 90]);
                    break;
                case 'aprobados':
                    $query->whereIn('current_status_id', [90, 63, 64]);
                    break;
            }
        }

        return $query->orderBy('updated_at', 'desc')->paginate($perPage);
    }
}
