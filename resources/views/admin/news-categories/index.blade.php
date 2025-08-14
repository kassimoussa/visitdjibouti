@extends('layouts.admin')

@section('title', 'Catégories d\'actualités')
@section('page-title', 'Catégories d\'actualités')

@section('content')
<div class="container-fluid">
    <livewire:admin.news-category-manager />
</div>
@endsection