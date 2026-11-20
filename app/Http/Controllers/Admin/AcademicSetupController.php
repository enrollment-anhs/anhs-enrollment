<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicSetupController extends Controller
{
    public function index(Request $request): View
    {
        return view('users.admin.academic-setup', [
            'yearData' => app(AcademicYearConfigurationController::class)->pageData($request),
            'setupTab' => 'academic-year-config',
        ]);
    }
}
