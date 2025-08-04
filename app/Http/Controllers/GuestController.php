<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Guest;
class GuestController extends Controller
{

   public function store(Request $request)
    {
        // dd($request);
        // $request->validate([
        //     'name' => 'required',
        //     'gender' => 'required',
        //     'email' => 'required|email',
        //     'dob' => 'required|date',
        //     'passport' => 'required',
        //     'nationality' => 'required',
        //     'cabin' => 'required',
        //     'surfLevel' => 'required',
        //     'arrivalFlightDate' => 'required|date',
        //     'arrivalFlightNumber' => 'required',
        //     'arrivalAirport' => 'required',
        //     'arrivalTime' => 'required',
        //     'departureFlightDate' => 'required|date',
        //     'departureFlightNumber' => 'required',
        //     'departureAirport' => 'required',
        //     'departureTime' => 'required',
        // ]);

        $guest = Guest::create([
            'trip_id' => $request->trip_id,
            'name' => $request->name,
            'gender' => $request->gender,
            'email' => $request->email,
            'dob' => $request->dob,
            'passport' => $request->passport,
            'nationality' => $request->nationality,
            'cabin' => $request->cabin,
            'surfLevel' => $request->surfLevel,
            'boardDetails' => $request->boardDetails,

            'arrivalFlightDate' => $request->arrivalFlightDate,
            'arrivalFlightNumber' => $request->arrivalFlightNumber,
            'arrivalAirport' => $request->arrivalAirport,
            'arrivalTime' => $request->arrivalTime,
            'hotelPickup' => $request->hotelPickup,
            'departureFlightDate' => $request->departureFlightDate,
            'departureFlightNumber' => $request->departureFlightNumber,
            'departureAirport' => $request->departureAirport,
            'departureTime' => $request->departureTime,

            'medicalDietary' => $request->medicalDietary,
            'specialRequests' => $request->specialRequests,
            'insuranceName' => $request->insuranceName,
            'policyNumber' => $request->policyNumber,
            'emergencyName' => $request->emergencyName,
            'emergencyRelation' => $request->emergencyRelation,
            'emergencyPhone' => $request->emergencyPhone,
            'guestWhatsapp' => $request->guestWhatsapp,
            'guestEmail' => $request->guestEmail,
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('guests/images', 'public');
            $guest->update(['image_path' => $imagePath]);
        }

        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('guests/pdfs', 'public');
            $guest->update(['pdf_path' => $pdfPath]);
        }

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('guests/videos', 'public');
            $guest->update(['video_path' => $videoPath]);
        }

        if ($request->has('otherGuests')) {
            foreach ($request->otherGuests as $other) {
                $guest->otherGuests()->create([
                    'name' => $other['name'] ?? null,
                    'gender' => $other['gender'] ?? null,
                    'email' => $other['email'] ?? null,
                    'password' => $other['password'] ?? null,
                    'dob' => $other['dob'] ?? null,
                    'passport' => $other['passport'] ?? null,
                    'nationality' => $other['nationality'] ?? null,
                    'cabin' => $other['cabin'] ?? null,
                    'surfLevel' => $other['surfLevel'] ?? null,
                    'boardDetails' => $other['boardDetails'] ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Guest form submitted successfully.');
    }

    public function show($token)
    {
        $trip = Trip::where('guest_form_token', $token)->firstOrFail();
        return view('guests.guest_form', compact('trip'));
    }

    public function submit(Request $request, $token)
    {
        $trip = Trip::where('guest_form_token', $token)->firstOrFail();

        Guest::create([
            'trip_id' => $trip->id,
            'name'    => $request->name,
            'email'   => $request->email,
            // other guest fields...
        ]);

        return redirect()->back()->with('success', 'Guest info submitted!');
    }
}
