@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container pt-3">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Company Details</h2>
            <a href="{{ route('company.index') }}" class="btn btn-secondary">Back to Companies</a>
        </div>

        {{-- Company Info --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Company Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Name:</strong> {{ $company->name ?? '-' }}</div>
                    <div class="col-md-6"><strong>Legal Name:</strong> {{ $company->legal_name ?? '-' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Slug:</strong> {{ $company->slug ?? '-' }}</div>
                    <div class="col-md-6"><strong>Currency:</strong> {{ $company->currency ?? '-' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Timezone:</strong> {{ $company->timezone ?? '-' }}</div>
                    <div class="col-md-6"><strong>Billing Email:</strong> {{ $company->billing_email ?? '-' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Address:</strong> {{ $company->address ?? '-' }}</div>
                    <div class="col-md-6"><strong>VAT/Tax ID:</strong> {{ $company->vat_tax_id ?? '-' }}</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
