<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role') && in_array($request->role, ['admin', 'user'])) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->get();
        return view('backoffice.users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,user'
        ]);

        // Prevent admin from demoting themselves
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return redirect()->route('backoffice.users.index')->with('error', 'You cannot change your own admin role.');
        }

        $user->update([
            'role' => $request->role
        ]);

        return redirect()->route('backoffice.users.index')->with('success', 'User role updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('backoffice.users.index')->with('error', 'You cannot delete yourself.');
        }

        $user->delete();
        return redirect()->route('backoffice.users.index')->with('success', 'User deleted successfully.');
    }
}
