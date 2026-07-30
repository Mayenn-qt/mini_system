<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        $storeName = Setting::get('store_name', 'Ohaiyo Japan Surplus');
        $storeAddress = Setting::get('store_address', '');
        $storePhone = Setting::get('store_phone', '');
        $storeEmail = Setting::get('store_email', '');
        $smsApiKey = Setting::get('sms_gateway_api_key', '');
        $smsSenderId = Setting::get('sms_sender_id', 'OHAIYOJP');

        return view('settings.index', compact(
            'storeName', 'storeAddress', 'storePhone', 'storeEmail', 'smsApiKey', 'smsSenderId'
        ));
    }

    public function updateStore(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'nullable|string|max:500',
            'store_phone' => 'nullable|string|max:50',
            'store_email' => 'nullable|email|max:255',
            'sms_gateway_api_key' => 'nullable|string|max:255',
            'sms_sender_id' => 'nullable|string|max:20',
        ]);

        Setting::set('store_name', $request->store_name);
        Setting::set('store_address', $request->store_address);
        Setting::set('store_phone', $request->store_phone);
        Setting::set('store_email', $request->store_email);
        Setting::set('sms_gateway_api_key', $request->sms_gateway_api_key);
        Setting::set('sms_sender_id', $request->sms_sender_id);

        return redirect()->route('settings.index')->with('success', 'Store configurations updated successfully!');
    }

    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Account profile updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
}
