@extends('layouts.admin')
@section('content')
    <div class="content-wrapper">
        <div class="container pt-3">
                <h4>Payments for Booking #{{ $booking->id }}</h4>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @foreach (['success','error'] as $msg)
                        @if(session($msg))
                            <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }}">
                                {{ session($msg) }}
                            </div>
                        @endif
                    @endforeach

                    <p>Total Price: {{ $booking->price }} USD</p>
                    <p>Deposit: {{ $booking->deposit_amount ?? 0 }} (Due: {{ \Carbon\Carbon::parse($booking->deposit_due_date)->format('d-m-Y') ?? '-' }})</p>
                    <p>Amount Paid: {{ $booking->amount_paid }}</p>
                    <p>Balance Due: {{ $booking->balance_due }}</p>
                    <p>Status: {{ $booking->status }}</p>

                    <hr>

                    <h5>Add Payment</h5>
                    <form method="POST" action="{{ route('admin.bookings.payments.store', $booking) }}">
                        @csrf
                        <div class="mb-2">
                            <label>Amount</label>
                            <input type="number" name="amount" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-2">
                            <label>Paid At</label>
                            <input type="date" name="paid_at" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <button class="btn btn-success">Add Payment</button>
                    </form>

                    <hr>

                    <h5>Payments Ledger</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Amount</th>
                                <th>Date Paid</th>
                                <th>Method</th>
                                <th>Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->payments as $payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $payment->amount }}</td>
                                <td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('d-m-Y') }}</td>
                                <td>{{ $payment->payment_method }}</td>
                                <td>
                                    <a href="{{ route('admin.bookings.payments.invoice', $payment) }}" class="btn btn-sm btn-primary" target="_blank">Invoice</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
            </div>
    </div>
@endsection
