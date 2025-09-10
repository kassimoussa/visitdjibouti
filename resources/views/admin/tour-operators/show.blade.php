@extends('layouts.admin')

@section('title', 'Détails de l\'opérateur - ' . ($tourOperator->name ?? 'Tour Operator'))

@section('content')
    @livewire('admin.tour-operator.tour-operator-detail', ['tourOperator' => $tourOperator])
@endsection