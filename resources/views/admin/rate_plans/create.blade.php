@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Create Rate Plan</h2>
        <a href="{{ route('rate-plans.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('rate-plans.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Currency</label>
                        <input type="text" name="currency" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Base Price Type</label>
                        <select name="base_price_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="per_room">Per Room</option>
                            <option value="charter">Charter</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Tax Included</label>
                        <select name="tax_included" class="form-control" required>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Create Rate Plan</button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
