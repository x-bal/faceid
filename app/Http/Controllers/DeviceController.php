<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DeviceController extends Controller
{
    public function index()
    {
        $title = 'Data Device';
        $breadcrumbs = ['Data Device'];

        return view('device.index', compact('title', 'breadcrumbs'));
    }

    function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Device::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('devices.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('devices.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';

                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'iddev' => 'required|numeric',
            'nama_device' => 'required|string',
            'ipaddress' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $device = Device::create([
                'iddev' => $request->iddev,
                'name' => $request->nama_device,
                'ipaddress' => $request->ipaddress,
            ]);

            DB::commit();

            return back()->with('success', "Device berhasil diupload");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Device $device)
    {
        return response()->json([
            'device' => $device
        ]);
    }

    public function update(Request $request, Device $device)
    {
        $request->validate([
            'iddev' => 'required|numeric',
            'nama_device' => 'required|string',
            'ipaddress' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $device->update([
                'iddev' => $request->iddev,
                'name' => $request->nama_device,
                'ipaddress' => $request->ipaddress,
            ]);

            DB::commit();

            return back()->with('success', "Device berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Device $device)
    {
        try {
            DB::beginTransaction();

            $device->delete();

            DB::commit();

            return back()->with('success', "Device berhasil dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
