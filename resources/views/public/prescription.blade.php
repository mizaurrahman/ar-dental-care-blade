@extends('layouts.public')

@section('title', 'Prescription — AR Dental Care')
@section('meta_robots', 'noindex,nofollow')
@section('meta_description', 'Private patient prescription. This link is not indexed by search engines.')

@section('content')
    <div class="min-h-screen p-4">
        <div class="mx-auto max-w-4xl space-y-4">
            <div class="no-print flex justify-end">
                <button class="btn-primary" type="button" onclick="window.print()">Print</button>
            </div>
            @include('partials.prescription-pad', [
                'readOnly' => true,
                'doctor' => $doctor,
                'patientName' => $prescription->patient?->name,
                'patientAge' => $prescription->patient_age,
                'prescribedOn' => $prescription->prescribed_on?->toDateString(),
                'chiefComplaints' => $prescription->chief_complaints ?? '',
                'onExamination' => $prescription->on_examination ?? '',
                'advice' => $prescription->advice ?? '',
                'rxText' => $prescription->rx_text ?? '',
                'followUpDays' => $prescription->follow_up_days,
                'items' => $prescription->items,
            ])
        </div>
    </div>
@endsection
