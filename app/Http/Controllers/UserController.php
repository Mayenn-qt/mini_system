<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('branch')->where('role', 'staff')->get();
        $branches = Branch::all();
        return view('users.index', compact('users', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|in:active,inactive',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
            'branch_id' => $request->branch_id,
            'status' => $request->status,
        ]);

        return redirect()->route('users.index')->with('success', 'Staff account created successfully!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'branch_id' => $request->branch_id,
            'status' => $request->status,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Staff account updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'owner') {
            return redirect()->route('users.index')->with('error', 'Cannot delete owner account.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Staff account deleted successfully!');
    }
}
