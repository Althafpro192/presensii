<?php

namespace App\Http\Controllers;

use App\Models\RfidDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RfidDeviceController extends Controller
{
    public function index()
    {
        // Untuk sementara kita asumsikan school_id = 1 (nanti akan dinamis)
        $devices = RfidDevice::where('school_id', 1)->get();
        return inertia('Admin/RfidDevices/Index', ['devices' => $devices]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_name' => 'required|string|max:255',
            'ip_address' => 'nullable|ip',
        ]);

        RfidDevice::create([
            'school_id' => 1, // sementara
            'device_name' => $request->device_name,
            'api_key' => Str::random(32),
            'ip_address' => $request->ip_address,
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'Perangkat berhasil ditambahkan');
    }

    public function destroy(RfidDevice $rfidDevice)
    {
        $rfidDevice->delete();
        return redirect()->back()->with('success', 'Perangkat dihapus');
    }
}