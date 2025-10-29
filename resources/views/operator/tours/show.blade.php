@extends('operator.layouts.app')

@section('title', 'Détails du Tour')

@section('page-title', 'Détails du Tour')

@section('content')
    <livewire:operator.tour.tour-details :tourId="$tour->id" />
@endsection
