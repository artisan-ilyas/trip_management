@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container mx-2 pt-3">

<h4>Create Booking</h4>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@foreach (['success','error'] as $msg)
    @if(session($msg))
        <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }}">
            {{ session($msg) }}
        </div>
    @endif
@endforeach

<form method="POST" action="{{ route('admin.bookings.store') }}">
@csrf

{{-- Slot --}}
<div class="row">
<div class="col-md-6 mb-3">
    <label>Slot</label>
    <select id="slotSelect" name="slot_id" class="form-control">
        <option value="">-- Select Slot (or create inline) --</option>
        @foreach($slots as $slot)
            <option value="{{ $slot->id }}">
                {{ $slot->boat->name }} | {{ $slot->start_date->format('d-m-Y') }} → {{ $slot->end_date->format('d-m-Y') }}
            </option>
        @endforeach
    </select>
</div>

{{-- Source --}}
<div class="col-md-6 mb-3">
    <label>Source</label>
    <select name="source" id="sourceSelect" class="form-control">
        <option value="Direct">Direct</option>
        <option value="Agent">Agent</option>
    </select>
</div>

{{-- Agent --}}
<div class="col-md-6 mb-3" id="agentWrapper">
    <label>Agent</label>
    <select name="agent_id" id="agentSelect" class="form-control" disabled>
        <option value="">-- Select Agent --</option>
        @foreach($agents as $agent)
            <option value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}</option>
        @endforeach
    </select>
</div>

<div class="col-md-6 mb-3">
    <label>Salesperson</label>
    <select name="salesperson_id" class="form-control" required>
        <option value="">-- Select Salesperson --</option>
        @foreach($salespersons as $sp)
            <option value="{{ $sp->id }}">{{ $sp->name }}</option>
        @endforeach
    </select>
</div>
</div>

<div class="row">
<div class="col-md-6 mb-3">
    <label>Status</label>
    <select name="status" class="form-control" required>
        <option value="Pending">Pending</option>
        <option value="Available">Available</option>
        <option value="Booked">Booked</option>
        <option value="Completed">Completed</option>
        <option value="Cancelled">Cancelled</option>
    </select>
</div>
</div>

