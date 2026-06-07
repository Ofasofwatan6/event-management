<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function showForm()
    {
        $eligiblePayments = Payment::with([
            'eventRegistration.event',
            'paymentMethod',
        ])
            ->where('status', 'approved')
            ->whereHas('eventRegistration.participant', fn ($q) => $q->where('user_id', auth()->id()))
            ->whereDoesntHave('refund')
            ->latest()
            ->get();

        return view('user.refund-request', compact('eligiblePayments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'reason' => 'required|string|max:255',
            'description' => 'required|string',
            'bank_name' => 'required|string|max:255',
            'account_number' => ['required', 'regex:/^\d+$/', 'max:30'],
            'account_holder' => 'required|string|max:255',
        ], [
            'required' => 'Wajib Diisi',
            'description.required' => 'Kolom ini tidak boleh kosong',
        ]);

        $payment = Payment::with('eventRegistration.participant')
            ->findOrFail($validated['payment_id']);

        abort_if(
            $payment->eventRegistration->participant->user_id !== auth()->id(),
            403
        );

        if ($payment->status !== 'approved') {
            return back()->with('error', 'Refund hanya bisa diajukan untuk pembayaran yang sudah terverifikasi.');
        }

        if ($payment->refund) {
            return back()->with('error', 'Pembayaran ini sudah memiliki pengajuan refund.');
        }

        Refund::create([
            'payment_id' => $payment->id,
            'event_registration_id' => $payment->event_registration_id,
            'user_id' => auth()->id(),
            'reason' => $validated['reason'],
            'description' => $validated['description'],
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_holder' => $validated['account_holder'],
            'amount' => $payment->amount,
            'status' => 'pending',
        ]);

        return redirect()->route('refund.status')
            ->with('success', 'Pengajuan refund berhasil dikirim.');
    }

    public function status()
    {
        $refunds = Refund::with(['payment.eventRegistration.event'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $totalRefunds = $refunds->count();
        $totalProcessed = $refunds->whereIn('status', ['pending', 'approved'])->sum('amount');
        $totalApproved = $refunds->where('status', 'approved')->sum('amount');

        return view('user.refund-status', compact(
            'refunds',
            'totalRefunds',
            'totalProcessed',
            'totalApproved'
        ));
    }
}
