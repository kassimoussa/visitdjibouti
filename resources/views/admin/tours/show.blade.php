@extends('layouts.admin')

@section('title', 'Détails du Tour')

@section('page-title', 'Détails du Tour')

@section('content')
    <livewire:admin.tour.tour-details :tourId="$tour->id" />
@endsection