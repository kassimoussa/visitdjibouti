<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Media;

class MediaController extends Controller
{
    public function index()
    {
        return view('operator.media.index');
    }

    public function create()
    {
        return view('operator.media.create');
    }

    public function edit($id)
    {
        $media = Media::findOrFail($id);

        return view('operator.media.edit', compact('media'));
    }
}
