<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourOperator;
use App\Models\Poi;
use App\Models\Event;
use Illuminate\Http\Request;

class TourController extends Controller
{
    /**
     * Display a listing of tours.
     */
    public function index()
    {
        return view('admin.tours.index');
    }

    /**
     * Show the form for creating a new tour.
     */
    public function create()
    {
        $tourOperators = TourOperator::active()->with('translations')->get();
        $pois = Poi::where('status', 'published')->with('translations')->get();
        $events = Event::where('status', 'published')->with('translations')->get();

        return view('admin.tours.create', compact('tourOperators', 'pois', 'events'));
    }

    /**
     * Show the form for editing the specified tour.
     */
    public function edit($id)
    {
        $tour = Tour::with(['translations', 'tourOperator', 'target'])->findOrFail($id);
        $tourOperators = TourOperator::active()->with('translations')->get();
        $pois = Poi::where('status', 'published')->with('translations')->get();
        $events = Event::where('status', 'published')->with('translations')->get();

        return view('admin.tours.edit', compact('tour', 'tourOperators', 'pois', 'events'));
    }

    /**
     * Display the specified tour.
     */
    public function show($id)
    {
        $tour = Tour::with([
            'translations',
            'tourOperator.translations',
            'target.translations',
            'schedules' => function($query) {
                $query->orderBy('start_date');
            }
        ])->findOrFail($id);

        return view('admin.tours.show', compact('tour'));
    }
}