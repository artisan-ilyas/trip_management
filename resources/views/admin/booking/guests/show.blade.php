@php
    $arrival = $arrival ?? null;
    $departure = $departure ?? null;
@endphp

<div class="p-3">

    <h4>
        {{ isset($bookingGuest->guest->first_name) ? $bookingGuest->guest->first_name . ' ' . $bookingGuest->guest->last_name : 'Guest' }}
        @if($bookingGuest->is_lead_guest)
            <span class="badge bg-primary">Lead Guest</span>
        @endif
    </h4>

    <hr>

    <!-- NAV -->
    <ul class="nav nav-tabs">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile">Profile</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#travel">Travel</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#medical">Medical</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#food">Food</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#drink">Drinks</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#housekeeping">Housekeeping</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#service">Service</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#diving">Diving</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#surfing">Surfing</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#documents">Documents</button></li>
    </ul>

    <div class="tab-content mt-3">
     <!-- PROFILE -->
     <div class="tab-pane fade show active" id="profile">
         <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/profile') }}">
            @csrf
            <div class="row"> <div class="col-md-6 mb-3">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" value="{{ isset($bookingGuest->guest->first_name) ? $bookingGuest->guest->first_name : '' }}"> </div>
                <div class="col-md-6 mb-3">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ isset($bookingGuest->guest->last_name) ? $bookingGuest->guest->last_name : '' }}">
                </div> <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ isset($bookingGuest->guest->email) ? $bookingGuest->guest->email : '' }}">
                </div> <div class="col-md-6 mb-3">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ isset($bookingGuest->guest->phone) ? $bookingGuest->guest->phone : '' }}">
                </div>
            </div>
            <button class="btn btn-primary">Save Profile</button>
        </form>
    </div>

    <!-- TRAVEL -->
    <div class="tab-pane fade" id="travel">
        <div class="row">
            <!-- ARRIVAL -->
            <div class="col-md-6 border-end">
                <h5 class="text-primary">Arrival</h5>
                <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/travel') }}">
                    @csrf
                    <input type="hidden" name="direction" value="arrival">

                    <div class="mb-2">
                        <label>Type</label>
                        <select name="travel_type" class="form-control">
                            <option value="flight" {{ optional($arrival)->travel_type == 'flight' ? 'selected' : '' }}>Flight</option>
                            <option value="hotel" {{ optional($arrival)->travel_type == 'hotel' ? 'selected' : '' }}>Hotel</option>
                            <option value="other" {{ optional($arrival)->travel_type == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <input type="date" name="date" class="form-control mb-2" value="{{ isset($arrival->date) ? \Carbon\Carbon::parse($arrival->date)->format('Y-m-d') : '' }}">
                    <input type="time" name="time" class="form-control mb-2" value="{{ isset($arrival->time) ? \Carbon\Carbon::parse($arrival->time)->format('H:i') : '' }}">
                    <input type="text" name="flight_number" class="form-control mb-2" placeholder="Flight Number" value="{{ isset($arrival->flight_number) ? $arrival->flight_number : '' }}">
                    <input type="text" name="airport" class="form-control mb-2" placeholder="Airport" value="{{ isset($arrival->airport) ? $arrival->airport : '' }}">
                    <textarea name="notes" class="form-control mb-2" placeholder="Notes">{{ isset($arrival->notes) ? $arrival->notes : '' }}</textarea>

                    <button class="btn btn-primary btn-sm">Save Arrival</button>
                </form>
            </div>

<!-- DEPARTURE -->
<div class="col-md-6">
    <h5 class="text-danger">Departure</h5>
    <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/travel') }}">
        @csrf
        <input type="hidden" name="direction" value="departure">

        <div class="mb-2">
            <label>Type</label>
            <select name="travel_type" class="form-control">
                <option value="flight" {{ optional($departure)->travel_type == 'flight' ? 'selected' : '' }}>Flight</option>
                <option value="hotel" {{ optional($departure)->travel_type == 'hotel' ? 'selected' : '' }}>Hotel</option>
                <option value="other" {{ optional($departure)->travel_type == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <input type="date" name="date" class="form-control mb-2" value="{{ isset($departure->date) ? \Carbon\Carbon::parse($departure->date)->format('Y-m-d') : '' }}">
        <input type="time" name="time" class="form-control mb-2" value="{{ isset($departure->time) ? \Carbon\Carbon::parse($departure->time)->format('H:i') : '' }}">
        <input type="text" name="flight_number" class="form-control mb-2" placeholder="Flight Number" value="{{ isset($departure->flight_number) ? $departure->flight_number : '' }}">
        <input type="text" name="airport" class="form-control mb-2" placeholder="Airport" value="{{ isset($departure->airport) ? $departure->airport : '' }}">
        <textarea name="notes" class="form-control mb-2" placeholder="Notes">{{ isset($departure->notes) ? $departure->notes : '' }}</textarea>

        <button class="btn btn-danger btn-sm">Save Departure</button>
    </form>
</div>
</div>
</div>

      {{-- ================= MEDICAL ================= --}}
<div class="tab-pane fade show" id="medical">

    <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/medical') }}">
        @csrf

                <textarea class="form-control mb-2" name="medical_conditions" placeholder="medical_conditions">{{ optional($bookingGuest->medical)->medical_conditions }}</textarea>
                <textarea class="form-control mb-2" name="medications" placeholder="medications">{{ optional($bookingGuest->medical)->medications }}</textarea>

                <input class="form-control mb-2" name="food_allergy_flag" placeholder="food_allergy_flag" value="{{ optional($bookingGuest->medical)->food_allergy_flag }}">
                <textarea class="form-control mb-2" name="food_allergy_details" placeholder="food_allergy_details">{{ optional($bookingGuest->medical)->food_allergy_details }}</textarea>

                <textarea class="form-control mb-2" name="other_allergies" placeholder="other_allergies">{{ optional($bookingGuest->medical)->other_allergies }}</textarea>
                <input class="form-control mb-2" name="motion_sickness" placeholder="motion_sickness" value="{{ optional($bookingGuest->medical)->motion_sickness }}">

                <textarea class="form-control mb-2" name="physical_limitations" placeholder="physical_limitations">{{ optional($bookingGuest->medical)->physical_limitations }}</textarea>
                <textarea class="form-control mb-2" name="mobility_notes" placeholder="mobility_notes">{{ optional($bookingGuest->medical)->mobility_notes }}</textarea>

                <input class="form-control mb-2" name="special_assistance_required" placeholder="special_assistance_required" value="{{ optional($bookingGuest->medical)->special_assistance_required }}">

                <input class="form-control mb-2" name="emergency_contact_name" placeholder="emergency_contact_name" value="{{ optional($bookingGuest->medical)->emergency_contact_name }}">
                <input class="form-control mb-2" name="emergency_contact_relationship" placeholder="emergency_contact_relationship" value="{{ optional($bookingGuest->medical)->emergency_contact_relationship }}">
                <input class="form-control mb-2" name="emergency_contact_phone" placeholder="emergency_contact_phone" value="{{ optional($bookingGuest->medical)->emergency_contact_phone }}">
                <input class="form-control mb-2" name="emergency_contact_email" placeholder="emergency_contact_email" value="{{ optional($bookingGuest->medical)->emergency_contact_email }}">

                <button class="btn btn-primary">Save Medical</button>
            </form>

        </div>

{{-- ================= FOOD ================= --}}
<div class="tab-pane fade" id="food">

    <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/food') }}">
        @csrf

                <input class="form-control mb-2" name="dietary_type" placeholder="dietary_type" value="{{ optional($bookingGuest->foodPreference)->dietary_type }}">
                <input class="form-control mb-2" name="allergy_flag" placeholder="allergy_flag" value="{{ optional($bookingGuest->foodPreference)->allergy_flag }}">
                <textarea class="form-control mb-2" name="allergy_details" placeholder="allergy_details">{{ optional($bookingGuest->foodPreference)->allergy_details }}</textarea>

                <textarea class="form-control mb-2" name="dislikes" placeholder="dislikes">{{ optional($bookingGuest->foodPreference)->dislikes }}</textarea>
                <textarea class="form-control mb-2" name="favorite_foods" placeholder="favorite_foods">{{ optional($bookingGuest->foodPreference)->favorite_foods }}</textarea>

                <input class="form-control mb-2" name="breakfast_preference" placeholder="breakfast_preference" value="{{ optional($bookingGuest->foodPreference)->breakfast_preference }}">
                <input class="form-control mb-2" name="snack_preference" placeholder="snack_preference" value="{{ optional($bookingGuest->foodPreference)->snack_preference }}">

                <input class="form-control mb-2" name="lactose_intolerant" placeholder="lactose_intolerant" value="{{ optional($bookingGuest->foodPreference)->lactose_intolerant }}">
                <input class="form-control mb-2" name="gluten_free" placeholder="gluten_free" value="{{ optional($bookingGuest->foodPreference)->gluten_free }}">
                <input class="form-control mb-2" name="halal" placeholder="halal" value="{{ optional($bookingGuest->foodPreference)->halal }}">
                <input class="form-control mb-2" name="vegetarian" placeholder="vegetarian" value="{{ optional($bookingGuest->foodPreference)->vegetarian }}">
                <input class="form-control mb-2" name="vegan" placeholder="vegan" value="{{ optional($bookingGuest->foodPreference)->vegan }}">
                <input class="form-control mb-2" name="pescatarian" placeholder="pescatarian" value="{{ optional($bookingGuest->foodPreference)->pescatarian }}">
                <input class="form-control mb-2" name="kosher" placeholder="kosher" value="{{ optional($bookingGuest->foodPreference)->kosher }}">

                <button class="btn btn-success">Save Food</button>
            </form>

        </div>

{{-- ================= DRINK ================= --}}
<div class="tab-pane fade" id="drink">

    <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/drink') }}">
        @csrf

                <input class="form-control mb-2" name="drinks_alcohol" placeholder="drinks_alcohol" value="{{ optional($bookingGuest->drinkPreference)->drinks_alcohol }}">
                <input class="form-control mb-2" name="wine_preference" placeholder="wine_preference" value="{{ optional($bookingGuest->drinkPreference)->wine_preference }}">
                <input class="form-control mb-2" name="spirits_preference" placeholder="spirits_preference" value="{{ optional($bookingGuest->drinkPreference)->spirits_preference }}">
                <input class="form-control mb-2" name="cocktail_preference" placeholder="cocktail_preference" value="{{ optional($bookingGuest->drinkPreference)->cocktail_preference }}">
                <input class="form-control mb-2" name="beer_preference" placeholder="beer_preference" value="{{ optional($bookingGuest->drinkPreference)->beer_preference }}">
                <input class="form-control mb-2" name="coffee_preference" placeholder="coffee_preference" value="{{ optional($bookingGuest->drinkPreference)->coffee_preference }}">
                <input class="form-control mb-2" name="tea_preference" placeholder="tea_preference" value="{{ optional($bookingGuest->drinkPreference)->tea_preference }}">
                <input class="form-control mb-2" name="soft_drink_preference" placeholder="soft_drink_preference" value="{{ optional($bookingGuest->drinkPreference)->soft_drink_preference }}">
                <input class="form-control mb-2" name="water_preference" placeholder="water_preference" value="{{ optional($bookingGuest->drinkPreference)->water_preference }}">

                <textarea class="form-control mb-2" name="drink_notes" placeholder="drink_notes">{{ optional($bookingGuest->drinkPreference)->drink_notes }}</textarea>

                <button class="btn btn-primary">Save Drinks</button>
            </form>

        </div>

{{-- ================= HOUSEKEEPING ================= --}}
<div class="tab-pane fade" id="housekeeping">

    <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/housekeeping') }}">
        @csrf

                <input class="form-control mb-2" name="room_id" placeholder="room_id" value="{{ optional($bookingGuest->housekeeping)->room_id }}">
                <input class="form-control mb-2" name="bed_setup_preference" placeholder="bed_setup_preference" value="{{ optional($bookingGuest->housekeeping)->bed_setup_preference }}">
                <input class="form-control mb-2" name="pillow_preference" placeholder="pillow_preference" value="{{ optional($bookingGuest->housekeeping)->pillow_preference }}">
                <input class="form-control mb-2" name="towel_change_preference" placeholder="towel_change_preference" value="{{ optional($bookingGuest->housekeeping)->towel_change_preference }}">

                <textarea class="form-control mb-2" name="bathroom_assignment_notes" placeholder="bathroom_assignment_notes">{{ optional($bookingGuest->housekeeping)->bathroom_assignment_notes }}</textarea>
                <textarea class="form-control mb-2" name="cleaning_notes" placeholder="cleaning_notes">{{ optional($bookingGuest->housekeeping)->cleaning_notes }}</textarea>

                <input class="form-control mb-2" name="baby_cot_required" placeholder="baby_cot_required" value="{{ optional($bookingGuest->housekeeping)->baby_cot_required }}">
                <input class="form-control mb-2" name="umbrella_required" placeholder="umbrella_required" value="{{ optional($bookingGuest->housekeeping)->umbrella_required }}">
                <input class="form-control mb-2" name="beach_setup_required" placeholder="beach_setup_required" value="{{ optional($bookingGuest->housekeeping)->beach_setup_required }}">

                <textarea class="form-control mb-2" name="room_comfort_notes" placeholder="room_comfort_notes">{{ optional($bookingGuest->housekeeping)->room_comfort_notes }}</textarea>

                <button class="btn btn-warning">Save Housekeeping</button>
            </form>

        </div>

{{-- ================= SERVICE ================= --}}
<div class="tab-pane fade" id="service">

    <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/service') }}">
        @csrf

                <input class="form-control mb-2" name="vip_level" placeholder="vip_level" value="{{ optional($bookingGuest->serviceNote)->vip_level }}">
                <input class="form-control mb-2" name="celebration_type" placeholder="celebration_type" value="{{ optional($bookingGuest->serviceNote)->celebration_type }}">

                <textarea class="form-control mb-2" name="celebration_notes" placeholder="celebration_notes">{{ optional($bookingGuest->serviceNote)->celebration_notes }}</textarea>

                <input class="form-control mb-2" name="beach_dining_requested" placeholder="beach_dining_requested" value="{{ optional($bookingGuest->serviceNote)->beach_dining_requested }}">
                <textarea class="form-control mb-2" name="excursion_requests" placeholder="excursion_requests">{{ optional($bookingGuest->serviceNote)->excursion_requests }}</textarea>

                <textarea class="form-control mb-2" name="activity_preferences" placeholder="activity_preferences">{{ optional($bookingGuest->serviceNote)->activity_preferences }}</textarea>
                <textarea class="form-control mb-2" name="guest_handling_notes" placeholder="guest_handling_notes">{{ optional($bookingGuest->serviceNote)->guest_handling_notes }}</textarea>
                <textarea class="form-control mb-2" name="internal_service_notes" placeholder="internal_service_notes">{{ optional($bookingGuest->serviceNote)->internal_service_notes }}</textarea>

                <button class="btn btn-success">Save Service</button>
            </form>

        </div>

{{-- ================= DIVING ================= --}}
<div class="tab-pane fade" id="diving">

    <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/diving') }}">
        @csrf

                <input class="form-control mb-2" name="is_diver" placeholder="is_diver" value="{{ optional($bookingGuest->diving)->is_diver }}">
                <input class="form-control mb-2" name="certification_level" placeholder="certification_level" value="{{ optional($bookingGuest->diving)->certification_level }}">
                <input class="form-control mb-2" name="logged_dives" placeholder="logged_dives" value="{{ optional($bookingGuest->diving)->logged_dives }}">
                <input class="form-control mb-2" name="last_dive_date" placeholder="last_dive_date" value="{{ optional($bookingGuest->diving)->last_dive_date }}">

                <textarea class="form-control mb-2" name="diving_notes" placeholder="diving_notes">{{ optional($bookingGuest->diving)->diving_notes }}</textarea>

                <button class="btn btn-primary">Save Diving</button>
            </form>

        </div>

{{-- ================= SURFING ================= --}}
<div class="tab-pane fade" id="surfing">

    <form method="POST" action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/surfing') }}">
        @csrf

                <input class="form-control mb-2" name="is_surfer" placeholder="is_surfer" value="{{ optional($bookingGuest->surfing)->is_surfer }}">
                <input class="form-control mb-2" name="surf_level" placeholder="surf_level" value="{{ optional($bookingGuest->surfing)->surf_level }}">
                <input class="form-control mb-2" name="board_type" placeholder="board_type" value="{{ optional($bookingGuest->surfing)->board_type }}">

                <textarea class="form-control mb-2" name="surfing_notes" placeholder="surfing_notes">{{ optional($bookingGuest->surfing)->surfing_notes }}</textarea>

                <button class="btn btn-primary">Save Surfing</button>
            </form>

        </div>

{{-- ================= DOCUMENTS ================= --}}
<div class="tab-pane fade" id="documents">

    {{-- UPLOAD FORM --}}
    <form method="POST" enctype="multipart/form-data"
          action="{{ url('/bookings/'.$booking->id.'/guests/'.$bookingGuest->id.'/documents') }}">
        @csrf

        <input type="file" class="form-control mb-2" name="file_path" required>

        <select class="form-control mb-2" name="document_type" required>
            <option value="passport">passport</option>
            <option value="insurance">insurance</option>
            <option value="diving_license">diving_license</option>
            <option value="waiver">waiver</option>
            <option value="other">other</option>
        </select>

        <textarea class="form-control mb-2" name="notes" placeholder="notes"></textarea>

        <button class="btn btn-dark">Upload Document</button>
    </form>

    <hr>

    {{-- DOCUMENTS LIST GROUPED BY TYPE --}}
    @php
        $documentsByType = $bookingGuest->documents->groupBy('document_type');
    @endphp

    @foreach($documentsByType as $type => $docs)
        <h5 class="mt-3 text-primary text-uppercase">{{ $type }}</h5>

        <div class="list-group mb-3">
            @foreach($docs as $doc)
                <div class="list-group-item d-flex justify-content-between align-items-center">

                    <div>
                        <strong>{{ $doc->original_filename }}</strong><br>
                        <small>{{ $doc->mime_type }} | {{ number_format($doc->file_size / 1024, 2) }} KB</small>

                        @if($doc->notes)
                            <div class="text-muted small">{{ $doc->notes }}</div>
                        @endif
                    </div>

                    <div>
                        <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank"
                           class="btn btn-sm btn-primary">
                            View
                        </a>
                    </div>

                </div>
            @endforeach
        </div>
    @endforeach

</div>
