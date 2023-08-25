<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class KaryawanController extends Controller
{
    public function index()
    {
        $title = 'Data Karyawan';
        $breadcrumbs = ['Master', 'Data Karyawan'];
        $devices = Device::get();
        $karyawanId = Karyawan::pluck('user_id');
        $karyawan = DB::connection('kmi_server')->table('musers')->whereNotIn('intiduser', $karyawanId)->get();

        return view('karyawan.index', compact('title', 'breadcrumbs', 'devices', 'karyawan'));
    }

    function get(Request $request)
    {
        if ($request->ajax()) {
            $userid = Karyawan::pluck('user_id');
            $users = DB::connection('kmi_server')->table('musers')->whereIn('intiduser', $userid)->get();
            $karyawan = Karyawan::get();

            $data = [];

            foreach ($users as $key => $user) {
                $data[] = [
                    'txtName' => $user->txtnamauser,
                    'foto' => Karyawan::where('user_id', $user->intiduser)->first()->foto,
                    'id' => Karyawan::where('user_id', $user->intiduser)->first()->id,
                    'is_export' => Karyawan::where('user_id', $user->intiduser)->first()->is_export,
                    'is_edit' => Karyawan::where('user_id', $user->intiduser)->first()->is_edit,
                ];
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('checkbox', function ($row) {
                    $checkbox = '';
                    if ($row['is_export'] == 0) {
                        $checkbox .= '<input type="checkbox" name="" id="' . $row['id'] . '" class="check-karyawan">';
                    }
                    return $checkbox;
                })
                ->editColumn('action', function ($row) {
                    $actionBtn = '<div class="btn-group my-n1">
                        <button type="button" disabled class="btn btn-secondary btn-sm">Action</button>
                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                        <a href="' . route('karyawan.edit', $row['id']) . '" class="dropdown-item ">Edit</a>
                        <a data-route="' . route('karyawan.destroy', $row['id']) . '" class="dropdown-item btn-delete">Delete</a>';
                    if ($row['is_edit'] == 1) {
                        $actionBtn .= '<a href="' . route('karyawan.updatePerson', $row['id']) . '"  class="dropdown-item btn-update">Update To Device</a> ';
                    }
                    if ($row['is_export'] == 1) {
                        $actionBtn .= '<a href="' . route('karyawan.deletePerson', $row['id']) . '"  class="dropdown-item btn-del">Delete From Device</a> ';
                    }

                    $actionBtn .= '</div>
                    </div>';
                    return $actionBtn;
                })
                ->editColumn('foto', function ($row) {
                    return '<div class="menu-profile-image">
                    <img src="' . asset('/storage/' . $row['foto']) . '" alt="User Photo" width="50">
                </div>';
                })
                // ->editColumn('created_at', function ($row) {
                //     return Carbon::parse($row->created_at)->format('d/m/Y H:i:s');
                // })
                ->rawColumns(['action', 'foto', 'checkbox'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan' => 'required|numeric',
            'foto' => 'required|mimes:jpg,jpeg,png'
        ]);

        try {
            DB::beginTransaction();

            $karyawan = DB::connection('kmi_server')->table('musers')->where('intiduser', $request->karyawan)->first();

            $foto = $request->file('foto');
            $fotoUrl = $foto->storeAs('karyawan', $karyawan->txtnik . '.' . $foto->extension());

            $karyawan = Karyawan::create([
                'user_id' => $request->karyawan,
                'foto' => $fotoUrl,
                'employee_id' => 23 . rand(100, 999)
            ]);

            DB::commit();

            return back()->with('success', "Karyawan berhasil ditambahkan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
        }
    }

    public function show(Karyawan $karyawan)
    {
        return response()->json([
            'karyawan' => $karyawan
        ]);
    }

    function edit(Karyawan $karyawan)
    {
        $title = 'Data Karyawan';
        $breadcrumbs = ['Master', 'Data Karyawan'];
        $devices = Device::get();

        return view('karyawan.form', compact('title', 'breadcrumbs', 'devices', 'karyawan', 'devices'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'foto' => 'required|mimes:jpg,jpeg,png'
        ]);

        try {
            DB::beginTransaction();

            $user = DB::connection('kmi_server')->table('musers')->where('intiduser', $karyawan->user_id)->first();

            Storage::delete($karyawan->foto);
            $foto = $request->file('foto');
            $fotoUrl = $foto->storeAs('karyawan', $user->txtnik . '.' . $foto->extension());

            $karyawan->update([
                'foto' => $fotoUrl,
                'is_edit' => 1
            ]);

            DB::commit();

            return redirect()->route('karyawan.index')->with('success', "Foto karyawan berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Karyawan $karyawan)
    {
        try {
            DB::beginTransaction();

            if ($karyawan->is_export == 0) {
                Storage::delete($karyawan->foto);
                $karyawan->delete();
            } else {
                return back()->with('error', "Please delete it from the device first");
            }

            DB::commit();

            return back()->with('success', "Foto karyawan berhasil dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function addPersons(Request $request)
    {
        try {
            DB::beginTransaction();

            if (!$request->device) {
                return back()->with('error', "Please select device");
            }

            $device = Device::find($request->device);
            $deviceTarget = $device->iddev;
            $ipDevice = $device->ipaddress;

            $personInfo = [];

            $karyawan = Karyawan::whereIn('id', $request->idkary)->get();

            $newArray = [
                "operator" => "AddPersons",
                "DeviceID" => $deviceTarget,
                "Total" => count($karyawan),
            ];

            foreach ($karyawan as $i => $person) {
                $member = DB::connection('kmi_server')->table('musers')->where('intiduser', $person->user_id)->first();

                $gambar = file_get_contents(storage_path('/app/public/' . $person->foto));
                $gambar_format = base64_encode($gambar);
                $id = $person->employee_id;

                $personInfo = [
                    "Name" => $member->txtnamauser,
                    "CustomizeID" => intval($id),
                    "PersonUUID" => $id,
                    "picinfo" => $gambar_format
                ];
                $newArray["Personinfo_$i"] = $personInfo;
            }

            // Encode newArray to json format
            $json = json_encode($newArray, JSON_UNESCAPED_SLASHES);

            $headers = array(
                "Authorization: Basic " . base64_encode("admin:admin"),
                'Content-Type: application/x-www-form-urlencoded'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ipDevice . '/action/AddPersons');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = json_decode(curl_exec($ch), true);
            curl_close($ch);

            if ($result && $result['code'] == 200) {
                foreach ($karyawan as $p) {
                    $p->update(['is_export' => 1, 'is_edit' => 0]);
                }

                DB::commit();

                return back()->with('success', "Foto karyawan berhasil diexport");
            } else {
                if ($result) {
                    return back()->with('error', $result);
                } else {
                    return back()->with('error', "Device not connected");
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function updatePerson(Request $request, $id)
    {
        if (!$request->device) {
            return back()->with('error', "Select Device");
        }

        $device = Device::find($request->device);
        $deviceTarget = $device->iddev;
        $ipDevice = $device->ipaddress;

        $karyawan = Karyawan::find($id);
        $member = DB::connection('kmi_server')->table('musers')->where('intiduser', $karyawan->user_id)->first();

        $gambar = file_get_contents(storage_path('/app/public/' . $karyawan->foto));
        $gambar_format = base64_encode($gambar);

        $data = array(
            "operator" => "EditPerson",
            "info" => array(
                "DeviceID" => $deviceTarget,
                "IdType" => 0,
                "CustomizeID" => $karyawan->employee_id,
                "PersonUUID" => $karyawan->employee_id,
            ),
            "picinfo" => $gambar_format
        );

        // Encode newArray to json format
        $json = json_encode($data, JSON_UNESCAPED_SLASHES);

        $headers = array(
            "Authorization: Basic " . base64_encode("admin:admin"),
            'Content-Type: application/x-www-form-urlencoded'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ipDevice . '/action/EditPerson');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if ($result['code'] == 200) {
            $karyawan->update(['is_export' => 1, 'is_edit' => 0]);

            return back()->with('success', "Foto karyawan berhasil diupdate");
        } else {
            return back()->with('error', $result);
        }
    }

    public function deletePerson(Request $request, $id)
    {
        if (!$request->device) {
            return back()->with('error', "Select Device");
        }

        $device = Device::find($request->device);
        $deviceTarget = $device->iddev;
        $ipDevice = $device->ipaddress;

        $karyawan = Karyawan::find($id);
        $member = DB::connection('kmi_server')->table('musers')->where('intiduser', $karyawan->user_id)->first();

        $gambar = file_get_contents(storage_path('/app/public/' . $karyawan->foto));
        $gambar_format = base64_encode($gambar);

        $data = array(
            "operator" => "DeletePerson",
            "info" => array(
                "DeviceID" => $deviceTarget,
                "IdType" => 0,
                "CustomizeID" => $karyawan->employee_id,
                "PersonUUID" => $karyawan->employee_id,
            ),
        );

        // Encode newArray to json format
        $json = json_encode($data, JSON_UNESCAPED_SLASHES);

        $headers = array(
            "Authorization: Basic " . base64_encode("admin:admin"),
            'Content-Type: application/x-www-form-urlencoded'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ipDevice . '/action/DeletePerson');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if ($result['code'] == 200) {
            $karyawan->update(['is_export' => 0, 'is_edit' => 0]);


            return back()->with('success', "Foto karyawan berhasil diupdate");
        } else {
            return back()->with('error', $result);
        }
    }
}
