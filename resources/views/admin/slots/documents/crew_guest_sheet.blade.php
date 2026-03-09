<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Crew Guest Sheet - Slot #{{ $slot->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 6px; text-align: left; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Crew Guest Sheet</h2>
    <p><strong>Slot ID:</strong> {{ $slot->id }} | <strong>Dates:</strong> {{ $slot->start_date->format('d-m-Y') }} → {{ $slot->end_date->format('d-m-Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Guest Name</th>
                <th>Room(s)</th>
                <th>Dietary Requirements</th>
                <th>Allergies</th>
                <th>Equipment Sizes</th>
                <th>Operational Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
                @foreach($booking->guests()->get() as $guest)
                <tr>
                    <td>{{ $guest->name ?? '-' }}</td>
                    <td>
                        @php
                            // Get only rooms for this booking
                            $roomsForBooking = $guest->rooms()->wherePivot('booking_id', $booking->id)->get();
                        @endphp
                        @if($roomsForBooking->count())
                            @foreach($roomsForBooking as $room)
                                {{ $room->room_name }}@if(!$loop->last), @endif
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $guest->dietary_requirements ?? '-' }}</td>
                    <td>{{ $guest->allergies ?? '-' }}</td>
                    <td>{{ $guest->equipment_sizes ?? '-' }}</td>
                    <td>{{ $guest->operational_notes ?? '-' }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
