@extends('layouts.admin')

@section('title', 'Rejected Students - KIT Training Portal')
@section('page-title', 'Rejected Students Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0" style="color: var(--text-dark); font-weight: 700;">
        <i class="fas fa-times-circle me-2" style="color: #dc3545;"></i>
        Rejected Students
    </h1>
    <div class="badge fs-6" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 10px 20px; border-radius: 20px; font-weight: 600;">
        {{ $rejectedStudents->total() }} Rejected Student{{ $rejectedStudents->total() !== 1 ? 's' : '' }}
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
    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 20px 25px; border: none;">
        <h5 class="card-title mb-0" style="font-weight: 700; font-size: 1.2rem;">
            <i class="fas fa-times-circle me-2"></i>Rejected Students
        </h5>
        @if($rejectedStudents->count() > 0)
            <div class="btn-group" role="group">
                <a href="{{ route('admin.pending-students') }}" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 6px 15px; border-radius: 8px; font-weight: 500; transition: all 0.3s; text-decoration: none;">
                    <i class="fas fa-clock me-1"></i>View Pending
                </a>
                <a href="{{ route('admin.approved-students') }}" class="btn btn-sm ms-2" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 6px 15px; border-radius: 8px; font-weight: 500; transition: all 0.3s; text-decoration: none;">
                    <i class="fas fa-check-circle me-1"></i>View Approved
                </a>
            </div>
        @endif
    </div>
    <div class="card-body p-0">
        @if($rejectedStudents->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registration Date</th>
                            <th>Rejection Date</th>
                            <th>Rejection Reason</th>
                            <th width="120" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rejectedStudents as $student)
                            <tr data-student-id="{{ $student->id }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); width: 40px; height: 40px; font-weight: 600; font-size: 16px;">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--text-dark);">{{ $student->name }}</div>
                                            <small class="text-muted">Former Student</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $student->email }}</span>
                                    @if($student->email_verified_at)
                                        <i class="fas fa-check-circle text-success ms-2" title="Email was verified"></i>
                                    @else
                                        <i class="fas fa-exclamation-triangle text-warning ms-2" title="Email was not verified"></i>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $student->created_at->format('M d, Y h:i A') }}
                                    </small>
                                </td>
                                <td>
                                    <small class="text-danger">
                                        {{ $student->admin_rejected_at ? $student->admin_rejected_at->format('M d, Y h:i A') : 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    @if($student->rejection_reason)
                                        <span class="text-muted">{{ Str::limit($student->rejection_reason, 50) }}</span>
                                        @if(strlen($student->rejection_reason) > 50)
                                            <button class="btn btn-link btn-sm p-0 ms-2" onclick="showFullReason('{{ addslashes($student->rejection_reason) }}')" title="View full reason">
                                                <i class="fas fa-expand-alt"></i>
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted fst-italic">No reason provided</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm" 
                                                onclick="viewStudentDetails({{ $student->id }})"
                                                title="View Details"
                                                style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 6px 12px; border-radius: 8px; font-weight: 500; transition: all 0.3s;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm ms-2" 
                                                onclick="restoreStudent({{ $student->id }}, '{{ addslashes($student->name) }}')"
                                                title="Restore Student"
                                                style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none; padding: 6px 12px; border-radius: 8px; font-weight: 500; transition: all 0.3s;">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($rejectedStudents->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-center">
                        {{ $rejectedStudents->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-times-circle fa-3x mb-3" style="color: #dc3545;"></i>
                <h5 style="color: var(--text-dark); font-weight: 700; margin-top: 20px;">No Rejected Students</h5>
                <p class="text-muted" style="font-size: 1rem;">No students have been rejected. All applications are either pending or approved.</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('admin.pending-students') }}" class="btn mt-3" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s; margin-right: 10px;">
                        <i class="fas fa-clock me-2"></i>View Pending Students
                    </a>
                    <a href="{{ route('admin.approved-students') }}" class="btn mt-3" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
                        <i class="fas fa-check-circle me-2"></i>View Approved Students
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Student Details Modal -->
<div class="modal fade" id="studentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden; border: none;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none; padding: 20px 25px;">
                <h5 class="modal-title" style="font-weight: 700;">
                    <i class="fas fa-user-graduate me-2"></i>Rejected Student Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="studentDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border" style="color: var(--primary-red);" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div class="modal fade" id="rejectionReasonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden; border: none;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none; padding: 20px 25px;">
                <h5 class="modal-title" style="font-weight: 700;">
                    <i class="fas fa-info-circle me-2"></i>Rejection Reason
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="rejectionReasonContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer" style="border-top: 1px solid #dee2e6; padding: 15px 25px;">
                <button type="button" class="btn" data-bs-dismiss="modal" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 8px 20px; border-radius: 8px; font-weight: 500;">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Restore Student Form -->
<form id="restoreForm" method="POST" style="display: none;">
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
    background-color: rgba(220, 53, 69, 0.05) !important;
}

/* Button hover effects */
button[onclick*="viewStudentDetails"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.4);
}

