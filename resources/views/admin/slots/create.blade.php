@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h4>Create Slot</h4>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.slots.store') }}">
@csrf

<div class="mb-3">
    <label>Template (optional)</label>
    <select id="templateSelect" class="form-control" name="template_id">
        <option value="">-- Select Template --</option>
        @foreach($templates as $template)
            <option value="{{ $template->id }}" data-json='@json($template)'>
                {{ $template->product_name }} ({{ $template->product_type }})
            </option>
        @endforeach
    </select>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Slot Type</label>
        <select name="slot_type" class="form-control" required>
            @foreach(['Open Trip','Private Charter','Maintenance','Docking','Crossing'] as $type)
                <option value="{{ $type }}" {{ old('slot_type')==$type?'selected':'' }}>{{ $type }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label>Status</label>
        <select name="status" class="form-control" required>
            @foreach(['Available','On-Hold','Blocked'] as $status)
                <option value="{{ $status }}" {{ old('status')==$status?'selected':'' }}>{{ $status }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Vessels</label>
        <select name="boats_allowed[]" id="boatsAllowed" class="form-control" multiple required>
            @foreach($boats as $boat)
                <option value="{{ $boat->id }}">{{ $boat->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label>Region</label>
        <select name="region_id" class="form-control" required>
            @foreach($regions as $region)
                <option value="{{ $region->id }}">{{ $region->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Departure Port</label>
        <select name="departure_port_id" class="form-control" required>
            @foreach($ports as $port)
                <option value="{{ $port->id }}">{{ $port->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label>Arrival Port</label>
        <select name="arrival_port_id" class="form-control" required>
            @foreach($ports as $port)
                <option value="{{ $port->id }}">{{ $port->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Start Date</label>
        <input type="date" name="start_date" id="startDate" class="form-control" value="{{ old('start_date') }}" required>
    </div>
    <div class="col-md-3">
        <label>Duration (Nights)</label>
        <input type="number" name="duration_nights" id="durationNights" class="form-control" value="{{ old('duration_nights', 0) }}" min="0" required>
    </div>
    <div class="col-md-3">
        <label>End Date</label>
        <input type="date" name="end_date" id="endDate" class="form-control" readonly>
    </div>
</div>

<div class="mb-3">
    <label>Notes</label>
    <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
</div>

<button class="btn btn-success">Create Slot</button>
<a href="{{ route('admin.slots.index') }}" class="btn btn-secondary">Cancel</a>
</form>
</div>
</div>

<!-- Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // Initialize Vessels multiselect
    const vesselsSelect = document.getElementById('boatsAllowed');
    const vesselsChoices = new Choices(vesselsSelect, {
        removeItemButton: true,
        searchEnabled: true,
        shouldSort: false,
        placeholder: true,
        placeholderValue: 'Select vessels'
    });

    // Auto-calculate End Date
    const startInput = document.getElementById('startDate');
    const durationInput = document.getElementById('durationNights');
    const endInput = document.getElementById('endDate');

    function calculateEndDate(){
        if(!startInput.value || !durationInput.value) return;
        const startDate = new Date(startInput.value);
        const nights = parseInt(durationInput.value);
        const endDate = new Date(startDate);
        endDate.setDate(endDate.getDate() + nights);
        endInput.valueAsDate = endDate;
    }

    startInput.addEventListener('change', calculateEndDate);
    durationInput.addEventListener('input', calculateEndDate);

    // Template auto-fill
    document.getElementById('templateSelect').addEventListener('change', function () {
        const opt = this.selectedOptions[0];
        if (!opt || !opt.dataset.json) return;
        const data = JSON.parse(opt.dataset.json);

        // Region
        if(data.region_id) document.querySelector('[name=region_id]').value = data.region_id;

        // Vessels multiselect
        if(data.vessels_allowed && data.vessels_allowed.length){
            vesselsChoices.removeActiveItems(); // clear previous selections
            data.vessels_allowed.forEach(id => {
                const option = vesselsSelect.querySelector(`option[value='${id}']`);
                if(option) vesselsChoices.setChoiceByValue(option.value);
            });
        } else {
            vesselsChoices.removeActiveItems();
        }

        // Departure Port
        if(data.departure_ports && data.departure_ports.length){
            document.querySelector('[name=departure_port_id]').value = data.departure_ports[0];
        }

        // Arrival Port
        if(data.arrival_ports && data.arrival_ports.length){
            document.querySelector('[name=arrival_port_id]').value = data.arrival_ports[0];
        }

        // Duration → calculate end date
        if(data.duration_nights){
            durationInput.value = data.duration_nights;
            calculateEndDate();
        }

        // Slot Type
        if(data.product_type) document.querySelector('[name=slot_type]').value = data.product_type;
    });
});
</script>
@endsection
