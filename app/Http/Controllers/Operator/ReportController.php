<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function export(Request $request)
    {
        // Logic to export reports
        return response()->json(['message' => 'Exporting reports']);
    }
}
