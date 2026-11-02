<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditRequest;

class ConsultationController extends Controller
{
    /**
     * Vista de búsqueda unificada y auditoría de solicitudes.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $creditRequest = null;
        $recentRequests = [];

        if (!empty($search)) {
            $term = trim($search);
            $creditRequest = CreditRequest::with([
                'customer.state',
                'product',
                'subproduct',
                'modality',
                'agency.state',
                'currentStatus',
                'assignedUser',
                'histories.fromStatus',
                'histories.toStatus',
                'histories.user.role',
            ])->where('request_code', 'ilike', $term)
              ->orWhereHas('customer', function ($q) use ($term) {
                  $q->where('document_number', $term);
              })
              ->first();
        }

        if (!$creditRequest) {
            $recentRequests = CreditRequest::with(['customer', 'product', 'currentStatus', 'agency'])
                ->orderBy('updated_at', 'desc')
                ->take(6)
                ->get();
        }

        return view('consultation.index', compact('creditRequest', 'search', 'recentRequests'));
    }
}
