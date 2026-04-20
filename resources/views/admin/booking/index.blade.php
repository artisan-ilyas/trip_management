@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container-fluid pt-3"> {{-- use container-fluid for full width --}}
        <div class="d-flex justify-content-between mb-3">
            <h4>Bookings</h4>
            <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">Create Booking</a>
        </div>

        @foreach (['success','error'] as $msg)
            @if(session($msg))
                <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }}">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach

        <div class="table-responsive"> {{-- wrap table with responsive div --}}
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-light text-uppercase small">
                    <tr>
                        <th>ID</th>
                        <th>Slot</th>
                        <th>Boat</th>
                        <th>Room</th>
                        <th>Guest</th>
                        <th>Dates</th>
                        <th>Price (USD)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td>{{ $booking->id }}</td>
                        <td>#{{ $booking->slot_id }}</td>
                        <td>
                            @if(!empty($booking->boats) && $booking->boats->count() > 0)
                                <ul class="mb-0">
                                    @foreach($booking->boats as $boat)
                                        <li>{{ $boat->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                {{ $booking->boat->name ?? '-' }}
                            @endif
                        </td>
                        <td>
                            @if(!empty($booking->rooms) && $booking->rooms->count() > 0)
                                <ul class="mb-0">
                                    @foreach($booking->rooms as $room)
                                        <li>{{ $room->room_name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                {{ $booking->room->room_name ?? '-' }}
                            @endif
                        </td>
                        <td>{{ $booking->guest_name }}</td>
                        <td>
                            @if($booking->slot)
                                {{ \Carbon\Carbon::parse($booking->slot->start_date)->format('d-m-Y') }} →
                                {{ \Carbon\Carbon::parse($booking->slot->end_date)->format('d-m-Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $booking->price_usd }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('admin.bookings.edit', $booking) }}"
                            class="btn btn-sm btn-warning">
                                Edit
                            </a>

                            <a href="{{ route('admin.bookings.payments.index', $booking) }}"
                            class="btn btn-sm btn-info">
                                Payments
                            </a>

                            <!-- ✅ ADD THIS BUTTON -->
                            <a href="{{ route('bookings.guests', $booking->id) }}"
                            class="btn btn-sm btn-dark">
                                Guests
                            </a>

                            <form method="POST"
                                action="{{ route('admin.bookings.destroy', $booking) }}"
                                class="d-inline"
                                onsubmit="return confirm('Delete this booking?')">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-danger">
                                    Delete
                                </button>

                            </form>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
