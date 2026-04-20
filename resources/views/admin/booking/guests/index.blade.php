@extends('layouts.admin')

@section('content')

<div class="content-wrapper">
    <div class="container-fluid pt-3">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between mb-3">
            <h4>Booking Guests</h4>

            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                Back to Bookings
            </a>
        </div>

        {{-- ALERTS --}}
        @foreach (['success','error'] as $msg)
            @if(session($msg))
                <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }}">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach

        <div class="row">

            {{-- LEFT SIDEBAR --}}
            <div class="col-md-3 border-end" style="height: 75vh; overflow-y:auto;">

                <h6 class="mb-3">Guests</h6>

                @foreach($booking->bookingGuests as $bg)
                    <div class="p-2 mb-2 border rounded guest-item"
                         id="guest-item-{{ $bg->id }}"
                         style="cursor:pointer"
                         onclick="loadGuest({{ $bg->id }})">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>
                                <strong>{{ isset($bg->guest->first_name) ? $bg->guest->first_name . ' ' . $bg->guest->last_name : 'No Name' }}</strong>
                                <div class="small text-muted">
                                    {{ isset($bg->guest->email) ? $bg->guest->email : '' }}
                                </div>
                            </div>

                            @if($bg->is_lead_guest)
                                <span class="badge bg-primary">Lead</span>
                            @endif

                        </div>

                    </div>
                @endforeach

            </div>

            {{-- RIGHT DETAIL PANEL --}}
            <div class="col-md-9" id="guest-detail" style="height: 75vh; overflow-y:auto;">

                <div class="text-center mt-5 text-muted">
                    <h5>Select a guest to view details</h5>
                </div>

            </div>

        </div>

    </div>
</div>

@endsection

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>

function loadGuest(id) {

    // highlight active
    document.querySelectorAll('.guest-item').forEach(el => {
        el.classList.remove('bg-light');
    });

    let active = document.getElementById('guest-item-' + id);
    if (active) active.classList.add('bg-light');

    // loading
    document.getElementById('guest-detail').innerHTML = `
        <div class="text-center mt-5">
            <div class="spinner-border"></div>
        </div>
    `;

    fetch(`/bookings/{{ $booking->id }}/guests/${id}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('guest-detail').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('guest-detail').innerHTML =
                '<div class="alert alert-danger">Failed to load guest</div>';
        });
}

// auto-load first guest
document.addEventListener('DOMContentLoaded', function () {
    let first = document.querySelector('.guest-item');
    if (first) {
        first.click();
    }
});

</script>
