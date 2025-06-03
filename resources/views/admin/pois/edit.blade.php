@extends('layouts.admin')

@section('title', 'Modifier un point d\'intérêt')

@section('page-title', 'Modifier un point d\'intérêt')

@section('content')
    <livewire:admin.poi.poi-form :poiId="$poiId" />
@endsection