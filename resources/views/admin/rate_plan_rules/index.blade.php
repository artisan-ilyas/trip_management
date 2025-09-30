@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">
<h2>Rules for {{ $ratePlan->name }}</h2>
<a href="{{ route('rate-plan-rules.create', $ratePlan->id) }}" class="btn btn-primary mb-3">Add Rule</a>

@if(session('success'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>Swal.fire({icon:'success',title:'Success',text:'{{ session('success') }}',confirmButtonColor:'#3085d6'});</script>
@endif

<table class="table table-bordered">
<thead>
<tr>
<th>Room ID</th>
<th>Base Price</th>
<th>Extra Bed Price</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
@foreach($ratePlan->rules as $rule)
<tr>
<td>{{ $rule->room_id ?? '-' }}</td>
<td>{{ $rule->base_price }}</td>
<td>{{ $rule->extra_bed_price ?? '-' }}</td>
<td>
<a href="{{ route('rate-plan-rules.edit', [$ratePlan->id,$rule->id]) }}" class="btn btn-sm btn-warning">Edit</a>
<form action="{{ route('rate-plan-rules.destroy', [$ratePlan->id,$rule->id]) }}" method="POST" style="display:inline-block;">
@csrf @method('DELETE')
<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete rule?')">Delete</button>
</form>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
@endsection
