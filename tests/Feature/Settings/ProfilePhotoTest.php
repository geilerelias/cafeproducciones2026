<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('profile photo can be uploaded', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post('/settings/profile/photo', [
            'photo' => UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg'),
        ]);

    $response->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->profile_photo_path)->not->toBeNull();
    expect($user->profile_photo_url)->not->toBeNull();
    Storage::disk('public')->assertExists($user->profile_photo_path);
});

test('profile photo can be deleted', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $path = 'profile-photos/test-avatar.jpg';
    Storage::disk('public')->put($path, 'fake-image-content');
    $user->forceFill(['profile_photo_path' => $path])->save();

    $response = $this
        ->actingAs($user)
        ->delete('/settings/profile/photo');

    $response->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->profile_photo_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('profile photo upload requires an image', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('profile.edit'))
        ->post('/settings/profile/photo', [
            'photo' => UploadedFile::fake()->create('document.pdf', 100),
        ]);

    $response->assertSessionHasErrors('photo');
});
