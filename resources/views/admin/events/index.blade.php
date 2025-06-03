@extends('layouts.admin')

@section('title', 'Gestion des évenements')
@section('page-title', 'Gestion des évenements')

@section('content')
    <livewire:admin.event.event-list />
@endsection
