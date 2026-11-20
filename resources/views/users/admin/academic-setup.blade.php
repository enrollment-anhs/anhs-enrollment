@extends(request()->routeIs('principal.*') ? 'users.principal.layout' : 'users.admin.layout')

@section('title', 'Academic Setup - Academic Years')

@push('toasts')
    <x-password-reset-toasts test-prefix="academic-setup" />
@endpush

@section('content')
@php
    $managementRoutePrefix = request()->routeIs('principal.*') ? 'principal.' : 'admin.';
@endphp

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-800 md:text-3xl">Academic Years</h1>
        <p class="mt-1 text-sm text-gray-500">Manage school years for enrollment.</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Current academic year</p>
        <p class="mt-1 font-bold text-[#296374]">{{ $yearData['currentYear']?->school_year ?? 'No active school year' }}</p>
    </div>
</div>

<section>
    @include('users.admin.academic-year-config', $yearData)
</section>
@endsection
