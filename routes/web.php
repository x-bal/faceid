<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest')->name('home');

Route::post('/login', LoginController::class)->name('login');

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('users/get', [UserController::class, 'get'])->name('users.list');
    Route::resource('users', UserController::class);

    Route::get('/devices/get', [DeviceController::class, 'get'])->name('devices.get');
    Route::resource('devices', DeviceController::class);

    Route::get('karyawan/get', [KaryawanController::class, 'get'])->name('karyawan.get');
    Route::post('/karyawan/add-persons', [KaryawanController::class, 'addPersons'])->name('karyawan.addperson');
    Route::post('/karyawan/update-persons/{id}', [KaryawanController::class, 'updatePerson'])->name('karyawan.updatePerson');
    Route::post('/karyawan/delete-persons/{id}', [KaryawanController::class, 'deletePerson'])->name('karyawan.deletePerson');
    Route::resource('karyawan', KaryawanController::class);

    Route::get('/logs/get', [LogController::class, 'get'])->name('logs.get');
    Route::get('logs/export', [LogController::class, 'export'])->name('logs.export');
    Route::resource('logs', LogController::class);

    Route::resource('setting', SettingController::class);
});
