@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">View Guests</h2>
            <!-- <a href="{{ route('trips.create') }}" class="btn btn-primary">Create Trip</a> -->
        </div>

        @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: (session('success')),
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        </script>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Region</th>
                                <th>Status</th>
                                <th>Leading Guest</th>
                                <th>Agent Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Price</th>
                                <th class="col-2">Link/UUID</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Table body intentionally left empty -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
