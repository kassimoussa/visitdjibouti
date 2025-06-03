@extends('layouts.admin')

@section('title', $poi->name)

@section('page-title', 'Détails du point d\'intérêt')

@section('content')
    <livewire:admin.poi.poi-details :poiId="$poi->id" />
@endsection