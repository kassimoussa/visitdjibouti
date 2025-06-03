<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Affiche la page de profil de l'administrateur.
     */
    public function show()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Met à jour les informations de profil.
     */
    public function updateInfo(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('admin_users')->ignore($admin->id)
            ],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]);

        $admin->update($validated);

        return redirect()->route('profile.show')->with('status', 'Informations de profil mises à jour avec succès.');
    }

    /**
     * Met à jour le mot de passe.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $admin = Auth::guard('admin')->user();
        $admin->password = Hash::make($validated['password']);
        $admin->save();

        return redirect()->route('profile.show')->with('status', 'Mot de passe mis à jour avec succès.');
    }

    /**
     * Met à jour l'avatar.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:1024'], // 1MB max
        ]);

        $admin = Auth::guard('admin')->user();

        // Supprimer l'ancien avatar s'il existe
        if ($admin->avatar && Storage::disk('public')->exists($admin->avatar)) {
            Storage::disk('public')->delete($admin->avatar);
        }

        // Stocker le nouvel avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        $admin->avatar = $path;
        $admin->save();

        return redirect()->route('profile.show')->with('status', 'Avatar mis à jour avec succès.');
    }
}
