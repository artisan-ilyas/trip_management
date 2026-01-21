@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h2>Add Rule for {{ $policy->name }}</h2>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ route('cancellation-policy-rules.store', $policy->id) }}" method="POST">
@csrf
<div class="row mb-3">
<div class="col-md-3"><label>Days From</label><input type="number" name="days_from" class="form-control" required></div>
<div class="col-md-3"><label>Days To</label><input type="number" name="days_to" class="form-control" required></div>
<div class="col-md-3"><label>Penalty %</label><input type="number" name="penalty_percent" class="form-control" required></div>
<div class="col-md-3"><label>Refundable</label>
<select name="refundable" class="form-control" required>
<option value="0">No</option>
<option value="1">Yes</option>
</select></div>
</div>

<button type="submit" class="btn btn-primary">Add Rule</button>
</form>

</div>
</div>
@endsection