button[onclick*="restoreStudent"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
}

a[href*="pending"]:hover,
a[href*="approved"]:hover {
    background: rgba(255,255,255,0.35) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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

.badge.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
}

.badge.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #218838 100%) !important;
}

.badge.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
}
</style>

<script>
function viewStudentDetails(studentId) {
    const modal = new bootstrap.Modal(document.getElementById('studentDetailsModal'));
    modal.show();
    
    // Reset content to loading state
    document.getElementById('studentDetailsContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-danger" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    fetch(`/admin/students/${studentId}/details`)
        .then(response => response.json())
        .then(data => {
            let rejectionReasonHtml = '';
            if (data.rejection_reason) {
                rejectionReasonHtml = `
                    <div class="col-12 mt-3">
                        <div class="alert alert-danger" style="border-radius: 10px;">
                            <h6 class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Rejection Reason</h6>
                            <p class="mb-0">${data.rejection_reason}</p>
                        </div>
                    </div>
                `;
            }
            
            document.getElementById('studentDetailsContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Personal Information</h6>
                        <table class="table table-sm">
                            <tr><th width="40%">Name:</th><td>${data.name}</td></tr>
                            <tr><th>Email:</th><td>${data.email}</td></tr>
                            <tr><th>ID:</th><td>${data.id}</td></tr>
                            <tr><th>Status:</th><td><span class="badge bg-danger">Rejected</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Account Information</h6>
                        <table class="table table-sm">
                            <tr><th width="40%">Registered:</th><td>${new Date(data.created_at).toLocaleDateString()}</td></tr>
                            <tr><th>Verified:</th><td>${data.email_verified_at ? new Date(data.email_verified_at).toLocaleDateString() : 'Not verified'}</td></tr>
                            <tr><th>Rejected:</th><td>${data.admin_rejected_at ? new Date(data.admin_rejected_at).toLocaleDateString() : 'N/A'}</td></tr>
                        </table>
                    </div>
                    ${rejectionReasonHtml}
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('studentDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load student details. Please try again.
                </div>
            `;
        });
}

function showFullReason(reason) {
    document.getElementById('rejectionReasonContent').innerHTML = `<p style="margin: 0; padding: 10px 0; color: var(--text-dark); line-height: 1.6;">${reason}</p>`;
    const modal = new bootstrap.Modal(document.getElementById('rejectionReasonModal'));
    modal.show();
}

function restoreStudent(studentId, studentName) {
    if (confirm(`Are you sure you want to restore ${studentName}? This will move them back to pending status and allow them to access the portal.`)) {
        // Show loading state
        const row = document.querySelector(`tr[data-student-id="${studentId}"]`);
        if (row) {
            row.style.opacity = '0.5';
            row.style.pointerEvents = 'none';
        }
        
        const form = document.getElementById('restoreForm');
        form.action = `/admin/students/${studentId}/restore`;
        form.submit();
    }
}

function updateRejectedCount() {
    const rows = document.querySelectorAll('tbody tr[data-student-id]');
    const count = rows.length;
    const badge = document.querySelector('.badge.fs-6');
    if (badge) {
        badge.textContent = `${count} rejected student${count !== 1 ? 's' : ''}`;
    }
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Add new alert
    const container = document.querySelector('.card').parentElement;
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }
    }, 5000);
}
</script>
@endpush
