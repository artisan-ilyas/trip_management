<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    Agent, Booking, Slot, Boat, BookingGuestRoom,
    CancellationPolicy, Company, Currency, Guest,
    PaymentPolicy, Port, RatePlan, Region, Room, Salesperson
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::query()
            ->with([
                'slot',
                'boat',
                'boats',
                'room',
                'rooms',
            ])
            ->select('bookings.*')
            ->distinct()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.booking.index', compact('bookings'));
    }

    public function create()
    {
        $slots = Slot::with(['boat.rooms', 'boats.rooms'])->get();

        // Slots already booked by PRIVATE CHARTER only
        $bookedSlotIds = Booking::leftJoin('slots', 'bookings.slot_id', '=', 'slots.id')
            ->whereNotNull('bookings.slot_id')
            ->where('slots.slot_type', 'Private Charter')
            ->pluck('bookings.slot_id')
            ->unique()
            ->toArray();

        // Room usage per slot (per booking) including guest IDs
        $roomUsageBySlot = [];

        foreach ($slots as $slot) {
            // Only active bookings (exclude cancelled)
            $bookingIds = Booking::where('slot_id', $slot->id)
                ->whereIn('status', ['Pending', 'DP Paid', 'Full Paid'])
                ->pluck('id');

            // Room usage counts per room
            $roomUsage = BookingGuestRoom::whereIn('booking_id', $bookingIds)
                ->select('room_id', DB::raw('COUNT(*) as used'))
                ->groupBy('room_id')
                ->pluck('used', 'room_id')
                ->toArray();

            // Guest IDs per room per booking
            $roomGuests = BookingGuestRoom::whereIn('booking_id', $bookingIds)
                ->get()
                ->groupBy(function ($item) {
                    return $item->booking_id . '_' . $item->room_id;
                })
                ->map(function ($items) {
                    return $items->pluck('guest_id')->toArray();
                })
                ->toArray();

            // Initialize room usage for this slot
            $roomUsageBySlot[$slot->id] = [];

            // Process rooms for main boat
            foreach ($slot->boat->rooms ?? [] as $room) {
                $roomId = $room->id;
                // dd($roomUsage[$roomId]);
                $roomUsageBySlot[$slot->id][$roomId] = [
                    'used' => $roomUsage[$roomId] ?? 0,
                    'guests' => [],
                ];

                // Merge guest IDs per booking
                foreach ($bookingIds as $bookingId) {
                    $key = $bookingId . '_' . $roomId;
                    if (isset($roomGuests[$key])) {
                        $roomUsageBySlot[$slot->id][$roomId]['guests'] = array_merge(
                            $roomUsageBySlot[$slot->id][$roomId]['guests'],
                            $roomGuests[$key]
                        );
                    }
                }
            }

            // Process rooms for any additional boats linked to the slot
            if ($slot->boats) {
                foreach ($slot->boats as $boat) {
                    foreach ($boat->rooms ?? [] as $room) {
                        $roomId = $room->id;
                        $roomUsageBySlot[$slot->id][$roomId] = [
                            'used' => $roomUsage[$roomId] ?? 0,
                            'guests' => [],
                        ];

                        foreach ($bookingIds as $bookingId) {
                            $key = $bookingId . '_' . $roomId;
                            if (isset($roomGuests[$key])) {
                                $roomUsageBySlot[$slot->id][$roomId]['guests'] = array_merge(
                                    $roomUsageBySlot[$slot->id][$roomId]['guests'],
                                    $roomGuests[$key]
                                );
                            }
                        }
                    }
                }
            }
        }


        return view('admin.booking.create', [
            'slots' => $slots->toArray(),
            'agents' => Agent::orderBy('first_name')->get(),
            'guests' => Guest::orderBy('first_name')->get(),
            'ratePlans' => RatePlan::with('rules')->get(),
            'paymentPolicies' => PaymentPolicy::all(),
            'cancellationPolicies' => CancellationPolicy::with('rules')->get(),
            'boats' => Boat::with('rooms')->get(),
            'regions' => Region::all(),
            'ports' => Port::all(),
            'salespersons' => Salesperson::orderBy('name')->get(),
            'currencies' => Currency::all(),
            'companies' => auth()->user()->hasRole('admin')
                ? Company::all()
                : Company::where('id', auth()->user()->company_id)->get(),

            'bookedSlotIds' => $bookedSlotIds,
            'roomUsageBySlot' => $roomUsageBySlot,
        ]);
    }





    public function store(Request $request)
    {
        // dd($request->all());
        // ------------------------------
        // VALIDATION
        // ------------------------------
        $validator = Validator::make($request->all(), [
            'source' => 'required|in:Direct,Agent',
            'agent_id' => 'nullable|required_if:source,Agent',
            'lead_customer_id' => 'required|exists:guests,id',
            'guest_rooms' => 'nullable',
            'price' => 'required|numeric',
            'currency' => 'required',
            'salesperson_id' => 'required',
            'booking_status' => 'required',

            // Existing slot OR inline slot
            'slot_id' => 'nullable|exists:slots,id',

            // Inline slot fields (required if slot_id not present)
            'slot_type' => 'required_without:slot_id',
            'boats_allowed' => 'required_without:slot_id|array',
            'region_id' => 'required_without:slot_id',
            'departure_port_id' => 'required_without:slot_id',
            'arrival_port_id' => 'required_without:slot_id',
            'start_date' => 'required_without:slot_id|date',
            'end_date' => 'required_without:slot_id|date|after_or_equal:start_date',
            'duration_nights' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $guestRooms = [];

        if ($request->guest_rooms) {

            foreach ($request->guest_rooms as $roomId => $guestString) {

                $ids = array_filter(explode(',', $guestString));

                if ($ids) {
                    $guestRooms[$roomId] = $ids;
                }
            }
        }

        DB::beginTransaction();

        // try {

            // ------------------------------
            // STEP 1: RESOLVE OR CREATE SLOT
            // ------------------------------
            if ($request->slot_id) {

                // EXISTING SLOT
                $slot = Slot::with(['boat.rooms', 'boats.rooms'])
                    ->lockForUpdate()
                    ->findOrFail($request->slot_id);

            } else {

                // ============================
                // INLINE SLOT CREATION
                // ============================

                $status = $request->slot_status ?? 'Available';

                // Auto Blocked for Maintenance/Docking
                if (in_array($request->slot_type, ['Maintenance', 'Docking'])) {
                    $status = 'Blocked';
                }

                // Notes required if Blocked or On-Hold
                if (in_array($status, ['Blocked', 'On-Hold']) && empty($request->notes)) {
                    DB::rollBack();
                    return back()->withErrors([
                        'notes' => 'Notes are required for this status.'
                    ])->withInput();
                }

                // ❌ BOAT + DATE COLLISION CHECK
                if ($this->hasBoatDateCollision(
                    $request->boats_allowed,
                    $request->start_date,
                    $request->end_date
                )) {
                    DB::rollBack();
                    return back()->withErrors([
                        'boats_allowed' =>
                            'Collision detected: a selected vessel already has a slot between '
                            . $request->start_date . ' and ' . $request->end_date
                    ])->withInput();
                }

                // CREATE SLOT
                $slot = Slot::create([
                    'slot_type' => $request->slot_type,
                    'status' => $status,
                    'region_id' => $request->region_id,
                    'departure_port_id' => $request->departure_port_id,
                    'arrival_port_id' => $request->arrival_port_id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'notes' => $request->notes,
                    'duration_nights' => $request->duration_nights,
                    'company_id' => auth()->user()->company_id,
                ]);

                // Attach Boats
                $slot->boats()->sync($request->boats_allowed);

                $slot->load(['boats.rooms']);
            }

            $slotType = $slot->slot_type ?? 'Open Trip';

            // ------------------------------
            // PRIVATE CHARTER CHECK
            // ------------------------------
            if (
                $slotType === 'Private Charter' &&
                Booking::where('slot_id', $slot->id)->exists()
            ) {
                DB::rollBack();
                return back()->withErrors([
                    'slot_id' => 'This Private Charter slot is already booked.'
                ])->withInput();
            }

            // ------------------------------
            // SLOT ROOM CAPACITY CHECK
            // ------------------------------
            $usedRoomCapacity =
            BookingGuestRoom::whereIn(
                'booking_id',
                Booking::where('slot_id', $slot->id)->pluck('id')
            )
            ->select('room_id', DB::raw('COUNT(*) as used'))
            ->groupBy('room_id')
            ->pluck('used', 'room_id')
            ->toArray();

            // if ($request->filled('guest_rooms')) {

            //     $rooms = Room::whereIn('id', array_keys($request->guest_rooms))
            //         ->get()
            //         ->keyBy('id');

            //     foreach ($request->guest_rooms as $roomId => $guestIds) {

            //         $guestIds = array_filter((array) $guestIds);
            //         if (empty($guestIds)) continue;

            //         if (!isset($rooms[$roomId])) {
            //             DB::rollBack();
            //             return back()->withErrors([
            //                 'guest_rooms' => 'Invalid room selected'
            //             ])->withInput();
            //         }

            //         $room = $rooms[$roomId];
            //         $capacity = $room->capacity + $room->extra_beds;
            //         $alreadyUsed = $usedRoomCapacity[$roomId] ?? 0;


            //         if (($alreadyUsed + count($guestIds)) > $capacity) {
            //             DB::rollBack();
            //             return back()->withErrors([
            //                 'guest_rooms' => "Room capacity exceeded."
            //             ])->withInput();
            //         }
            //     }
            // }

            // ------------------------------
            // COLLECT GUEST IDS
            // ------------------------------
            $guestIds = collect($request->guest_rooms ?? [])
                ->flatten()
                ->merge($request->guests_without_room ?? [])
                ->unique()
                ->values()
                ->toArray();

            if (empty($guestIds)) {
                DB::rollBack();
                return back()->withErrors([
                    'guest_rooms' => 'At least one guest is required'
                ])->withInput();
            }

            $leadGuest = Guest::findOrFail($request->lead_customer_id);

            if (!$leadGuest) {
                DB::rollBack();
                return back()->withErrors([
                    'guest_rooms' => 'Invalid guest selected'
                ])->withInput();
            }


            $currency = Currency::findOrFail($request->currency);

            $rate = (float) $currency->rate;
            $price = (float) $request->price;

            // ------------------------------
            // CREATE BOOKING
            // ------------------------------
            $status = $request->booking_status;

            // If deposit amount exists → status becomes DP Paid
            if ($request->deposit_amount >= $price) {
                $status = 'Full Paid';
            } elseif ($request->deposit_amount > 0) {
                $status = 'DP Paid';
            } else {
                $status = 'Pending';
            }


            $booking = Booking::create([
                'slot_id' => $slot->id,
                'boat_id' => $slot->boats->first()->id ?? null,
                'room_id' => $slotType === 'Private Charter'
                    ? null
                    : array_key_first($request->guest_rooms ?? []),

                'guest_name' => $leadGuest->first_name . ' ' . $leadGuest->last_name,
                'guest_count' => count($guestIds),

                'source' => $request->source,
                'agent_id' => $request->agent_id,
                'rate_plan_id' => $request->rate_plan_id,
                'payment_policy_id' => $request->payment_policy_id,
                'cancellation_policy_id' => $request->cancellation_policy_id,
                'notes' => $request->notes,

                // Status logic
                'status' => $status,

                // pricing
                'price' => $price,
                'currency' => $currency->name,

                // frozen exchange rate
                'exchange_rate' => $rate,
                'exchange_rate_timestamp' => now(),

                'deposit_amount' => $request->deposit_amount,
                'deposit_due_date' => $request->deposit_due_date,
                'final_balance_due_date' => $request->final_balance_due_date,

                // USD conversion
                'price_usd' => round($price * $rate, 2),

                'salesperson_id' => $request->salesperson_id,
            ]);

            // Store payment when payment is made
            if ($request->deposit_amount) {
                $payment = $booking->payments()->create([
                    'amount' => $request->deposit_amount,
                    'paid_at' => now(),
                    'payment_method' => 'Cash', // Default method, can be extended to accept from request
                    'invoice_number' => 'INV-' . now()->format('YmdHis') . '-' . $booking->id,
                ]);
            }

            // ------------------------------
            // ATTACH ROOMS & GUESTS
            // ------------------------------
            if (!empty($guestRooms)) {

                $booking->rooms()->sync(array_keys($guestRooms));

                foreach ($guestRooms as $roomId => $gIds) {

                    foreach ($gIds as $guestId) {

                        BookingGuestRoom::create([
                            'booking_id' => $booking->id,
                            'room_id' => $roomId,
                            'guest_id' => $guestId,
                        ]);
                    }
                }
            }

            if (!empty($request->guests_without_room)) {
                $booking->guests()->syncWithoutDetaching($request->guests_without_room);
            }

            // ------------------------------
            // ATTACH BOATS TO BOOKING
            // ------------------------------
            $boatsToAttach = $slot->boats->pluck('id')->toArray();

            if ($boatsToAttach) {
                $booking->boats()->sync($boatsToAttach);
            }

            // ------------------------------
            // UPDATE SLOT STATUS IF PRIVATE
            // ------------------------------
            if ($slotType === 'Private Charter') {
                $slot->update(['status' => 'Booked']);
            }

            DB::commit();

            return redirect()
                ->route('admin.bookings.index')
                ->with('success', 'Booking created successfully');

        // } catch (\Throwable $e) {

        //     DB::rollBack();
        //     report($e);

        //     return back()->withErrors([
        //         'error' => 'Something went wrong while creating the booking.'
        //     ])->withInput();
        // }
    }



public function edit(Booking $booking)
{
    // Load all slots with boats and rooms
    $slots = Slot::with(['boat.rooms', 'boats.rooms'])->get();

    // Slots already booked by PRIVATE CHARTER (excluding current booking)
    $bookedSlotIds = Booking::where('bookings.id', '!=', $booking->id) // <-- specify table
        ->leftJoin('slots', 'bookings.slot_id', '=', 'slots.id')
        ->whereNotNull('bookings.slot_id')
        ->where('slots.slot_type', 'Private Charter')
        ->pluck('bookings.slot_id')
        ->unique()
        ->toArray();

    // Room usage per slot (per booking) including guest IDs
    $roomUsageBySlot = [];

    foreach ($slots as $slot) {
        // Only active bookings (exclude cancelled), exclude current booking
        $bookingIds = Booking::where('slot_id', $slot->id)
            ->where('id', '!=', $booking->id)
            ->whereIn('status', ['Pending', 'DP Paid', 'Full Paid'])
            ->pluck('id');

        // Room usage counts per room
        $roomUsage = BookingGuestRoom::whereIn('booking_id', $bookingIds)
            ->select('room_id', DB::raw('COUNT(*) as used'))
            ->groupBy('room_id')
            ->pluck('used', 'room_id')
            ->toArray();

        // Guest IDs per room per booking
        $roomGuests = BookingGuestRoom::whereIn('booking_id', $bookingIds)
            ->get()
            ->groupBy(function ($item) {
                return $item->booking_id . '_' . $item->room_id;
            })
            ->map(function ($items) {
                return $items->pluck('guest_id')->toArray();
            })
            ->toArray();

        // Initialize room usage for this slot
        $roomUsageBySlot[$slot->id] = [];

        // Process rooms for main boat
        foreach ($slot->boat->rooms ?? [] as $room) {
            $roomId = $room->id;
            $roomUsageBySlot[$slot->id][$roomId] = [
                'used' => $roomUsage[$roomId] ?? 0,
                'guests' => [],
            ];

            // Merge guest IDs per booking
            foreach ($bookingIds as $bId) {
                $key = $bId . '_' . $roomId;
                if (isset($roomGuests[$key])) {
                    $roomUsageBySlot[$slot->id][$roomId]['guests'] = array_merge(
                        $roomUsageBySlot[$slot->id][$roomId]['guests'],
                        $roomGuests[$key]
                    );
                }
            }
        }

        // Process rooms for additional boats linked to the slot
        if ($slot->boats) {
            foreach ($slot->boats as $boat) {
                foreach ($boat->rooms ?? [] as $room) {
                    $roomId = $room->id;
                    $roomUsageBySlot[$slot->id][$roomId] = [
                        'used' => $roomUsage[$roomId] ?? 0,
                        'guests' => [],
                    ];

                    foreach ($bookingIds as $bId) {
                        $key = $bId . '_' . $roomId;
                        if (isset($roomGuests[$key])) {
                            $roomUsageBySlot[$slot->id][$roomId]['guests'] = array_merge(
                                $roomUsageBySlot[$slot->id][$roomId]['guests'],
                                $roomGuests[$key]
                            );
                        }
                    }
                }
            }
        }
    }

    // Preload current booking's guest-room assignments
    $bookingGuestRooms = BookingGuestRoom::where('booking_id', $booking->id)->get();
    $bookingRoomGuests = $bookingGuestRooms
    ->groupBy('room_id')
    ->map(function ($items) {
        return $items->pluck('guest_id')->toArray();
    })
    ->toArray();
    $bookingGuests = $bookingGuestRooms->map(function ($bgr) {
        $guest = $bgr->guest; // assuming relation exists
        return [
            'id' => $guest->id,
            'first_name' => $guest->first_name,
            'last_name' => $guest->last_name,
        ];
    });

    return view('admin.booking.edit', [
        'booking' => $booking,
        'slots' => $slots->toArray(),
        'agents' => Agent::orderBy('first_name')->get(),
        'guests' => Guest::orderBy('first_name')->get(),
        'ratePlans' => RatePlan::with('rules')->get(),
        'paymentPolicies' => PaymentPolicy::all(),
        'cancellationPolicies' => CancellationPolicy::with('rules')->get(),
        'boats' => Boat::with('rooms')->get(),
        'regions' => Region::all(),
        'ports' => Port::all(),
        'salespersons' => Salesperson::orderBy('name')->get(),
        'currencies' => Currency::all(),
        'companies' => auth()->user()->hasRole('admin')
            ? Company::all()
            : Company::where('id', auth()->user()->company_id)->get(),

        'bookedSlotIds' => $bookedSlotIds,
        'roomUsageBySlot' => $roomUsageBySlot,
        'bookingGuests' => $bookingGuests, // preload guests already on this booking
        'bookingRoomGuests' => $bookingRoomGuests, // preload guest-room assignments for this booking
    ]);
}


public function update(Request $request, Booking $booking)
{
    $validator = Validator::make($request->all(), [
        'source' => 'required|in:Direct,Agent',
        'agent_id' => 'nullable|required_if:source,Agent',
        'lead_customer_id' => 'required|exists:guests,id',
        'guest_rooms' => 'nullable',
        'price' => 'required|numeric',
        'currency' => 'required',
        'salesperson_id' => 'required',
        'booking_status' => 'required',
        'slot_id' => 'nullable|exists:slots,id',
        'slot_type' => 'required_without:slot_id',
        'boats_allowed' => 'required_without:slot_id|array',
        'region_id' => 'required_without:slot_id',
        'departure_port_id' => 'required_without:slot_id',
        'arrival_port_id' => 'required_without:slot_id',
        'start_date' => 'required_without:slot_id|date',
        'end_date' => 'required_without:slot_id|date|after_or_equal:start_date',
        'duration_nights' => 'nullable|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    $guestRooms = [];
    if ($request->guest_rooms) {
        foreach ($request->guest_rooms as $roomId => $guestString) {
            $ids = array_filter(explode(',', $guestString));
            if ($ids) {
                $guestRooms[$roomId] = $ids;
            }
        }
    }

    DB::beginTransaction();

    try {
        // ------------------------------
        // RESOLVE OR CREATE SLOT
        // ------------------------------
        if ($request->slot_id) {
            $slot = Slot::with(['boat.rooms', 'boats.rooms'])
                ->lockForUpdate()
                ->findOrFail($request->slot_id);
        } else {
            $status = $request->slot_status ?? 'Available';
            if (in_array($request->slot_type, ['Maintenance', 'Docking'])) {
                $status = 'Blocked';
            }

            if (in_array($status, ['Blocked', 'On-Hold']) && empty($request->notes)) {
                DB::rollBack();
                return back()->withErrors(['notes' => 'Notes are required for this status.'])->withInput();
            }

            if ($this->hasBoatDateCollision(
                $request->boats_allowed,
                $request->start_date,
                $request->end_date
            )) {
                DB::rollBack();
                return back()->withErrors([
                    'boats_allowed' => 'Collision detected: a selected vessel already has a slot between '
                        . $request->start_date . ' and ' . $request->end_date
                ])->withInput();
            }

            $slot = Slot::create([
                'slot_type' => $request->slot_type,
                'status' => $status,
                'region_id' => $request->region_id,
                'departure_port_id' => $request->departure_port_id,
                'arrival_port_id' => $request->arrival_port_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'notes' => $request->notes,
                'duration_nights' => $request->duration_nights,
                'company_id' => auth()->user()->company_id,
            ]);

            $slot->boats()->sync($request->boats_allowed);
            $slot->load(['boats.rooms']);
        }

        $slotType = $slot->slot_type ?? 'Open Trip';

        // ------------------------------
        // PRIVATE CHARTER CHECK
        // ------------------------------
        if ($slotType === 'Private Charter' &&
            Booking::where('slot_id', $slot->id)->where('id', '!=', $booking->id)->exists()) {
            DB::rollBack();
            return back()->withErrors([
                'slot_id' => 'This Private Charter slot is already booked.'
            ])->withInput();
        }

        // ------------------------------
        // COLLECT GUEST IDS
        // ------------------------------
        $guestIds = collect($request->guest_rooms ?? [])
            ->flatten()
            ->merge($request->guests_without_room ?? [])
            ->unique()
            ->values()
            ->toArray();

        if (empty($guestIds)) {
            DB::rollBack();
            return back()->withErrors(['guest_rooms' => 'At least one guest is required'])->withInput();
        }

        $leadGuest = Guest::findOrFail($request->lead_customer_id);
        $currency = Currency::findOrFail($request->currency);
        $rate = (float) $currency->rate;
        $price = (float) $request->price;

        // ------------------------------
        // UPDATE BOOKING
        // ------------------------------
        $status = $request->booking_status;
        if ($request->deposit_amount >= $price) {
            $status = 'Full Paid';
        } elseif ($request->deposit_amount > 0) {
            $status = 'DP Paid';
        } else {
            $status = 'Pending';
        }

        $booking->update([
            'slot_id' => $slot->id,
            'boat_id' => $slot->boats->first()->id ?? null,
            'room_id' => $slotType === 'Private Charter' ? null : array_key_first($request->guest_rooms ?? []),
            'guest_name' => $leadGuest->first_name . ' ' . $leadGuest->last_name,
            'guest_count' => count($guestIds),
            'source' => $request->source,
            'agent_id' => $request->agent_id,
            'rate_plan_id' => $request->rate_plan_id,
            'payment_policy_id' => $request->payment_policy_id,
            'cancellation_policy_id' => $request->cancellation_policy_id,
            'notes' => $request->notes,
            'status' => $status,
            'price' => $price,
            'currency' => $currency->name,
            'exchange_rate' => $rate,
            'exchange_rate_timestamp' => now(),
            'deposit_amount' => $request->deposit_amount,
            'deposit_due_date' => $request->deposit_due_date,
            'final_balance_due_date' => $request->final_balance_due_date,
            'price_usd' => round($price * $rate, 2),
            'salesperson_id' => $request->salesperson_id,
        ]);

        // ------------------------------
        // HANDLE PAYMENTS
        // ------------------------------
        if ($request->deposit_amount) {
            $payment = $booking->payments()->first();
            if ($payment) {
                $payment->update([
                    'amount' => $request->deposit_amount,
                    'paid_at' => now(),
                    'payment_method' => 'Cash',
                    'invoice_number' => 'INV-' . now()->format('YmdHis') . '-' . $booking->id,
                ]);
            } else {
                $booking->payments()->create([
                    'amount' => $request->deposit_amount,
                    'paid_at' => now(),
                    'payment_method' => 'Cash',
                    'invoice_number' => 'INV-' . now()->format('YmdHis') . '-' . $booking->id,
                ]);
            }
        } else {
            $booking->payments()->delete();
        }

        // ------------------------------
        // ATTACH ROOMS & GUESTS
        // ------------------------------
        $booking->rooms()->sync(array_keys($guestRooms));
        BookingGuestRoom::where('booking_id', $booking->id)->delete();

        foreach ($guestRooms as $roomId => $gIds) {
            foreach ($gIds as $guestId) {
                BookingGuestRoom::create([
                    'booking_id' => $booking->id,
                    'room_id' => $roomId,
                    'guest_id' => $guestId,
                ]);
            }
        }

        if (!empty($request->guests_without_room)) {
            $booking->guests()->sync($request->guests_without_room);
        }

        // ------------------------------
        // ATTACH BOATS TO BOOKING
        // ------------------------------
        $boatsToAttach = $slot->boats->pluck('id')->toArray();
        $booking->boats()->sync($boatsToAttach);

        // ------------------------------
        // UPDATE SLOT STATUS IF PRIVATE
        // ------------------------------
        if ($slotType === 'Private Charter') {
            $slot->update(['status' => 'Booked']);
        }

        DB::commit();

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking updated successfully');

    } catch (\Throwable $e) {
        DB::rollBack();
        report($e);

        return back()->withErrors([
            'error' => 'Something went wrong while updating the booking.'
        ])->withInput();
    }
}

    public function destroy(Booking $booking)
    {
        // Prevent deletion if slot already started
        // if ($booking->slot && Carbon::parse($booking->slot->start_date)->isPast()) {
        //     return back()->withErrors(['error' => 'Cannot delete booking because the slot has already started.']);
        // }

        $booking->delete();
        return back()->with('success', 'Booking deleted successfully.');
    }

    protected function hasBoatDateCollision(array $boatIds, string $startDate, string $endDate): bool
    {
        return Slot::whereIn('boat_id', $boatIds)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();
    }
}
