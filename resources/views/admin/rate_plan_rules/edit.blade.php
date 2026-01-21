@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
    <h2>Edit Rule for {{ $ratePlan->name }}</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('rate-plan-rules.update', [$ratePlan->id,$rule->id]) }}" method="POST">
        @csrf @method('PUT')

        <div class="row mb-3">
                <div class="col-md-4">
                    <label>Room ID (optional)</label>
                    <input type="number" name="room_id" class="form-control" value="{{ $rule->room_id }}">
                </div>
                <div class="col-md-4">
                    <label>Base Price</label>
                    <input type="number" name="base_price" class="form-control" step="0.01" value="{{ $rule->base_price }}" required>
                </div>
                <div class="col-md-4">
                    <label>Extra Bed Price</label>
                    <input type="number" name="extra_bed_price" class="form-control" step="0.01" value="{{ $rule->extra_bed_price }}">
                </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Rule</button>
    </form>
</div>
</div>
@endsection
