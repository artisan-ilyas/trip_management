<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boat;
use App\Models\Trip;

class FleetCalendarController extends Controller
{
    public function resources()
    {
        $resources = [];

        $boats = Boat::with('trip')->get();

        foreach ($boats as $boat) {
            $resources[] = [
                'id' => 'boat-'.$boat->id,
                'title' => $boat->name,
            ];

            foreach ($boat->trips as $trip) {
                $resources[] = [
                    'id' => 'trip-'.$trip->id,
                    'title' => $trip->title,
                    'parentId' => 'boat-'.$boat->id,
                ];
            }
        }

        return response()->json($resources);
    }


    public function events()
    {
        $events = [];

        $trips = Trip::all();

        foreach ($trips as $trip) {
            $events[] = [
                'id' => 'trip-'.$trip->id,
                'resourceId' => 'trip-'.$trip->id,
                'title' => $trip->title,
                'start' => $trip->start_date,
                'end' => $trip->end_date,
                'backgroundColor' => '#28a745',
                'borderColor' => '#28a745',
                'extendedProps' => [
                    'type' => 'trip',
                    'trip_id' => $trip->id
                ]
            ];
        }

        return response()->json($events);
    }


}
