<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        return redirect('/admin/profile');
    }

    public function update(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        Auth::user()->update($request->only('name'));

        return redirect()->back()->with('status', 'profile-updated');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', ['password' => 'required|current_password']);

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        return redirect('/');
    }
}
