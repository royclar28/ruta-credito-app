<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MetricsService;
use App\Models\Region;
use App\Models\State;
use App\Models\Agency;
use App\Models\CreditProduct;
use App\Models\CreditStatus;

class DashboardController extends Controller
{
    protected MetricsService $metricsService;

    public function __construct(MetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['region_id', 'state_id', 'agency_id', 'product_id', 'status_id', 'date_from', 'date_to', 'search']);

        $scorecards = $this->metricsService->getScorecards($filters);
        $statusDistribution = $this->metricsService->getStatusDistribution($filters);
        $productDistribution = $this->metricsService->getProductDistribution($filters);
        $geographicDistribution = $this->metricsService->getGeographicDistribution($filters);
        $requests = $this->metricsService->getOperationalTable($filters, 10);

        // Catálogos para filtros
        $regions = Region::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $agencies = Agency::orderBy('name')->get();
        $products = CreditProduct::where('is_active', true)->orderBy('name')->get();
        $statuses = CreditStatus::orderBy('id')->get();

        return view('dashboard.index', compact(
            'scorecards',
            'statusDistribution',
            'productDistribution',
            'geographicDistribution',
            'requests',
            'regions',
            'states',
            'agencies',
            'products',
            'statuses',
            'filters'
        ));
    }

    public function apiMetrics(Request $request)
    {
        $filters = $request->only(['region_id', 'state_id', 'agency_id', 'product_id', 'status_id', 'date_from', 'date_to', 'search']);

        return response()->json([
            'scorecards' => $this->metricsService->getScorecards($filters),
            'status_distribution' => $this->metricsService->getStatusDistribution($filters),
            'product_distribution' => $this->metricsService->getProductDistribution($filters),
            'geographic_distribution' => $this->metricsService->getGeographicDistribution($filters),
        ]);
    }
}
