@extends('layouts.admin')

@section('title', 'Gestion des utilisateurs')

@section('page-title', 'Gestion des utilisateurs')

@section('content')
    <livewire:admin.user-manager />
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation des éléments lors du chargement
        const animatedElements = document.querySelectorAll('.animated');
        animatedElements.forEach(el => {
            el.style.opacity = '1';
        });
    });
</script>
@endpush

@push('style')
<style>
    .animated {
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }
    
    .delay-1 {
        transition-delay: 0.1s;
    }
    
    .delay-2 {
        transition-delay: 0.2s;
    }
    
    .delay-3 {
        transition-delay: 0.3s;
    }

    /* Styles pour les éléments de pagination personnalisés */
    nav[aria-label="Pagination Navigation"] {
        display: flex;
        justify-content: center;
    }

    nav[aria-label="Pagination Navigation"] div {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    nav[aria-label="Pagination Navigation"] div span {
        display: inline-flex;
    }

    nav[aria-label="Pagination Navigation"] .relative.inline-flex {
        position: relative;
        display: inline-flex;
    }

    nav[aria-label="Pagination Navigation"] .rounded-md {
        padding: 0.3rem 0.8rem;
        margin: 0 0.1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    nav[aria-label="Pagination Navigation"] .bg-white {
        background-color: #fff;
        border: 1px solid #dee2e6;
        color: #212529;
    }

    nav[aria-label="Pagination Navigation"] .bg-primary {
        background-color: #0d6efd;
        color: white;
        border: 1px solid #0d6efd;
    }

    nav[aria-label="Pagination Navigation"] .text-gray-500 {
        color: #6c757d;
    }

    nav[aria-label="Pagination Navigation"] .disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endpush