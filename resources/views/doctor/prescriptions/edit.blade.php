@extends('layouts.doctor')

@section('title', ($prescription ? 'Edit' : 'New').' prescription — AR Dental Care')

@section('content')
    @if ($prescription)
        <livewire:prescription-editor :prescription-id="$prescription->id" />
    @elseif (! empty($appointment))
        <livewire:prescription-editor :appointment-id="$appointment->id" />
    @else
        <livewire:prescription-editor />
    @endif
@endsection
