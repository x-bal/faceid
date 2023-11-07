<?php

namespace App\Http\Controllers;

use App\Exports\LogExport;
use App\Models\Log;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class LogController extends Controller
{
    public function index()
    {
        $title = 'Data Karyawan';
        $breadcrumbs = ['Master', 'Data Karyawan'];
        $setting = Setting::where('name', 'suhu')->first();
        $departments = DB::connection('kmi_server')->table('mdepartemens')->get();

        return view('logs.index', compact('title', 'breadcrumbs', 'setting', 'departments'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {

            $data = [];
            $setting = Setting::find(1);

            if ($request->from || $request->to || $request->department) {
                $to = Carbon::parse($request->to)->addDay(1)->format('Y-m-d');
                $department = $request->department;
                $logs = Log::whereBetween('waktu', [$request->from, $to])->orderBy('waktu', 'DESC')->get();

                foreach ($logs as $log) {
                    $user = DB::connection('kmi_server')->table('musers')
                        ->join('mdepartemens', 'musers.intiddepartemen', '=', 'mdepartemens.intiddepartemen')
                        ->where('intiduser', $log->user_id)
                        ->when($request->has('department') && $department != 'all', function ($query) use ($department) {
                            $query->where('musers.intiddepartemen', $department);
                        })
                        ->first();
                    if ($user) {
                        if ($log->beard == 0 && $log->moustache == 0 && floatval($log->suhu) < floatval($setting->val)) {
                            $status = "Healthy";
                        } else {
                            $status = "Not Healthy";
                        }

                        $data[] = [
                            'id' => $log->id,
                            'txtName' => $user->txtnamauser,
                            'beard' => $log->beard,
                            'moustache' => $log->moustache,
                            'suhu' => $log->suhu,
                            'foto' => $log->foto,
                            'status' => $status,
                            'waktu' => $log->waktu,
                            'department' => $user->txtnamadepartemen,
                            'txtNik' => $user->txtnik,
                        ];
                    }
                }
            } else {
                $now = Carbon::now('Asia/Jakarta')->format('Y-m-d');
                $logs = Log::whereDate('waktu', $now)->get();

                foreach ($logs as $log) {
                    $user = DB::connection('kmi_server')->table('musers')
                        ->join('mdepartemens', 'musers.intiddepartemen', '=', 'mdepartemens.intiddepartemen')
                        ->where('intiduser', $log->user_id)
                        ->first();
                    if ($user) {
                        if ($log->beard == 0 && $log->moustache == 0 && floatval($log->suhu) < floatval($setting->val)) {
                            $status = "Healthy";
                        } else {
                            $status = "Not Healthy";
                        }

                        $data[] = [
                            'id' => $log->id,
                            'txtName' => $user->txtnamauser,
                            'beard' => $log->beard,
                            'moustache' => $log->moustache,
                            'suhu' => $log->suhu,
                            'foto' => $log->foto,
                            'status' => $status,
                            'waktu' => $log->waktu,
                            'department' => $user->txtnamadepartemen,
                            'txtNik' => $user->txtnik,
                        ];
                    }
                }
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-dialog" id="' . $row['id'] . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('karyawan.update', $row['id']) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('karyawan.destroy', $row['id']) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';

                    return $actionBtn;
                })
                ->editColumn('txtName', function ($row) {
                    return '<span class="text-dark">' . $row['txtName'] . '</span>';
                })
                ->editColumn('beard', function ($row) {
                    if ($row['beard'] == 1) {
                        $beard = 'Yes';
                    } else {
                        $beard = 'No';
                    }

                    if ($row['beard'] == 1) {
                        return '<span class="badge bg-danger">' . $beard . '</span>';
                    } else {
                        return '<span class="badge bg-success">' . $beard . '</span>';
                    }
                })
                ->editColumn('moustache', function ($row) {

                    if ($row['moustache'] == 1) {
                        $moustache = 'Yes';
                    } else {
                        $moustache = 'No';
                    }

                    if ($row['moustache'] == 1) {
                        return '<span class="badge bg-danger">' . $moustache . '</span>';
                    } else {
                        return '<span class="badge bg-success">' . $moustache . '</span>';
                    }
                })
                ->editColumn('suhu', function ($row) {
                    $setting = Setting::where('name', 'suhu')->first();
                    if ($row['suhu'] > $setting->val) {
                        return '<span class="badge bg-danger">' . $row['suhu'] . '</span>';
                    } else {
                        return '<span class="badge bg-success">' . $row['suhu'] . '</span>';
                    }
                })
                ->editColumn('foto', function ($row) {
                    return '<a href="#modal-dialog" id="' . $row['id'] . '" class="btn-action" data-route="' . route('logs.show', $row['id']) . '" data-bs-toggle="modal">
                    <div class="menu-profile-image">
                        <img src="' . asset('/storage/' . $row['foto']) . '" alt="User Photo" width="50">
                    </div>
                </a>';
                })
                ->editColumn('dtmCreated', function ($row) {
                    return '<span class="text-dark">' . Carbon::parse($row['waktu'])->format('d/m/Y H:i:s') . '</span>';
                })
                ->editColumn('status', function ($row) {
                    $setting = Setting::find(1);

                    if ($row["beard"] == 0 && $row["moustache"] == 0 && floatval($row["suhu"]) < floatval($setting->val)) {
                        $status = "Healthy";
                    } else {
                        $status = "Not Healthy";
                    }

                    if ($status == "Healthy") {
                        $status = '<span class="badge bg-success">' . $status . '</span>';
                    } else {
                        $status = '<span class="badge bg-danger">' . $status . '</span>';
                    }

                    return $status;
                })
                ->rawColumns(['action', 'foto', 'txtName', 'beard', 'moustache', 'suhu', 'dtmCreated', 'status'])
                ->make(true);
        }
    }

    public function show(Log $log)
    {
        return response()->json([
            'log' => $log
        ]);
    }

    public function update(Request $request, Log $log)
    {
        $beard = $request->beard;
        $moustache = $request->moustache;
        $setting = Setting::where('name', 'suhu')->first();

        if ($beard == 0 && $moustache == 0 && $log->suhu < $setting->val) {
            $status = "Healthy";
        } else {
            $status = "Not Healthy";
        }

        try {
            DB::beginTransaction();

            $log->update([
                'beard' => $beard,
                'moustache' => $moustache,
                'status' => $status,
            ]);

            DB::commit();

            return back()->with('success', "Log successfully updated");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function export(Request $request)
    {
        $data = [];
        $setting = Setting::find(1);

        if ($request->from != null && $request->to != null || $request->department) {
            $to = Carbon::parse($request->to)->addDay(1)->format('Y-m-d');
            $department = $request->department;

            $logs = Log::whereBetween('waktu', [$request->from, $to])->get();


            foreach ($logs as $log) {
                $user = DB::connection('kmi_server')->table('musers')
                    ->join('mdepartemens', 'musers.intiddepartemen', '=', 'mdepartemens.intiddepartemen')
                    ->where('intiduser', $log->user_id)
                    ->when($request->has('department') && $department != 'all', function ($query) use ($department) {
                        $query->where('musers.intiddepartemen', $department);
                    })
                    ->first();
                if ($user) {
                    $kondisi = '';
                    $gmp = '';
                    $moustache = '';
                    $beard = '';

                    if ($log->moustache == 0) {
                        $moustache = 'OK';
                    } else {
                        $moustache = 'NOK';
                    }

                    if ($log->beard == 0) {
                        $beard = 'OK';
                    } else {
                        $beard = 'NOK';
                    }

                    if ($log->suhu <= $setting->val) {
                        $kondisi = 'OK';
                    } else {
                        $kondisi = 'NOK';
                    }

                    if ($log->moustache == 0 && $log->beard == 0) {
                        $gmp = 'OK';
                    } else {
                        $gmp = 'NOK';
                    }

                    $data[] = [Carbon::parse($log->waktu)->format('d-F-y'), Carbon::parse($log->waktu)->format('H.i'), $user->txtnik, $user->txtnamauser, $user->txtnamadepartemen, $moustache, $beard, $log->suhu, 'Ya', $kondisi, $gmp];
                }
            }
        } else {
            $now = Carbon::now('Asia/Jakarta')->format('Y-m-d');
            $logs = Log::whereDate('waktu', $now)->get();

            foreach ($logs as $log) {
                $user = DB::connection('kmi_server')->table('musers')
                    ->join('mdepartemens', 'musers.intiddepartemen', '=', 'mdepartemens.intiddepartemen')
                    ->where('intiduser', $log->user_id)
                    ->first();
                if ($user) {
                    $kondisi = '';
                    $gmp = '';
                    $moustache = '';
                    $beard = '';

                    if ($log->moustache == 0) {
                        $moustache = 'OK';
                    } else {
                        $moustache = 'NOK';
                    }

                    if ($log->beard == 0) {
                        $beard = 'OK';
                    } else {
                        $beard = 'NOK';
                    }

                    if (floatval($log->suhu) <= floatval($setting->val)) {
                        $kondisi = 'OK';
                    } else {
                        $kondisi = 'NOK';
                    }

                    if ($log->moustache == 0 && $log->beard == 0) {
                        $gmp = 'OK';
                    } else {
                        $gmp = 'NOK';
                    }

                    $data[] = [Carbon::parse($log->waktu)->format('d-F-y'), Carbon::parse($log->waktu)->format('H:i'), $user->txtnik, $user->txtnamauser, $user->txtnamadepartemen, $moustache, $beard, $log->suhu, 'Ya', $kondisi, $gmp];
                }
            }
        }

        return Excel::download(new LogExport($data), 'logs_export.xlsx');
    }
}
