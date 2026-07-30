<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SmsHistory;
use App\Models\Setting;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        $subscribedCount = Customer::where('subscribed', true)->count();
        
        $sentTodayCount = SmsHistory::whereDate('created_at', today())
            ->where('status', 'Sent')
            ->count();

        $recentSmsActivity = SmsHistory::with('customer')
            ->latest()
            ->take(5)
            ->get();

        $logs = SmsHistory::with('customer')
            ->latest()
            ->get();

        return view('sms.index', compact('customers', 'subscribedCount', 'sentTodayCount', 'recentSmsActivity', 'logs'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:single,multiple,all_subscribed',
            'customer_id' => 'nullable|required_if:target_type,single|exists:customers,id',
            'customer_ids' => 'nullable|required_if:target_type,multiple|array',
            'message' => 'required|string|max:1000',
        ]);

        $recipients = collect();

        if ($request->target_type === 'single') {
            $recipients->push(Customer::find($request->customer_id));
        } elseif ($request->target_type === 'multiple') {
            $recipients = Customer::whereIn('id', $request->customer_ids)->get();
        } else {
            $recipients = Customer::where('subscribed', true)->get();
        }

        if ($recipients->isEmpty()) {
            return back()->with('error', 'No recipients found matching selections.');
        }

        $senderId = Setting::get('sms_sender_id', 'OHAIYOJP');

        foreach ($recipients as $recipient) {
            // Simulated delivery logic: mock successful send unless the number has "failed" in it
            $status = str_contains($recipient->phone, 'failed') ? 'Failed' : 'Sent';

            SmsHistory::create([
                'customer_id' => $recipient->id,
                'phone' => $recipient->phone,
                'message' => $request->message,
                'status' => $status,
            ]);
        }

        return redirect()->route('sms.index')->with('success', 'Simulated SMS dispatch completed.');
    }
}
