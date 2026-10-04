<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    // Host: View withdrawals and request form
    public function index()
    {
        $user = auth()->user();
        $withdrawals = $user->withdrawals()->latest()->get();
        $availableBalance = $user->availableBalance();
        $bankAccount = $user->bankAccount;

        return view('host.withdrawals.index', compact('withdrawals', 'availableBalance', 'bankAccount'));
    }

    // Host: Request withdrawal
    public function store(Request $request)
    {
        $user = auth()->user();
        $availableBalance = $user->availableBalance();

        if (!$user->bankAccount) {
            return back()->with('error', 'Please add a bank account before requesting a withdrawal.');
        }

        $request->validate([
            'amount' => ['required', 'numeric', 'min:100', 'max:' . $availableBalance],
        ]);

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Withdrawal request submitted successfully.');
    }

    // Admin: List all withdrawals
    public function adminIndex()
    {
        $withdrawals = Withdrawal::with('user.bankAccount')->latest()->paginate(20);
        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    // Admin: Update status (Approve/Reject)
    public function updateStatus(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,processing,paid',
            'admin_notes' => 'nullable|string'
        ]);

        $withdrawal->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ]);

        return back()->with('success', 'Withdrawal status updated to ' . $request->status . '.');
    }
}
