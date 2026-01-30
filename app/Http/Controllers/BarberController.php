<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BarberController extends Controller
{
    public function index()
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        $barbers = Barber::with('user')->latest()->get();
        return view('barbers.index', compact('barbers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6',

            'nickname'   => 'nullable|string',
            'speciality' => 'nullable|string',
            'price'      => 'required|integer|min:0',
            'image'      => 'nullable|image|max:2048',
            'is_active'  => 'boolean',
        ]);

        // ================= USER =================
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'barber',
            'is_active' => true,
        ]);

        // ================= BARBER =================
        $data = [
            'user_id'    => $user->id,
            'nickname'   => $request->nickname,
            'speciality' => $request->speciality,
            'price'      => $request->price,
            'is_active'  => $request->is_active ?? 1,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('barbers', 'public');
        }

        Barber::create($data);

        return redirect()->route('barbers.index')
            ->with('success', 'Barber berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $barber = Barber::with('user')->findOrFail($id);

        $request->validate([
            'name'       => 'required|string',
            'email'      => 'required|email|unique:users,email,' . $barber->user_id,

            'nickname'   => 'nullable|string',
            'speciality' => 'nullable|string',
            'price'      => 'required|integer|min:0',
            'image'      => 'nullable|image|max:2048',
            'is_active'  => 'boolean',
        ]);

        // update user
        $barber->user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        // update barber
        $data = $request->only(['nickname', 'speciality', 'price', 'is_active']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('barbers', 'public');
        }

        $barber->update($data);

        return redirect()->route('barbers.index')
            ->with('success', 'Barber berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barber = Barber::with('user')->findOrFail($id);

        // optional: hapus user juga
        $barber->user->delete();
        $barber->delete();

        return back()->with('success', 'Barber berhasil dihapus.');
    }
}
