@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
<div class="container pt-3">
    <h4>Edit Guest</h4>
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.guests.update', $guest->id) }}" method="POST">
        @csrf
        @method('PUT')


        <div class="mb-3">
            <label>First Name</label>
            <input type="text" name="first_name"
                value="{{ $guest->first_name }}"
                class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Last Name</label>
            <input type="text" name="last_name"
                value="{{ $guest->last_name }}"
                class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Gender</label>
            <select name="gender" class="form-control" required>
                <option {{ $guest->gender == 'Male' ? 'selected' : '' }}>Male</option>
                <option {{ $guest->gender == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email"
                   value="{{ $guest->email }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label>Passport</label>
            <input type="text" name="passport"
                   value="{{ $guest->passport }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone"
                   value="{{ $guest->phone }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label>Address</label>
            <input type="text" name="address"
                   value="{{ $guest->address }}"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label>Date of Birth</label>
            <input type="date" name="dob"
                   value="{{ $guest->dob }}"
                   class="form-control">
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('admin.guests.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</div>
@endsection
