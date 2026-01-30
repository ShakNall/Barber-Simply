<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $barber = $user->barber ?? null;

        return view('profile.index', compact('user', 'barber'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
        ]);

        // Update user table
        $user->name  = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Jika BARBER → update barber detail
        if ($user->role === 'barber') {
            $request->validate([
                'nickname' => 'nullable|string|max:255',
                'speciality' => 'nullable|string|max:255',
                'price' => 'nullable|numeric|min:0',
            ]);

            $barber = $user->barber;
            if ($barber) {
                $barber->nickname = $request->nickname;
                $barber->speciality = $request->speciality;
                if ($request->price !== null) {
                    $barber->price = $request->price;
                }
                $barber->save();
            }
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
