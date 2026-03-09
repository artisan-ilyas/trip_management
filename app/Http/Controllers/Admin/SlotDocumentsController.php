<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slot;
use Barryvdh\DomPDF\Facade\Pdf;

class SlotDocumentsController extends Controller
{
    // Harbormaster Manifest: full name, DOB, passport, nationality
    public function harbormasterManifest(Slot $slot)
    {
$bookings = $slot->bookings()->with('guests')->get();
// dd($bookings);
        $pdf = PDF::loadView('admin.slots.documents.harbor_manifest', compact('slot', 'bookings'));
        return $pdf->stream("Harbormaster_Manifest_Slot_{$slot->id}.pdf");
    }

    // Crew Guest Sheet: room assignments, dietary info, allergies, equipment sizes, operational notes
    public function crewGuestSheet(Slot $slot)
    {
        $bookings = $slot->bookings()->with(['guests.rooms'])->get();

        $pdf = PDF::loadView('admin.slots.documents.crew_guest_sheet', compact('slot', 'bookings'));
        return $pdf->stream("Crew_Guest_Sheet_Slot_{$slot->id}.pdf");
    }
}
