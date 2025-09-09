@extends('layouts.admin')

@section('content')
<style>
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .card-hover {
        transition: all 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full mb-4">
                <i class="fas fa-tools text-white text-2xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Maintenance Requests</h1>
            <p class="text-lg text-gray-600">Review and manage maintenance requests from users</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg animate-fade-in">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg animate-fade-in">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $requests->where('status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-check text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Approved</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $requests->where('status', 'approved')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-handshake text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Acknowledged</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $requests->where('status', 'acknowledged')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-times text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Rejected</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $requests->where('status', 'rejected')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Table Header -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">All Requests</h2>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-filter text-gray-400"></i>
                        <span class="text-sm text-gray-600">Total: {{ $requests->total() }}</span>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Details</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($requests as $req)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <!-- Request Details -->
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="text-sm font-medium text-gray-900">{{ $req->school_year }}</div>
                                    <div class="text-sm text-gray-600">{{ $req->department }}</div>
                                    <div class="text-xs text-gray-500">{{ $req->created_at->format('M d, Y H:i') }}</div>
                                    @if($req->program)
                                        <div class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full inline-block">{{ $req->program }}</div>
                                    @endif
                                </div>
                            </td>

                            <!-- Requester -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">{{ substr($req->requester->name, 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $req->requester->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $req->instructor_name }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Location -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $req->location->building }}</div>
                                <div class="text-sm text-gray-600">Floor {{ $req->location->floor }} - Room {{ $req->location->room }}</div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{
                                    $req->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                    ($req->status === 'approved' ? 'bg-blue-100 text-blue-800' : 
                                    ($req->status === 'rejected' ? 'bg-red-100 text-red-800' : 
                                    ($req->status === 'acknowledged' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')))
                                }}">
                                    <i class="fas {{
                                        $req->status === 'pending' ? 'fa-clock' : 
                                        ($req->status === 'approved' ? 'fa-check' : 
                                        ($req->status === 'rejected' ? 'fa-times' : 
                                        ($req->status === 'acknowledged' ? 'fa-handshake' : 'fa-question')))
                                    }} mr-1"></i>
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <!-- View Details Link -->
                                    <a href="{{ route('maintenance-requests.show', $req) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                    
                                    @if($req->status === 'pending')
                                        <button onclick="openRejectModal({{ $req->id }}, '{{ $req->requester->name }}', '{{ $req->location->building }} - Floor {{ $req->location->floor }} - Room {{ $req->location->room }}')" 
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                            <i class="fas fa-times mr-1"></i> Reject
                                        </button>
                                        <button onclick="openApproveModal({{ $req->id }}, '{{ $req->requester->name }}', '{{ $req->location->building }} - Floor {{ $req->location->floor }} - Room {{ $req->location->room }}', '{{ $req->school_year }}', '{{ $req->department }}', '{{ $req->instructor_name }}')" 
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </button>
                                    @elseif($req->status === 'approved')
                                        <a href="{{ route('maintenance-requests.acknowledge', $req) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                            <i class="fas fa-handshake mr-1"></i> Acknowledge (GSU)
                                        </a>
                                    @elseif($req->status === 'acknowledged' && $req->maintenance_checklist_id)
                                        <a href="{{ route('maintenance-checklists.show', $req->maintenance_checklist_id) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                            <i class="fas fa-clipboard-check mr-1"></i> View Checklist
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No maintenance requests found</h3>
                                    <p class="text-gray-500">When users submit maintenance requests, they will appear here.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <i class="fas fa-times text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Reject Maintenance Request</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center mb-4">
                    Are you sure you want to reject this maintenance request?
                </p>
                <div class="bg-gray-50 rounded-lg p-3 mb-4">
                    <p class="text-sm font-medium text-gray-900" id="rejectRequesterName"></p>
                    <p class="text-xs text-gray-600" id="rejectLocation"></p>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
                        <textarea name="rejection_reason" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                                  placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            Reject Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Approve Maintenance Request</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center mb-4">
                    This will create a maintenance checklist and notify GSU.
                </p>
                <div class="bg-green-50 rounded-lg p-3 mb-4">
                    <p class="text-sm font-medium text-gray-900" id="approveRequesterName"></p>
                    <p class="text-xs text-gray-600" id="approveLocation"></p>
                    <p class="text-xs text-gray-600" id="approveDetails"></p>
                </div>
                <form id="approveForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Optional)</label>
                        <textarea name="admin_notes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                  placeholder="Add any additional notes for GSU..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeApproveModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                            Approve Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openRejectModal(requestId, requesterName, location) {
    document.getElementById('rejectRequesterName').textContent = requesterName;
    document.getElementById('rejectLocation').textContent = location;
    document.getElementById('rejectForm').action = `/admin/maintenance-requests/${requestId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function openApproveModal(requestId, requesterName, location, schoolYear, department, instructor) {
    document.getElementById('approveRequesterName').textContent = requesterName;
    document.getElementById('approveLocation').textContent = location;
    document.getElementById('approveDetails').textContent = `${schoolYear} - ${department} - ${instructor}`;
    document.getElementById('approveForm').action = `/admin/maintenance-requests/${requestId}/approve`;
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const rejectModal = document.getElementById('rejectModal');
    const approveModal = document.getElementById('approveModal');
    
    if (event.target === rejectModal) {
        closeRejectModal();
    }
    if (event.target === approveModal) {
        closeApproveModal();
    }
}
</script>
@endsection


