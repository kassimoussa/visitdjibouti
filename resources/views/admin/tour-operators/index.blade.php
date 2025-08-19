@extends('layouts.admin')

@section('title', 'Opérateurs de Tour')
@section('page-title', 'Opérateurs de Tour')

@section('content')
    <livewire:admin.tour-operator.tour-operator-list />
@endsection

@push('styles')
<style>
.table img {
    max-width: 40px;
    max-height: 40px;
    object-fit: cover;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.modal-xl {
    max-width: 1200px;
}

.badge {
    font-size: 0.8em;
}

.btn-group .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.text-warning .fas {
    color: #ffc107 !important;
}

.text-warning .far {
    color: #6c757d !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-close alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endpush