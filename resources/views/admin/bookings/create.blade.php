@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Create Booking</h2>
        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Back</a>
    </div>

    @if(session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    </script>
    @endif

    <div class="card">
        <div class="card-body">
        <form action="{{ route('bookings.store') }}" method="POST">
            @csrf

            {{-- Inline Slot Creation --}}
            <div class="form-check mb-3">
                <input type="checkbox" name="inline_slot" id="inlineSlotCheckbox" class="form-check-input">
                <label for="inlineSlotCheckbox" class="form-check-label">Create New Slot Inline</label>
            </div>

            <div id="inlineSlotFields" style="display:none; border:1px solid #ccc; padding:15px; border-radius:5px;">
                <div class="row">
                    <div class="col-md-6">
                        <label>Slot Title</label>
                        <input type="text" name="slot_title" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Boat</label>
                        <select name="boat_id" id="inline_boat" class="form-control">
                            <option value="">Select boat</option>
                            @foreach($boats as $boat)
                                <option value="{{ $boat->id }}">{{ $boat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Slot Type</label>
                        <select name="slot_type" class="form-control">
                            <option value="Available">Available</option>
                            <option value="On Hold">On Hold</option>
                            <option value="Booked">Booked</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Docking">Docking</option>
                            <option value="Crossing">Crossing</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="col-md-6 mt-2">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>
            </div>

            {{-- Existing Slot --}}
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Select Slot</label>
                    <select name="slot_id" id="slot_id" class="form-control">
                        <option value="">Select Slot</option>
                        @foreach($trips as $slot)
                            <option value="{{ $slot->id }}">{{ $slot->title }} ({{ $slot->start_date }} - {{ $slot->end_date }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Direct / Agent</label>
                    <select name="source" id="source" class="form-control">
                        <option value="">Select</option>
                        <option value="Direct">Direct</option>
                        <option value="By Agent">By Agent</option>
                    </select>
                </div>
            </div>

            {{-- Rooms Multi-Select --}}
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Rooms</label>
                    <select name="rooms[]" id="rooms" class="form-control" multiple>
                        <option value="">Select rooms</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Price</label>
                    <input type="text" name="price" id="price" class="form-control" readonly>
                </div>
            </div>

            {{-- Guest Details --}}
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Guest Name</label>
                    <input type="text" name="customer_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-6">
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Nationality</label>
                    <input type="text" name="nationality" class="form-control">
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control"></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Create Booking</button>
        </form>
        </div>
    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script>
$(document).ready(function(){
    // Toggle inline slot creation
    $('#inlineSlotCheckbox').on('change', function(){
        $('#inlineSlotFields').toggle(this.checked);
        $('#slot_id').prop('disabled', this.checked);
    }).trigger('change');

    // Dynamic Rooms loading for selected slot
    $('#slot_id').on('change', function(){
        const slotId = $(this).val();
        const roomsSelect = $('#rooms');
        roomsSelect.empty();
        if(!slotId) return;
        $.get(`/slots/${slotId}/available-rooms`, function(data){
            data.rooms.forEach(r=>{
                roomsSelect.append(`<option value="${r.id}" data-price="${r.price}">${r.name}</option>`);
            });
        });
    });

    // Set price when selecting rooms
    $('#rooms').on('change', function(){
        let total = 0;
        $(this).find('option:selected').each(function(){
            total += parseFloat($(this).data('price') || 0);
        });
        $('#price').val(total);
    });

    // Source logic
    $('#source').on('change', function(){
        const val = $(this).val();
        if(val==='Direct'){
            $('input[name="customer_name"], input[name="email"], input[name="phone_number"]').prop('disabled', false);
        } else {
            $('input[name="customer_name"], input[name="email"], input[name="phone_number"]').prop('disabled', true);
        }
    }).trigger('change');
});
</script>
@endsection
