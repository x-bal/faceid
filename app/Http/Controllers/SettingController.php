<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    function update(Request $request, Setting $setting)
    {
        try {
            DB::beginTransaction();

            $setting->update([
                'val' => $request->limit,
            ]);

            DB::commit();

            return back()->with('success', "Limit suhu berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
