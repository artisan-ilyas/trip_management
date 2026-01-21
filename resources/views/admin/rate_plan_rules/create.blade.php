@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">
<h2>Add Rule for {{ $ratePlan->name }}</h2>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ route('rate-plan-rules.store', $ratePlan->id) }}" method="POST">
@csrf
<div class="row mb-3">
<div class="col-md-4"><label>Room ID (optional)</label><input type="number" name="room_id" class="form-control"></div>
<div class="col-md-4"><label>Base Price</label><input type="number" name="base_price" class="form-control" step="0.01" required></div>
<div class="col-md-4"><label>Extra Bed Price</label><input type="number" name="extra_bed_price" class="form-control" step="0.01"></div>
</div>
<button type="submit" class="btn btn-primary">Add Rule</button>
</form>
</div>
</div>
@endsection
