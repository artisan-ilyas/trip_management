@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Create Role</h2>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Back</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('roles.store') }}" method="post">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Role Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter role name" required>
                </div>

                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
