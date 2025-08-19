@extends('layouts.admin')

@section('title', 'Créer un opérateur de tour')
@section('page-title', 'Créer un opérateur de tour')

@section('content')
    <livewire:admin.tour-operator.tour-operator-form />
@endsection

@push('styles')
<style>
.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.badge {
    font-size: 0.8em;
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