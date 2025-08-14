<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    /**
     * Afficher la page d'exemple du Universal Media Selector
     */
    public function index()
    {
        return view('admin.example.universal-media-selector');
    }

    /**
     * Afficher la page de test simple du Universal Media Selector
     */
    public function test()
    {
        return view('test-selector');
    }

    /**
     * Afficher la page de debug du Universal Media Selector
     */
    public function debug()
    {
        return view('test-selector-debug');
    }

    /**
     * Afficher la page de test simple du Universal Media Selector
     */
    public function simple()
    {
        return view('test-selector-simple');
    }

    /**
     * Afficher la page de test minimal du Universal Media Selector
     */
    public function minimal()
    {
        return view('test-selector-minimal');
    }
}