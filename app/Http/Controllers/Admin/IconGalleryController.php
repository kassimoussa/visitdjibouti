<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class IconGalleryController extends Controller
{
    /**
     * Afficher la galerie d'icônes dans une nouvelle fenêtre
     */
    public function index()
    {
        return view('admin.icons.gallery');
    }
}
