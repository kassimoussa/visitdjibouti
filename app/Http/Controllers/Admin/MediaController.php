<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;

class MediaController extends Controller
{
    public function index()
    {
        return view('admin.media.index');
    }

    public function create()
    {
        return view('admin.media.create');
    }

    public function edit($id)
    {
        $media = Media::findOrFail($id);

        return view('admin.media.edit', compact('media'));
    }

    public function simpleupload()
    {
        return view('admin.media.simple-upload');
    }
}
