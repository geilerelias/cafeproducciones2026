<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfilePhotoController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        $request->user()->updateProfilePhoto($request->file('photo'));

        return to_route('profile.edit')->with('status', 'profile-photo-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->user()->deleteProfilePhoto();

        return to_route('profile.edit')->with('status', 'profile-photo-deleted');
    }
}
