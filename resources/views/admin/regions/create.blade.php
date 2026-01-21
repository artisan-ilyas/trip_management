@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container pt-3">

        <h4>Add Region</h4>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
        <form method="POST" action="{{ route('admin.regions.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Region Name</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       required>
            </div>

            <button class="btn btn-success">Save</button>
            <a href="{{ route('admin.regions.index') }}" class="btn btn-secondary">Back</a>
        </form>

    </div>
</div>
@endsection
