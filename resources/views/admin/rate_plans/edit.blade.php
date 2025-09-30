@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">
    <h2>Edit Rate Plan</h2>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('rate-plans.update', $ratePlan->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $ratePlan->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label>Currency</label>
                        <input type="text" name="currency" class="form-control" value="{{ $ratePlan->currency }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Base Price Type</label>
                        <select name="base_price_type" class="form-control" required>
                            <option value="per_room" {{ $ratePlan->base_price_type=='per_room' ? 'selected' : '' }}>Per Room</option>
                            <option value="charter" {{ $ratePlan->base_price_type=='charter' ? 'selected' : '' }}>Charter</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Tax Included</label>
                        <select name="tax_included" class="form-control" required>
                            <option value="0" {{ !$ratePlan->tax_included ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $ratePlan->tax_included ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Rate Plan</button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
