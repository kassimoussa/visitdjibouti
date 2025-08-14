<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExternalLink;
use Illuminate\Http\Request;

class ExternalLinkController extends Controller
{
    public function index()
    {
        return view('admin.external-links.index');
    }

    public function create()
    {
        return view('admin.external-links.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'status' => 'boolean',
        ]);

        ExternalLink::create([
            'name' => $request->name,
            'url' => $request->url,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('external-links.index')
                        ->with('success', 'Lien externe créé avec succès.');
    }

    public function show(ExternalLink $externalLink)
    {
        return view('admin.external-links.show', compact('externalLink'));
    }

    public function edit(ExternalLink $externalLink)
    {
        return view('admin.external-links.edit', compact('externalLink'));
    }

    public function update(Request $request, ExternalLink $externalLink)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'status' => 'boolean',
        ]);

        $externalLink->update([
            'name' => $request->name,
            'url' => $request->url,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('external-links.index')
                        ->with('success', 'Lien externe modifié avec succès.');
    }

    public function destroy(ExternalLink $externalLink)
    {
        $externalLink->delete();

        return redirect()->route('external-links.index')
                        ->with('success', 'Lien externe supprimé avec succès.');
    }
}