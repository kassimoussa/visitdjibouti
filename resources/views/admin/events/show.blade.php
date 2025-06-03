@extends('layouts.admin')

@section('title', $event->title)

@section('page-title', "Détails d'un évenement")

@section('content')
    <livewire:admin.event.event-details :eventId="$event->id" />
@endsection