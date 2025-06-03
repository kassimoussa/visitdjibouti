<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
