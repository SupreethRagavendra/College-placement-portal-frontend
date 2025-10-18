@extends('layouts.admin')

@section('title', 'Pending Students - KIT Training Portal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0" style="color: var(--text-dark); font-weight: 700;">
        <i class="fas fa-user-clock me-2" style="color: var(--primary-red);"></i>
        Pending Training Portal Registrations
    </h1>
    <div class="badge fs-6" style="background: linear-gradient(135deg, #ffca3a 0%, #f4a600 100%); color: var(--text-dark); padding: 10px 20px; border-radius: 20px; font-weight: 600;">
        {{ $pendingStudents->total() }} Pending Approval{{ $pendingStudents->total() !== 1 ? 's' : '' }}
    </div>
</div>

@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; padding: 20px 25px; border: none;">
        <h5 class="card-title mb-0" style="font-weight: 700; font-size: 1.2rem;">
            <i class="fas fa-users-clock me-2"></i>Students Awaiting Approval
        </h5>
        @if($pendingStudents->count() > 0)
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm" onclick="selectAll()" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 6px 15px; border-radius: 8px; font-weight: 500; transition: all 0.3s;">
                    <i class="fas fa-check-square me-1"></i>Select All
                </button>
                <button type="button" class="btn btn-sm ms-2" onclick="clearSelection()" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 6px 15px; border-radius: 8px; font-weight: 500; transition: all 0.3s;">
                    <i class="fas fa-square me-1"></i>Clear
                </button>
            </div>
        @endif
    </div>
    <div class="card-body p-0">
        @if($pendingStudents->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                            </th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registered At</th>
                            <th width="200" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingStudents as $student)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input student-checkbox" value="{{ $student->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); width: 40px; height: 40px; font-weight: 600; font-size: 16px;">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0" style="font-weight: 600; color: var(--text-dark);">{{ $student->name }}</h6>
                                            <small class="text-muted">Training Portal Student</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $student->email }}</span>
                                    @if($student->email_verified_at)
                                        <i class="fas fa-check-circle text-success ms-2" title="Email verified"></i>
                                    @else
                                        <i class="fas fa-exclamation-triangle text-warning ms-2" title="Email not verified"></i>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <span class="text-muted">{{ $student->created_at->format('M d, Y') }}</span>
                                        <br>
                                        <small class="text-muted">{{ $student->created_at->format('H:i') }} ({{ $student->created_at->diffForHumans() }})</small>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <form action="{{ route('admin.approve-student', $student->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" title="Approve Student" onclick="return confirm('Are you sure you want to approve {{ $student->name }} for the training portal?')" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; transition: all 0.3s;">
                                                <i class="fas fa-check me-1"></i>Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.reject-student', $student->id) }}" method="POST" class="d-inline ms-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" title="Reject Student" onclick="return confirm('Are you sure you want to reject {{ $student->name }}? This will permanently delete their account from the training portal.')" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; transition: all 0.3s;">
                                                <i class="fas fa-times me-1"></i>Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($pendingStudents->count() > 1)
                <div class="card-footer" style="background: #f8f9fa; padding: 20px 25px; border-top: 2px solid #e9ecef;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span style="color: var(--text-dark); font-weight: 600;">
                                Selected: <span id="selectedCount" style="color: var(--primary-red); font-size: 1.1rem;">0</span> students
                            </span>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn" id="bulkApproveBtn" disabled onclick="bulkApprove()" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; transition: all 0.3s; margin-right: 10px;">
                                <i class="fas fa-check me-2"></i>Approve Selected
                            </button>
                            <button type="button" class="btn" id="bulkRejectBtn" disabled onclick="bulkReject()" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; transition: all 0.3s;">
                                <i class="fas fa-times me-2"></i>Reject Selected
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x mb-3" style="color: #28a745;"></i>
                <h5 style="color: var(--text-dark); font-weight: 700; margin-top: 20px;">No Pending Students</h5>
                <p class="text-muted" style="font-size: 1rem;">All students have been processed! New training portal registrations will appear here.</p>
                <a href="{{ route('admin.dashboard') }}" class="btn mt-3" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
                    <i class="fas fa-tachometer-alt me-2"></i>Back to Dashboard
                </a>
            </div>
        @endif
    </div>
    @if (method_exists($pendingStudents, 'links') && $pendingStudents->hasPages())
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-center">
                {{ $pendingStudents->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Bulk Action Forms -->
<form id="bulkApproveForm" action="{{ route('admin.bulk-approve') }}" method="POST" style="display: none;">
    @csrf
</form>

<form id="bulkRejectForm" action="{{ route('admin.bulk-reject') }}" method="POST" style="display: none;">
    @csrf
</form>
@endsection

@push('scripts')
<style>
:root {
    --primary-red: #DC143C;
    --dark-red: #B91C1C;
    --light-red: #EF4444;
    --black: #1A1A1A;
    --dark-gray: #2D2D2D;
    --white: #FFFFFF;
    --light-gray: #F5F5F5;
    --text-dark: #333333;
    --accent-red: #FF0000;
}

.avatar-sm {
    width: 40px;
    height: 40px;
    font-weight: 600;
    font-size: 16px;
}

.table th {
    border-top: none;
    font-weight: 700;
    color: var(--text-dark);
    background-color: #f8f9fa;
    padding: 15px 12px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: rgba(220, 20, 60, 0.05) !important;
}

.btn-group .btn {
    border-radius: 8px;
}

.btn-group .btn:not(:last-child) {
    margin-right: 4px;
}

/* Button hover effects */
button[onclick*="selectAll"]:hover,
button[onclick*="clearSelection"]:hover {
    background: rgba(255,255,255,0.35) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

button[onclick*="bulkApprove"]:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

button[onclick*="bulkReject"]:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(220, 20, 60, 0.4);
}

button[title*="Approve"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
}

button[title*="Reject"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(220, 20, 60, 0.4);
}

#bulkApproveBtn:disabled,
#bulkRejectBtn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

.form-check-input:checked {
    background-color: var(--primary-red);
    border-color: var(--primary-red);
}

.form-check-input:focus {
    border-color: var(--primary-red);
    box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.25);
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border: none;
    border-left: 4px solid #28a745;
    border-radius: 10px;
    color: #155724;
    font-weight: 500;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border: none;
    border-left: 4px solid var(--primary-red);
    border-radius: 10px;
    color: #721c24;
    font-weight: 500;
}
</style>

<script>
function selectAll() {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    selectAllCheckbox.checked = true;
    updateSelectedCount();
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    selectAllCheckbox.checked = false;
    updateSelectedCount();
}

function updateSelectedCount() {
    const selectedCheckboxes = document.querySelectorAll('.student-checkbox:checked');
    const count = selectedCheckboxes.length;
    
    document.getElementById('selectedCount').textContent = count;
    
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');
    const bulkRejectBtn = document.getElementById('bulkRejectBtn');
    
    if (bulkApproveBtn) bulkApproveBtn.disabled = count === 0;
    if (bulkRejectBtn) bulkRejectBtn.disabled = count === 0;
}

function getSelectedStudentIds() {
    const selectedCheckboxes = document.querySelectorAll('.student-checkbox:checked');
    return Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
}

function bulkApprove() {
    const selectedIds = getSelectedStudentIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one student to approve.');
        return;
    }
    
    if (confirm(`Are you sure you want to approve ${selectedIds.length} student(s)?`)) {
        const form = document.getElementById('bulkApproveForm');
        
        // Add student IDs to form
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'student_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        form.submit();
    }
}

function bulkReject() {
    const selectedIds = getSelectedStudentIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one student to reject.');
        return;
    }
    
    if (confirm(`Are you sure you want to reject ${selectedIds.length} student(s)? This action cannot be undone.`)) {
        const form = document.getElementById('bulkRejectForm');
        
        // Add student IDs to form
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'student_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        form.submit();
    }
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Handle select all checkbox
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }
    
    // Handle individual checkboxes
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    studentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();
            
            // Update select all checkbox state
            const totalCheckboxes = studentCheckboxes.length;
            const checkedCheckboxes = document.querySelectorAll('.student-checkbox:checked').length;
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = totalCheckboxes === checkedCheckboxes;
                selectAllCheckbox.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
            }
        });
    });
    
    // Initialize count
    updateSelectedCount();
});
</script>
@endpush