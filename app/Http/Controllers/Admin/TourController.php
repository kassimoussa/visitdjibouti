<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Poi;
use App\Models\Tour;
use App\Models\TourOperator;

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
            'target.translations' => function ($query) {
                $query->orderBy('start_date');
            },
        ])->findOrFail($id);

        return view('admin.tours.show', compact('tour'));
    }

    /**
     * Remove the specified tour from storage.
     */
    public function destroy(Tour $tour)
    {
        try {
            $tour->delete();

            return redirect()->route('tours.index')
                ->with('success', 'Tour supprimé avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du tour: ' . $e->getMessage());
        }
    }
}
