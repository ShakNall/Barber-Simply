<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('id', 'DESC')->get();
        return view('services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|integer|min:0',
            'duration'    => 'required|integer|min:10',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        Service::create($data);

        return back()->with('success', 'Service berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|integer|min:0',
            'duration'    => 'required|integer|min:10',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048'
        ]);

        $service = Service::findOrFail($id);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        return back()->with('success', 'Service berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Service::findOrFail($id)->delete();

        return back()->with('success', 'Service berhasil dihapus.');
    }
}
