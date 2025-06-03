@extends('layouts.admin')

@section('title', 'Modifier  des Media')
@section('page-title', 'Modifier  des Media')

@section('content')
    <livewire:admin.media-edit :id="$id" />
@endsection
