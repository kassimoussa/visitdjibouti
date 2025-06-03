<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poi;
use Illuminate\Http\Request;

class PoiController extends Controller
{
    /**
     * Display a listing of the points of interest.
     */
    public function index()
    {
        return view('admin.pois.index');
    }

    /**
     * Show the form for creating a new point of interest.
     */
    public function create()
    {
        return view('admin.pois.create');
    }

    /**
     * Show the form for editing the specified point of interest.
     */
    public function edit($id)
    {
        return view('admin.pois.edit', ['poiId' => $id]);
    }

    /**
     * Display the specified point of interest.
     */
    public function show(Poi $poi)
    {
        return view('admin.pois.show', compact('poi'));
    }
}
