@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h2>Edit Payment Policy</h2>

<form action="{{ route('payment-policies.update', $policy->id) }}" method="POST">
@csrf @method('PUT')
<div class="row mb-3">
<div class="col-md-6"><label>Name</label><input type="text" name="name" class="form-control" value="{{ $policy->name }}" required></div>
<div class="col-md-3"><label>DP %</label><input type="number" name="dp_percent" class="form-control" min="0" max="100" value="{{ $policy->dp_percent }}" required></div>
<div class="col-md-3"><label>Balance Days Before Start</label><input type="number" name="balance_days_before_start" class="form-control" value="{{ $policy->balance_days_before_start }}" required></div>
</div>

<div class="row mb-3">
<div class="col-md-6"><label>Auto Cancel if DP Overdue</label>
<select name="auto_cancel_if_dp_overdue" class="form-control" required>
<option value="0" {{ !$policy->auto_cancel_if_dp_overdue ? 'selected' : '' }}>No</option>
<option value="1" {{ $policy->auto_cancel_if_dp_overdue ? 'selected' : '' }}>Yes</option>
</select></div>
<div class="col-md-6"><label>Grace Days</label><input type="number" name="grace_days" class="form-control" value="{{ $policy->grace_days }}" required></div>
</div>

<button type="submit" class="btn btn-primary">Update Payment Policy</button>
</form>

</div>
</div>
@endsection
