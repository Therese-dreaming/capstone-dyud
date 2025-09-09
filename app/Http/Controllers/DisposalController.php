<?php

namespace App\Http\Controllers;

use App\Models\Dispose;
use App\Exports\DisposalsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class DisposalController extends Controller
{
    public function history(Request $request)
    {
        $query = Dispose::with(['asset.category', 'asset.location']);

        // Apply filters
        if ($request->filled('asset_search')) {
            $search = $request->asset_search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('asset_code', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('disposal_date_from')) {
            $query->whereDate('disposal_date', '>=', $request->disposal_date_from);
        }

        if ($request->filled('disposal_date_to')) {
            $query->whereDate('disposal_date', '<=', $request->disposal_date_to);
        }

        if ($request->filled('disposed_by')) {
            $query->where('disposed_by', 'LIKE', "%{$request->disposed_by}%");
        }

        $disposals = $query->orderBy('disposal_date', 'desc')->paginate(15);
        
        return view('disposals.history', compact('disposals'));
    }

    public function export(Request $request)
    {
        $query = Dispose::with(['asset.category', 'asset.location']);

        if ($request->filled('asset_search')) {
            $search = $request->asset_search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('asset_code', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('disposal_date_from')) {
            $query->whereDate('disposal_date', '>=', $request->disposal_date_from);
        }

        if ($request->filled('disposal_date_to')) {
            $query->whereDate('disposal_date', '<=', $request->disposal_date_to);
        }

        if ($request->filled('disposed_by')) {
            $query->where('disposed_by', 'LIKE', "%{$request->disposed_by}%");
        }

        $disposals = $query->orderBy('disposal_date', 'desc')->get();

        return Excel::download(new DisposalsExport($disposals), 'disposals.xlsx');
    }
}
