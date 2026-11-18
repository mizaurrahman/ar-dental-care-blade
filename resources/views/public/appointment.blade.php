@extends('layouts.public')

@php
    $clinic = $doctor->clinic_name ?: 'Dental clinic';
    $doctorName = $doctor->name ?: 'Doctor';
    $specialty = $doctor->specialty ?: 'Dental care';
    $description = trim(collect([
        'Book an appointment at '.$clinic.' with '.$doctorName.'.',
        $specialty,
        $doctor->clinic_address,
        $doctor->visiting_hours ? 'Hours: '.$doctor->visiting_hours : null,
    ])->filter()->implode(' '));
    $image = $doctor->logo_url ?: asset('images/ar-dental-logo.svg');
    $canonical = route('appointment.public', $doctor->appointment_slug);
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Dentist',
        'name' => $clinic,
        'url' => $canonical,
        'image' => $image,
        'description' => $description,
        'telephone' => $doctor->clinic_phone,
        'address' => array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $doctor->clinic_address,
            'addressCountry' => 'BD',
        ]),
        'openingHours' => $doctor->visiting_hours,
        'employee' => array_filter([
            '@type' => 'Person',
            'name' => $doctorName,
            'jobTitle' => $specialty,
            'image' => $doctor->avatar_url,
        ]),
        'medicalSpecialty' => $specialty,
    ];
@endphp

@section('title', 'Book appointment — '.$clinic)

@section('meta_description', \Illuminate\Support\Str::limit($description, 155))
@section('meta_robots', 'index,follow')
@section('canonical', $canonical)
@section('og_image', $image)
@section('og_type', 'website')

@section('json_ld')
{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
@endsection

@section('content')
    <livewire:public-booking :slug="$slug" />
@endsection
