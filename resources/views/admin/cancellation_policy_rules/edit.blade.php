@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h2>Edit Rule for {{ $policy->name }}</h2>

<form action="{{ route('cancellation-policy-rules.update', [$policy->id,$rule->id]) }}" method="POST">
@csrf @method('PUT')
<div class="row mb-3">
<div class="col-md-3"><label>Days From</label><input type="number" name="days_from" class="form-control" value="{{ $rule->days_from }}" required></div>
<div class="col-md-3"><label>Days To</label><input type="number" name="days_to" class="form-control" value="{{ $rule->days_to }}" required></div>
<div class="col-md-3"><label>Penalty %</label><input type="number" name="penalty_percent" class="form-control" value="{{ $rule->penalty_percent }}" required></div>
<div class="col-md-3"><label>Refundable</label>
<select name="refundable" class="form-control" required>
<option value="0" {{ !$rule->refundable ? 'selected' : '' }}>No</option>
<option value="1" {{ $rule->refundable ? 'selected' : '' }}>Yes</option>
</select></div>
</div>

<button type="submit" class="btn btn-primary">Update Rule</button>
</form>

</div>
</div>
@endsection
