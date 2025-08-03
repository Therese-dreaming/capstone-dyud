<!-- Cancel Request Modal -->
<div id="cancelRequestModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
        <button onclick="closeModal('cancelRequestModal')" class="absolute top-3 right-3 text-gray-400 hover:text-red-800 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex flex-col items-center">
            <div class="bg-red-100 text-red-800 rounded-full p-4 mb-4">
                <i class="fas fa-exclamation-triangle text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Cancel Borrowing Request</h3>
            <p class="text-gray-600 mb-6 text-center">Are you sure you want to cancel this borrowing request? This action cannot be undone.</p>
            <div class="flex gap-3 w-full">
                <button id="cancelRequestConfirm" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Yes, Cancel Request
                </button>
                <button onclick="closeModal('cancelRequestModal')" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> No, Keep Request
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Request Modal -->
<div id="deleteRequestModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
        <button onclick="closeModal('deleteRequestModal')" class="absolute top-3 right-3 text-gray-400 hover:text-red-800 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex flex-col items-center">
            <div class="bg-red-100 text-red-800 rounded-full p-4 mb-4">
                <i class="fas fa-trash text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Delete Borrowing Request</h3>
            <p class="text-gray-600 mb-6 text-center">Are you sure you want to permanently delete this borrowing request? This action cannot be undone.</p>
            <div class="flex gap-3 w-full">
                <button id="deleteRequestConfirm" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-trash"></i> Yes, Delete Request
                </button>
                <button onclick="closeModal('deleteRequestModal')" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> No, Keep Request
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Asset Details Modal -->
<div id="assetDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-2xl relative">
        <button onclick="closeModal('assetDetailsModal')" class="absolute top-3 right-3 text-gray-400 hover:text-red-800 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex flex-col">
            <div class="flex items-center mb-6">
                <div class="bg-blue-100 text-blue-800 rounded-full p-3 mr-4">
                    <i class="fas fa-box text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Asset Details</h3>
            </div>
            <div id="assetDetailsContent" class="space-y-4">
                <!-- Asset details will be loaded here -->
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="closeModal('assetDetailsModal')" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
        <button onclick="closeModal('successModal')" class="absolute top-3 right-3 text-gray-400 hover:text-green-800 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex flex-col items-center">
            <div class="bg-green-100 text-green-800 rounded-full p-4 mb-4">
                <i class="fas fa-check text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Success!</h3>
            <p id="successMessage" class="text-gray-600 mb-6 text-center">Operation completed successfully.</p>
            <button onclick="closeModal('successModal')" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-check"></i> OK
            </button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md relative">
        <button onclick="closeModal('errorModal')" class="absolute top-3 right-3 text-gray-400 hover:text-red-800 text-xl">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex flex-col items-center">
            <div class="bg-red-100 text-red-800 rounded-full p-4 mb-4">
                <i class="fas fa-times text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2 text-gray-800">Error</h3>
            <p id="errorMessage" class="text-gray-600 mb-6 text-center">An error occurred. Please try again.</p>
            <button onclick="closeModal('errorModal')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fas fa-times"></i> OK
            </button>
        </div>
    </div>
</div>

<script>
// Modal utility functions
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function showSuccessModal(message) {
    document.getElementById('successMessage').textContent = message;
    openModal('successModal');
}

function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    openModal('errorModal');
}

// Close modals when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('[id$="Modal"]');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
    });
});

// Cancel request modal functionality
function openCancelRequestModal(requestId) {
    const confirmBtn = document.getElementById('cancelRequestConfirm');
    confirmBtn.onclick = function() {
        // Submit the cancel form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/user/borrowings/${requestId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    };
    openModal('cancelRequestModal');
}

// Delete request modal functionality
function openDeleteRequestModal(requestId) {
    const confirmBtn = document.getElementById('deleteRequestConfirm');
    confirmBtn.onclick = function() {
        // Submit the delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/borrowings/${requestId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    };
    openModal('deleteRequestModal');
}

// Asset details modal functionality
function openAssetDetailsModal(assetId) {
    // Load asset details via AJAX
    fetch(`/api/assets/${assetId}`)
        .then(response => response.json())
        .then(data => {
            const content = document.getElementById('assetDetailsContent');
            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Asset Name</label>
                        <p class="mt-1 text-sm text-gray-900">${data.name}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Asset Code</label>
                        <p class="mt-1 text-sm text-gray-900 font-mono">${data.asset_code}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <p class="mt-1 text-sm text-gray-900">${data.category.name}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                            ${data.status === 'Available' ? 'bg-green-100 text-green-800' : 
                              (data.status === 'In Use' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')}">
                            ${data.status}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Condition</label>
                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full 
                            ${data.condition === 'Good' ? 'bg-green-100 text-green-800' : 
                              (data.condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')}">
                            ${data.condition}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Location</label>
                        <p class="mt-1 text-sm text-gray-900">
                            ${data.location.building} - Floor ${data.location.floor} - Room ${data.location.room}
                        </p>
                    </div>
                </div>
                ${data.description ? `
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <p class="mt-1 text-sm text-gray-900">${data.description}</p>
                </div>
                ` : ''}
            `;
            openModal('assetDetailsModal');
        })
        .catch(error => {
            console.error('Error loading asset details:', error);
            showErrorModal('Failed to load asset details');
        });
}
</script> 