@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container mx-2 pt-3">

<h4>Edit Booking</h4>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('admin.bookings.update', $booking->id) }}">
@csrf
@method('PUT')

{{-- SLOT --}}
<div class="row">
<div class="col-md-6 mb-3">
    <label>Slot</label>
    <select id="slotSelect" name="slot_id" class="form-control" disabled>
        <option value="1">-- Select Slot (or create inline) --</option>
        @foreach($slots as $slot)
            <option value="{{ $slot->id }}" {{ $booking->slot_id == $slot->id ? 'selected' : '' }}>
                {{ $slot->boats->count() ? $slot->boats->pluck('name')->join(', ') : $slot->boat?->name }}
                | {{ $slot->start_date->format('d-m-Y') }} → {{ $slot->end_date->format('d-m-Y') }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-6 mb-3">
    <label>Source</label>
    <select name="source" id="sourceSelect" class="form-control">
        <option value="Direct" {{ $booking->source=='Direct'?'selected':'' }}>Direct</option>
        <option value="Agent" {{ $booking->source=='Agent'?'selected':'' }}>Agent</option>
    </select>
</div>
</div>

{{-- AGENT / SALESPERSON --}}
<div class="row">
<div class="col-md-6 mb-3" id="agentWrapper">
    <label>Agent</label>
    <select name="agent_id" id="agentSelect" class="form-control">
        <option value="">-- Select Agent --</option>
        @foreach($agents as $agent)
            <option value="{{ $agent->id }}" {{ $booking->agent_id==$agent->id?'selected':'' }}>
                {{ $agent->first_name }} {{ $agent->last_name }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-6 mb-3">
    <label>Salesperson</label>
    <select name="salesperson_id" class="form-control" required>
        @foreach($salespersons as $sp)
            <option value="{{ $sp->id }}" {{ $booking->salesperson_id==$sp->id?'selected':'' }}>
                {{ $sp->name }}
            </option>
        @endforeach
    </select>
</div>
</div>

{{-- STATUS --}}
<div class="row">
<div class="col-md-6 mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        @foreach(['Pending','Booked','Completed','Cancelled'] as $st)
            <option value="{{ $st }}" {{ $booking->status==$st?'selected':'' }}>{{ $st }}</option>
        @endforeach
    </select>
</div>
</div>

{{-- PRICE --}}
<div class="row">
<div class="col-md-4 mb-3">
    <label>Price</label>
    <input type="number" name="price" id="price" value="{{ $booking->price }}" class="form-control" required>
</div>
<div class="col-md-4 mb-3">
    <label>Currency</label>
    <select name="currency" id="currency" class="form-control">
        @foreach($currencies as $c)
            <option value="{{ $c->id }}" data-rate="{{ $c->rate }}" {{ $booking->currency==$c->id?'selected':'' }}>
                {{ $c->symbol }} {{ $c->name }}
            </option>
        @endforeach
    </select>
</div>
<div class="col-md-4 mb-3">
    <label>Price USD</label>
    <input type="number" id="price_usd" name="price_usd" value="{{ $booking->price_usd }}" class="form-control" readonly>
</div>
</div>

<hr>

{{-- ROOMS --}}
<h5>Rooms & Guests</h5>
<div id="roomWrapper" class="row g-3"></div>

{{-- PRIVATE CHARTER --}}
<div class="mt-3">
<label class="fw-bold">Guests without Room (Private Charter)</label>
<select multiple name="guests_without_room[]" id="guestsWithoutRoom" class="form-control"></select>
</div>

{{-- ADD GUEST --}}
<button type="button" class="btn btn-sm btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#guestModal">
    + Add Guest
</button>

{{-- NOTES --}}
<div class="mt-3">
<label>Notes</label>
<textarea name="notes" class="form-control">{{ $booking->notes }}</textarea>
</div>

<button class="btn btn-success mt-3">Update Booking</button>
<a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary mt-3">Cancel</a>

</form>
</div>
</div>

{{-- GUEST MODAL --}}

{{-- SCRIPTS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

const slots = @json($slots);
const guests = @json($guests);
const guestRoomMap = @json($guestRoomMapping);
const guestsWithoutRoom = @json($guestsWithoutRoom ?? []);

const roomWrapper = document.getElementById('roomWrapper');
const slotSelect = document.getElementById('slotSelect');

const guestChoices = {};
const privateChoices = new Choices('#guestsWithoutRoom', {
    removeItemButton:true,
    shouldSort:false
});

// preload private guests
guests.forEach(g=>{
    privateChoices.setChoices([{value:g.id,label:g.name}], 'value','label', false);
});
privateChoices.setChoiceByValue(guestsWithoutRoom.map(String));

function renderRooms(boats){
    roomWrapper.innerHTML = '';
    boats.forEach(boat=>{
        boat.rooms.forEach(room=>{
            const col = document.createElement('div');
            col.className='col-md-4';
            col.innerHTML=`
            <div class="card p-2">
                <strong>${boat.name} - ${room.room_name}</strong>
                <select multiple class="roomSelect form-control mt-2" name="guest_rooms[${room.id}][]"></select>
            </div>`;
            roomWrapper.appendChild(col);

            const select = col.querySelector('select');
            const choices = new Choices(select,{removeItemButton:true,shouldSort:false});
            guestChoices[room.id]=choices;

            guests.forEach(g=>{
                choices.setChoices([{value:g.id,label:g.name}], 'value','label', false);
            });

            if(guestRoomMap[room.id]){
                choices.setChoiceByValue(guestRoomMap[room.id].map(String));
            }
        });
    });
}

slotSelect.addEventListener('change',()=>{
    const slot = slots.find(s=>s.id==slotSelect.value);
    if(!slot) return roomWrapper.innerHTML='';
    const boats = slot.boats.length ? slot.boats : [slot.boat];
    renderRooms(boats);
});

// initial load
if(slotSelect.value){
    const slot = slots.find(s=>s.id==slotSelect.value);
    const boats = slot.boats.length ? slot.boats : [slot.boat];
    renderRooms(boats);
}

// price USD
function updateUSD(){
    const rate = parseFloat(currency.selectedOptions[0].dataset.rate);
    price_usd.value = (price.value*rate).toFixed(2);
}
price.addEventListener('input',updateUSD);
currency.addEventListener('change',updateUSD);
updateUSD();

});
</script>
@endsection
