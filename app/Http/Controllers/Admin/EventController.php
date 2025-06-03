<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     */
    public function index()
    {
        return view('admin.events.index');
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit($id)
    {
        return view('admin.events.edit', ['eventId' => $id]);
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        return view('admin.events.show', compact('event'));
    }
}

