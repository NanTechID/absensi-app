<?php

use App\Http\Controllers\AttendanceReportExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/login', '/admin/login')->name('login');

Route::middleware('auth')
    ->get('/admin/laporan-absensi/export/{format}', AttendanceReportExportController::class)
    ->whereIn('format', ['xlsx', 'pdf'])
    ->name('admin.reports.attendance.export');
