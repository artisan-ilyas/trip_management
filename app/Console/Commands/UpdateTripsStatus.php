<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Trip;


class UpdateTripsStatus extends Command
{
    protected $signature = 'trips:update-status';
    protected $description = 'Auto-update trip and booking statuses based on dates';

    public function handle()
    {
        $trips = Trip::get();

        

        foreach ($trips as $trip) {
            if ($trip->start_date <= now() && $trip->status != 'active') {
                $trip->update(['status' => 'active']);
                $trip->bookings()->where('booking_status','confirmed')
                    ->update(['booking_status'=>'active']);
            }
            // dd($trip->end_date < now());

            if ($trip->end_date < now()) {

                $trip->update(['status'=>'completed']);
                $trip->bookings()->whereIn('booking_status',['active','confirmed'])
                    ->update(['booking_status'=>'completed']);
            }
        }

        $this->info('Trips status updated successfully.');
    }
}
