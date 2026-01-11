@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Create Slot</h2>
        <a href="{{ route('trips.index') }}" class="btn btn-secondary">Back</a>
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
                <form action="{{ route('trips.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">

                        {{-- Slot Type --}}
                        <div class="col-md-6">
                            <label for="slot_type" class="form-label">Slot Type</label>
                            <select name="trip_type" id="slot_type" class="form-control" required>
                                <option value="">Select Slot Type</option>
                                <option value="Open">Open Trip</option>
                                <option value="Private">Private Charter</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Docking">Docking</option>
                                <option value="Crossing">Crossing</option>
                            </select>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="">Select Status</option>
                                <option value="Available">Available</option>
                                <option value="On Hold">On Hold</option>
                                <option value="Booked">Booked</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Docking">Docking</option>
                                <option value="Crossing">Crossing</option>
                            </select>
                        </div>

                        {{-- Boat --}}
                        <div class="col-md-6">
                            <label for="boat" class="form-label">Boat</label>
                            <select name="boat_id" id="boat" class="form-control" required>
                                <option value="">Select boat</option>
                                @foreach($boats as $boat)
                                    <option value="{{ $boat->id }}">{{ $boat->name }} ({{ $boat->rooms_count ?? $boat->rooms->count() }} rooms)</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Region --}}
                        <div class="col-md-6">
                            <label for="region" class="form-label">Region</label>
                            <input type="text" name="region" id="region" class="form-control" value="{{ old('region', $lastRegion ?? '') }}" required>
                        </div>

                        {{-- Start/End --}}
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" required>
                        </div>

                        {{-- Rooms --}}
                        <div class="col-md-12">
                            <label class="form-label">Available Rooms</label>
                            <div id="rooms-container" class="border p-2">
                                @foreach($boats as $boat)
                                    <div class="mb-2">
                                        <strong>{{ $boat->name }} Rooms:</strong>
                                        @foreach($boat->rooms as $room)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input room-checkbox" type="checkbox" name="rooms[{{ $room->id }}]" value="1" checked>
                                                <label class="form-check-label">{{ $room->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="col-md-12 mt-2">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Enter notes"></textarea>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary">Create Slot</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById("start_date").setAttribute("min", today);
    document.getElementById("end_date").setAttribute("min", today);

    document.getElementById("start_date").addEventListener("change", function () {
        const selectedStart = this.value;
        document.getElementById("end_date").setAttribute("min", selectedStart);
    });

    // Auto-generate Slot Title
    const boatSelect = document.getElementById('boat');
    const slotTypeSelect = document.getElementById('slot_type');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const titleInput = document.getElementById('title');

    function generateTitle() {
        if (boatSelect.value && slotTypeSelect.value && startDate.value && endDate.value) {
            const boatName = boatSelect.options[boatSelect.selectedIndex].text.split(' (')[0];
            const type = slotTypeSelect.value.replace(' ', '');
            const from = startDate.value;
            const to = endDate.value;
            const year = new Date(startDate.value).getFullYear();
            titleInput.value = `${boatName}-${type}-${from}-${to}-${year}`;
        }
    }

    boatSelect.addEventListener('change', generateTitle);
    slotTypeSelect.addEventListener('change', generateTitle);
    startDate.addEventListener('change', generateTitle);
    endDate.addEventListener('change', generateTitle);
});
</script>
@endsection
