<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditRequest;
use App\Models\CreditProduct;
use App\Models\CreditStatus;
use App\Models\Agency;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $products = CreditProduct::where('is_active', true)->orderBy('name')->get();
        $statuses = CreditStatus::orderBy('id')->get();
        $agencies = Agency::where('is_active', true)->orderBy('name')->get();

        $previewRequests = CreditRequest::with(['customer', 'product', 'agency', 'currentStatus'])
            ->orderBy('updated_at', 'desc')
            ->take(8)
            ->get();

        return view('reports.index', compact('products', 'statuses', 'agencies', 'previewRequests'));
    }

    /**
     * Exportación ultrarrápida en CSV streaming compatible con Excel y UTF-8 con BOM.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = CreditRequest::with([
            'customer',
            'product',
            'subproduct',
            'agency.state',
            'currentStatus',
            'assignedUser',
        ]);

        if ($request->filled('status_id')) {
            $query->where('current_status_id', $request->status_id);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('request_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('request_date', '<=', $request->date_to);
        }

        $filename = 'reporte_ruta_credito_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM para compatibilidad con Microsoft Excel en UTF-8
            fputs($handle, "\xEF\xBB\xBF");

            // Cabeceras
            fputcsv($handle, [
                'N° Solicitud',
                'Cédula / RIF',
                'Cliente / Razón Social',
                'Tipo Cliente',
                'Gerencia General Canónica',
                'Producto',
                'Subproducto',
                'Agencia',
                'Estado Territorial',
                'Estatus Actual',
                'Monto Solicitado',
                'Moneda',
                'Monto UVC',
                'Fecha Solicitud',
                'Analista Asignado',
            ], ';');

            $query->chunk(200, function ($records) use ($handle) {
                foreach ($records as $row) {
                    fputcsv($handle, [
                        $row->request_code,
                        $row->customer ? $row->customer->formatted_document : 'N/A',
                        $row->customer ? $row->customer->business_name : 'N/A',
                        $row->customer ? $row->customer->customer_type : 'N/A',
                        $row->gerencia_general,
                        $row->product ? $row->product->name : 'N/A',
                        $row->subproduct ? $row->subproduct->name : 'N/A',
                        $row->agency ? $row->agency->name : 'N/A',
                        $row->agency && $row->agency->state ? $row->agency->state->name : 'N/A',
                        $row->currentStatus ? $row->currentStatus->name : 'N/A',
                        number_format($row->requested_amount, 2, ',', '.'),
                        $row->currency,
                        $row->amount_uvc ? number_format($row->amount_uvc, 2, ',', '.') : '0,00',
                        $row->request_date ? $row->request_date->format('d/m/Y') : '',
                        $row->assignedUser ? $row->assignedUser->name : 'Sin Asignar',
                    ], ';');
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Resumen de expediente imprimible / PDF.
     */
    public function printSummary(Request $request, int $id)
    {
        $creditRequest = CreditRequest::with([
            'customer.state.region',
            'product.subproducts',
            'subproduct',
            'modality',
            'agency.state',
            'currentStatus',
            'assignedUser.role',
            'registeredByUser',
            'histories.fromStatus',
            'histories.toStatus',
            'histories.user.role',
            'legalDocuments',
        ])->findOrFail($id);

        return view('reports.print_summary', compact('creditRequest'));
    }
}
