@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h2>Create Cancellation Policy</h2>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ route('cancellation-policies.store') }}" method="POST">
@csrf
<div class="row mb-3">
<div class="col-md-6"><label>Name</label><input type="text" name="name" class="form-control" required></div>
</div>

<button type="submit" class="btn btn-primary">Create Cancellation Policy</button>
</form>

</div>
</div>
@endsection
