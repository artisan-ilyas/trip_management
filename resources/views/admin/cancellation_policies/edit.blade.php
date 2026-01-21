@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h2>Edit Cancellation Policy</h2>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ route('cancellation-policies.update', $policy->id) }}" method="POST">
@csrf @method('PUT')
<div class="row mb-3">
<div class="col-md-6"><label>Name</label><input type="text" name="name" class="form-control" value="{{ $policy->name }}" required></div>
</div>

<button type="submit" class="btn btn-primary">Update Cancellation Policy</button>
</form>

</div>
</div>
@endsection
