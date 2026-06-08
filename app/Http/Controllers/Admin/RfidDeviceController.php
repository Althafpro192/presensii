<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\RfidDevice;
use App\Models\RfidWriteJob;
use App\Models\Student;
use App\Jobs\ProcessRfidWriteJob;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RfidDeviceController extends Controller
{
    public function index()
    {
        $devices = RfidDevice::orderBy('device_name')->paginate(15);

        return Inertia::render('Admin/RfidDevices/Index', [
            'devices' => $devices,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/RfidDevices/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'ip_address' => 'nullable|ip',
        ]);

        $validated['school_id'] = $request->user()->school_id;

        RfidDevice::create($validated);

        return redirect()->route('rfid-devices.index')
            ->with('success', 'Perangkat RFID berhasil ditambahkan.');
    }

    public function edit(RfidDevice $rfidDevice)
    {
        return Inertia::render('Admin/RfidDevices/Edit', [
            'device' => $rfidDevice,
        ]);
    }

    public function update(Request $request, RfidDevice $rfidDevice)
    {
        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'ip_address' => 'nullable|ip',
            'status' => 'required|in:active,inactive',
        ]);

        $rfidDevice->update($validated);

        return redirect()->route('rfid-devices.index')
            ->with('success', 'Perangkat berhasil diperbarui.');
    }

    public function destroy(RfidDevice $rfidDevice)
    {
        $rfidDevice->delete();

        return redirect()->route('rfid-devices.index')
            ->with('success', 'Perangkat berhasil dihapus.');
    }
}
