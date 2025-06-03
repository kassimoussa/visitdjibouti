@extends('layouts.admin')

@section('title', 'Modifier un évenement')

@section('page-title', 'Modifier un évenement')

@section('content')
    <livewire:admin.event.event-form :eventId="$eventId" />
@endsection