<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Dashboard';
        $breadcrumbs = ['Dashboard'];

        $year = '';
        $month = '';
        $dailyhealth = [];
        $dailynothealth = [];
        $dates = [];
        $temps = [];
        $users = [];
        $userid = [];
        $gmpok = [];
        $gmpnok = [];
        $totalDaily = [];
        $departments = DB::connection('kmi_server')->table('mdepartemens')->get();

        if ($request->month || $request->from && $request->to) {
            $year = explode('-', $request->month)[0];
            $month = explode('-', $request->month)[1];
            $startDate = Carbon::create($request->from);
            $endDate = Carbon::create($request->to);
            $startMonth = Carbon::parse($request->month)->startOfMonth();
            $endMonth = Carbon::parse($request->month)->endOfMonth();
        } else {
            $year = Carbon::now('Asia/Jakarta')->format('Y');
            $month = Carbon::now('Asia/Jakarta')->format('m');
            $startDate = Carbon::now('Asia/Jakarta')->startOfMonth();
            $endDate = Carbon::now('Asia/Jakarta')->endOfMonth();
            $startMonth = Carbon::now('Asia/Jakarta')->startOfMonth();
            $endMonth = Carbon::now('Asia/Jakarta')->endOfMonth();
        }

        $dateList = [];
        $newdateList = [];

        for ($date = $startMonth; $date->lte($endMonth); $date->addDay()) {
            $dateList[] = $date->format('Y-m-d');
        }


        for ($newdate = $startDate; $newdate->lte($endDate); $newdate->addDay()) {
            $newdateList[] = $newdate->format('Y-m-d');
        }

        foreach ($dateList as $period) {
            $totalDaily[] = Log::whereDate('waktu', $period)->groupBy('user_id')->count();
            $dates[] = $period;
        }

        foreach ($newdateList as $tgl) {
            $dailyhealth[] = Log::where('status', "Healthy")->whereDate('waktu', $tgl)->count();
            $dailynothealth[] = Log::where('status', "Not Healthy")->whereDate('waktu', $tgl)->count();
            $gmpok[] = Log::where(['moustache' => 0, 'beard' => 0])->whereDate('waktu', $tgl)->count();
            $gmpnok[] = Log::where(function ($query) {
                $query->where('moustache', 'like', '%' . 1 . '%')
                    ->orWhere('beard', 'like', '%' . 1 . '%');
            })->whereDate('waktu', $tgl)->count();
        }


        // return $dateList;
        $counthealth = Log::where('status', "Healthy")->whereYear('waktu', $year)->whereMonth('waktu', $month)->count();
        $countnothealth = Log::where('status', "Not Healthy")->whereYear('waktu', $year)->whereMonth('waktu', $month)->count();

        $userid = DB::connection('kmi_server')->table('musers')->pluck('intiduser');

        foreach ($userid as $id) {
            $temps[] = Log::where('user_id', $id)->whereYear('waktu', $year)->whereMonth('waktu', $month)->latest()->first()->suhu ?? 0;
            $users[] = '-';
        }

        return view('dashboard.index', compact('title', 'breadcrumbs', 'counthealth', 'countnothealth', 'dailyhealth', 'dailynothealth', 'dates', 'temps', 'users', 'gmpok', 'gmpnok', 'newdateList', 'totalDaily', 'departments'));
    }
}
