<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Affiche la page de gestion des catégories contenant le composant Livewire.
     */
    public function index()
    {
        return view('admin.categories.index');
    }
}
