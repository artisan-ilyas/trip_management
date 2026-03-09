<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Harbormaster Manifest - Slot #{{ $slot->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 6px; text-align: left; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Harbormaster Manifest</h2>
    <p><strong>Slot ID:</strong> {{ $slot->id }} | <strong>Dates:</strong> {{ $slot->start_date->format('d-m-Y') }} → {{ $slot->end_date->format('d-m-Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Guest Name</th>
                <th>Gender</th>
                <th>Date of Birth</th>
                <th>Passport</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
@foreach($bookings as $booking)
    @foreach($booking->guests()->get() as $guest)
        <tr>
            <td>{{ $guest->name ?? '-' }}</td>
            <td>{{ $guest->gender ?? '-' }}</td>
            <td>{{ $guest->dob ? \Carbon\Carbon::parse($guest->dob)->format('d-m-Y') : '-' }}</td>
            <td>{{ $guest->passport ?? '-' }}</td>
            <td>{{ $guest->phone ?? '-' }}</td>
        </tr>
    @endforeach
@endforeach
        </tbody>
    </table>
</body>
</html>
