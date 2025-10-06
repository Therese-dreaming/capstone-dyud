<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceRequest;
use App\Models\RepairRequest;
use Illuminate\Support\Facades\Auth;

class MyRequestsController extends Controller
{
    /**
     * Display a unified view of user's maintenance and repair requests
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's maintenance requests
        $maintenanceRequests = MaintenanceRequest::where('requester_id', $user->id)
            ->with(['location', 'approvedBy', 'rejectedBy', 'checklist'])
            ->orderByDesc('created_at')
            ->get();
        
        // Get user's repair requests
        $repairRequests = RepairRequest::where('requester_id', $user->id)
            ->with(['asset.location', 'asset.category', 'approvedBy', 'rejectedBy', 'completedBy'])
            ->orderByDesc('created_at')
            ->get();
        
        return view('my-requests.index', compact('maintenanceRequests', 'repairRequests'));
    }
}
