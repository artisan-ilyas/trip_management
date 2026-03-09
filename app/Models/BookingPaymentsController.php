<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BookingPaymentsController extends Controller
{
    public function index($bookingId)
    {
        $booking = Booking::with(['installments','payments'])->findOrFail($bookingId);
        return view('admin.booking.payments', compact('booking'));
    }

    public function store(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $payment = $booking->payments()->create([
            'amount' => $request->amount,
            'paid_at' => $request->paid_at,
            'payment_method' => $request->payment_method,
            'invoice_number' => 'INV-' . now()->format('YmdHis') . '-' . $booking->id,
        ]);

        if ($booking->balance_due <= 0) {
            $booking->status = 'fully_paid';
        } elseif ($booking->amount_paid >= $booking->deposit_amount) {
            $booking->status = 'deposit_paid';
        }
        $booking->save();

        return redirect()->back()->with('success','Payment recorded successfully.');
    }

    public function invoice(BookingPayment $payment)
    {
        $booking = $payment->booking;
        $pdf = PDF::loadView('admin.booking.invoice', compact('booking','payment'));
        return $pdf->stream('invoice-'.$payment->invoice_number.'.pdf');
    }
}
