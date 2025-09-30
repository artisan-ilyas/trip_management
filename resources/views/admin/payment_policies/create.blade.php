@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h2>Create Payment Policy</h2>

<form action="{{ route('payment-policies.store') }}" method="POST">
@csrf
<div class="row mb-3">
<div class="col-md-6"><label>Name</label><input type="text" name="name" class="form-control" required></div>
<div class="col-md-3"><label>DP %</label><input type="number" name="dp_percent" class="form-control" min="0" max="100" required></div>
<div class="col-md-3"><label>Balance Days Before Start</label><input type="number" name="balance_days_before_start" class="form-control" required></div>
</div>

<div class="row mb-3">
<div class="col-md-6"><label>Auto Cancel if DP Overdue</label>
<select name="auto_cancel_if_dp_overdue" class="form-control" required>
<option value="0">No</option>
<option value="1">Yes</option>
</select></div>
<div class="col-md-6"><label>Grace Days</label><input type="number" name="grace_days" class="form-control" required></div>
</div>

<button type="submit" class="btn btn-primary">Create Payment Policy</button>
</form>

</div>
</div>
@endsection
