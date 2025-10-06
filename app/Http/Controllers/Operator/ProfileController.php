<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the operator user profile.
     */
    public function show(): View
    {
        $user = Auth::guard('operator')->user();
        $user->load('tourOperator.translations');

        return view('operator.profile.show', compact('user'));
    }

    /**
     * Update the operator user profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tour_operator_users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'language_preference' => 'required|in:fr,en,ar',
        ]);

        $user->update($validated);

        // Update session locale if changed
        if ($user->language_preference !== session('locale')) {
            session(['locale' => $user->language_preference]);
        }

        return redirect()
            ->route('operator.profile.show')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Update the operator user password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        $validated = $request->validate([
            'current_password' => 'required|current_password:operator',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('operator.profile.show')
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }

    /**
     * Update the operator user avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $avatarPath = $request->file('avatar')->store('avatars/operators', 'public');

        $user->update([
            'avatar' => $avatarPath,
        ]);

        return redirect()
            ->route('operator.profile.show')
            ->with('success', 'Avatar mis à jour avec succès.');
    }

    /**
     * Delete the operator user avatar.
     */
    public function deleteAvatar(): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update([
            'avatar' => null,
        ]);

        return redirect()
            ->route('operator.profile.show')
            ->with('success', 'Avatar supprimé avec succès.');
    }

    /**
     * Show the tour operator profile.
     */
    public function showTourOperator(): View
    {
        $user = Auth::guard('operator')->user();
        $tourOperator = $user->tourOperator;
        $tourOperator->load([
            'translations',
            'logo',
            'media',
            'pois.translations'
        ]);

        return view('operator.profile.tour-operator', compact('user', 'tourOperator'));
    }

    /**
     * Update the tour operator profile.
     */
    public function updateTourOperator(Request $request): RedirectResponse
    {
        $user = Auth::guard('operator')->user();
        $tourOperator = $user->tourOperator;

        $validated = $request->validate([
            'phones' => 'nullable|string',
            'emails' => 'nullable|string',
            'website' => 'nullable|url',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'translations' => 'required|array',
            'translations.*' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.address_translated' => 'nullable|string|max:500',
        ]);

        // Update main tour operator data
        $tourOperator->update([
            'phones' => $validated['phones'],
            'emails' => $validated['emails'],
            'website' => $validated['website'],
            'address' => $validated['address'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        // Update translations
        foreach ($validated['translations'] as $locale => $translationData) {
            $tourOperator->translations()->updateOrCreate(
                ['locale' => $locale],
                $translationData
            );
        }

        return redirect()
            ->route('operator.tour-operator.show')
            ->with('success', 'Informations de l\'opérateur touristique mises à jour avec succès.');
    }
}