{{-- Inline Slot Creation --}}
<div id="inlineSlotWrapper" class="border p-3 mb-3 d-none">
    <h5>Inline Slot Creation</h5>
    <div class="row">
    <div class="col-md-4 mb-3">
        <label>Boat</label>
        <select name="boat_id" id="boatSelect" class="form-control">
            <option value="">-- Select Boat --</option>
            @foreach($boats as $boat)
                <option value="{{ $boat->id }}">{{ $boat->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label>Start Date</label>
        <input type="date" name="start_date" class="form-control">
    </div>
    <div class="col-md-4 mb-3">
        <label>End Date</label>
        <input type="date" name="end_date" class="form-control">
    </div>
    </div>
    <div class="row">
    <div class="col-md-4 mb-3">
        <label>Region</label>
        <select name="region_id" class="form-control">
            <option value="">-- Select Region --</option>
            @foreach($regions as $region)
                <option value="{{ $region->id }}">{{ $region->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label>Embarkation Port</label>
        <select name="embarkation_port_id" class="form-control">
            <option value="">-- Select Port --</option>
            @foreach($ports as $port)
                <option value="{{ $port->id }}">{{ $port->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label>Disembarkation Port</label>
        <select name="disembarkation_port_id" class="form-control">
            <option value="">-- Select Port --</option>
            @foreach($ports as $port)
                <option value="{{ $port->id }}">{{ $port->name }}</option>
            @endforeach
        </select>
    </div>
    </div>
</div>

{{-- Price / Currency / Salesperson --}}
<div class="row">
    <div class="col-md-6 mb-3">
        <label>Price (Selected Currency)</label>
        <input type="number" id="price" name="price" step="0.01" class="form-control" required value="{{ old('price', $booking->price ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label>Currency</label>
        <select id="currency" name="currency" class="form-control" required>
            @foreach($currencies as $curr)
                <option value="{{ $curr->id }}" data-rate="{{ $curr->rate }}" {{ isset($booking) && $booking->currency_id == $curr->id ? 'selected' : '' }}>
                    {{ $curr->symbol }} - {{ $curr->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label>Price in USD</label>
        <input type="number" name="price_usd" id="price_usd" class="form-control" readonly>
    </div>
</div>

{{-- Rooms --}}
<div class="mb-3">
    <label class="fw-bold">Rooms & Guests</label>
    <div id="roomMessage" class="text-muted small mb-2">
        Please select a slot or create an inline slot and select a boat to see rooms.
    </div>
    <div id="roomWrapper" class="row g-2"></div>
</div>

{{-- Guest Modal Trigger --}}
<button type="button" id="addGuestBtn" class="btn btn-sm btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#guestModal">
    + Add Guest
</button>
{{-- Rate Plan --}}
<div class="row">
    <div class="col-md-6 mb-3">
        <label>Rate Plan</label>
        <select name="rate_plan_id" class="form-control" required>
            @foreach($ratePlans as $plan)
                <option value="{{ $plan->id }}">{{ $plan->name }}</option>
        @endforeach
    </select>
</div>

{{-- Payment Policy --}}
<div class="col-md-6 mb-3">
    <label>Payment Policy</label>
    <select name="payment_policy_id" class="form-control" required>
        @foreach($paymentPolicies as $policy)
            <option value="{{ $policy->id }}">{{ $policy->name }}</option>
        @endforeach
    </select>
</div>

{{-- Cancellation Policy --}}
<div class="col-md-6 mb-3">
    <label>Cancellation Policy</label>
    <select name="cancellation_policy_id" class="form-control" required>
        @foreach($cancellationPolicies as $policy)
            <option value="{{ $policy->id }}">{{ $policy->name }}</option>
        @endforeach
    </select>
</div>


{{-- Notes --}}
<div class="mb-3">
    <label>Notes</label>
    <textarea name="notes" class="form-control"></textarea>
</div>
</div>
<button class="btn btn-success">Create Booking</button>
<a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">Cancel</a>
</form>
</div>
</div>

{{-- Add Guest Modal --}}
<div class="modal fade" id="guestModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="guestForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add Guest</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
          <div class="mb-3"><label>Gender</label><select name="gender" class="form-control" required><option value="">-- Select Gender --</option><option value="Male">Male</option><option value="Female">Female</option></select></div>
          <div class="mb-3"><label>Date of Birth</label><input type="date" name="dob" class="form-control" required></div>
          <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control"></div>
          <div class="mb-3"><label>Passport</label><input type="text" name="passport" class="form-control"></div>
          <div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
          <div class="mb-3"><label>Address</label><input type="text" name="address" class="form-control"></div>
          <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-primary">Save Guest</button></div>
      </form>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const slots = @json($slots);
    const boats = @json($boats);
    const guests = @json($guests);

    const sourceSelect      = document.getElementById('sourceSelect');
    const agentSelect       = document.getElementById('agentSelect');
    const slotSelect        = document.getElementById('slotSelect');
    const boatSelect        = document.getElementById('boatSelect');
    const inlineSlotWrapper = document.getElementById('inlineSlotWrapper');
    const roomWrapper       = document.getElementById('roomWrapper');
    const roomMessage       = document.getElementById('roomMessage');

    // Toggle agent field
    function toggleAgentField() {
        agentSelect.disabled = sourceSelect.value !== 'Agent';
        if (agentSelect.disabled) agentSelect.value = '';
    }
    toggleAgentField();
    sourceSelect.addEventListener('change', toggleAgentField);

    // Render rooms
    function renderRooms(rooms) {
        roomWrapper.innerHTML = '';
        if(!rooms || !rooms.length){ roomMessage.style.display = 'block'; return; }
        roomMessage.style.display = 'none';

        rooms.forEach(room => {
            const cap = parseInt(room.capacity ?? 0) + parseInt(room.extra_beds ?? 0);
            const div = document.createElement('div');
            div.className = 'col-md-4';
            div.innerHTML = `
                <label class="card p-2 h-100">
                    <strong>${room.room_name}</strong><br>
                    <small class="text-muted">Max ${cap}</small>
                    <select multiple
                            class="form-control room-guests mt-2"
                            name="guest_rooms[${room.id}][]"
                            data-room-id="${room.id}"
                            data-cap="${cap}">
                    </select>
                    <div class="assigned-guests mt-1 text-muted"></div>
                    <div class="room-full text-danger mt-1" style="display:none;">Room is full!</div>
                </label>`;
            roomWrapper.appendChild(div);

            const select = div.querySelector('select.room-guests');
            const fullMsg = div.querySelector('.room-full');

            // Add guest options
            guests.forEach(g => {
                const opt = document.createElement('option');
                opt.value = g.id;
                opt.text = g.name;
                select.add(opt);
            });

            // Initialize Choices.js
            const choices = new Choices(select, {
                removeItemButton: true,
                searchEnabled: true,
                shouldSort: false,
                placeholder: true,
                placeholderValue: 'Select guests for this room'
            });

            // Update room state function
            function updateRoomState() {
                const selectedCount = select.selectedOptions.length;
                div.querySelector('.assigned-guests').textContent = Array.from(select.selectedOptions).map(o => o.text).join(', ');

                if(selectedCount >= cap) {
                    // Room full → hide dropdown
                    select.closest('label').querySelector('.choices').style.display = 'none';
                    fullMsg.style.display = 'block';
                } else {
                    // Room has space → show dropdown
                    select.closest('label').querySelector('.choices').style.display = 'block';
                    fullMsg.style.display = 'none';
                }
            }

            // Trigger when selection changes
            select.addEventListener('change', updateRoomState);
            select.addEventListener('removeItem', updateRoomState);
        });
    }

    // Slot change
    slotSelect.addEventListener('change', function() {
        const slot = slots.find(s => s.id == this.value);
        if(!slot){ inlineSlotWrapper.classList.remove('d-none'); roomWrapper.innerHTML=''; roomMessage.style.display='block'; return; }
        inlineSlotWrapper.classList.add('d-none');
        if(slot.boat && slot.boat.rooms) renderRooms(slot.boat.rooms);
    });

    // Boat change
    boatSelect.addEventListener('change', function() {
        const boat = boats.find(b => b.id == this.value);
        if(!boat || !boat.rooms){ roomWrapper.innerHTML=''; roomMessage.style.display='block'; return; }
        renderRooms(boat.rooms);
    });

    // Add guest via modal
    $('#guestForm').on('submit', function(e){
        e.preventDefault();
        const form = $(this);
        $.ajax({
            url: '{{ route("admin.guests.store") }}',
            method: 'POST',
            data: form.serialize(),
            success: function(guest){
                guests.push(guest);
                document.querySelectorAll('.room-guests').forEach(sel => {
                    const newOption = document.createElement('option');
                    newOption.value = guest.id; newOption.text = guest.name;
                    sel.add(newOption.cloneNode(true));
                });
                $('#guestModal').modal('hide'); form[0].reset();
            },
            error: function(){ alert('Failed to create guest'); }
        });
    });

     // Price USD
    const priceInput = document.getElementById('price');
    const currencySelect = document.getElementById('currency');
    const priceUsdInput = document.getElementById('price_usd');
    function updateUSD(){
        const rate = parseFloat(currencySelect.selectedOptions[0].dataset.rate);
        const price = parseFloat(priceInput.value)||0;
        priceUsdInput.value=(price*rate).toFixed(2);
    }
    priceInput.addEventListener('input', updateUSD);
    currencySelect.addEventListener('change', updateUSD);
    updateUSD();

    // Initial state
    roomMessage.style.display='block';
    // addGuestBtn.style.display='none';
});
</script>



@endsection
