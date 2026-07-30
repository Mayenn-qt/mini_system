<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $subscription = $request->get('subscription');

        $query = Customer::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($subscription !== null && $subscription !== '') {
            $query->where('subscribed', $subscription === 'subscribed');
        }

        $customers = $query->latest()->get();

        return view('customers.index', compact('customers', 'search', 'subscription'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'subscribed' => 'required|boolean',
        ]);

        Customer::create($request->only('name', 'phone', 'subscribed'));

        return redirect()->route('customers.index')->with('success', 'Customer added successfully!');
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'subscribed' => 'required|boolean',
        ]);

        $customer->update($request->only('name', 'phone', 'subscribed'));

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully!');
    }
}
