<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Karyawan;
use App\Models\Log;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $now = Carbon::now('Asia/Jakarta')->format('YmdHis');

            $foto = $request->file('foto');
            $fotoUrl = $foto->storeAs('logs', $now . '-' . rand(1000, 9999) . '.' . $foto->extension());

            $device = Device::where('iddev', $request->id_device)->first();

            $user = Karyawan::where('employee_id', $request->employeeid)->first();

            $setting = Setting::find(1);

            if ($request->beard == 0 && $request->moustache == 0 && floatval($request->suhu) < floatval($setting->val)) {
                $status = "Healthy";
            } else {
                $status = "Not Healthy";
            }

            Log::create([
                'user_id' => $user->user_id,
                'device_id' => $device->id,
                'moustache' => $request->moustache,
                'beard' => $request->beard,
                'suhu' => $request->suhu,
                'waktu' => $request->waktu,
                'foto' => $fotoUrl,
                'status' => $status
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Log berhasil disimpan"
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
