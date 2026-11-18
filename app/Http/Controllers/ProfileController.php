<?php

namespace App\Http\Controllers;

use App\Support\BdPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('doctor.profile', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $this->blankToNull($request, ['email', 'phone', 'specialty', 'qualifications', 'bmdc_reg', 'clinic_address', 'clinic_phone', 'visiting_hours']);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:190', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'clinic_name' => ['required', 'string', 'max:190'],
            'specialty' => ['nullable', 'string', 'max:190'],
            'qualifications' => ['nullable', 'string'],
            'bmdc_reg' => ['nullable', 'string', 'max:100'],
            'clinic_address' => ['nullable', 'string', 'max:255'],
            'clinic_phone' => ['nullable', 'string', 'max:20'],
            'visiting_hours' => ['nullable', 'string', 'max:255'],
            'appointment_slug' => ['required', 'string', 'max:80', 'alpha_dash', 'unique:users,appointment_slug,'.$user->id],
        ]);

        if (! empty($data['phone'])) {
            $phone = BdPhone::normalize($data['phone']);
            if (! $phone) {
                throw ValidationException::withMessages(['phone' => 'Invalid phone number.']);
            }
            $data['phone'] = $phone;
        }

        if (! empty($data['clinic_phone'])) {
            $clinicPhone = BdPhone::normalize($data['clinic_phone']);
            if (! $clinicPhone) {
                throw ValidationException::withMessages(['clinic_phone' => 'Invalid clinic phone number.']);
            }
            $data['clinic_phone'] = $clinicPhone;
        }

        if (empty($data['appointment_slug'])) {
            $data['appointment_slug'] = Str::slug($user->name).'-'.Str::lower(Str::random(4));
        }

        $user->update($data);

        return back()->with('status', 'Profile saved. Prescription headers and appointment link updated.');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $user = $request->user();
        $previous = $user->avatar_path;
        $path = $request->file('avatar')->store('avatars', 'public');

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'avatar' => 'Could not store the doctor photo. Please try again.',
            ]);
        }

        $user->update(['avatar_path' => $path]);

        if ($previous && $previous !== $path) {
            Storage::disk('public')->delete($previous);
        }

        return back()->with('status', 'Doctor photo updated.');
    }

    public function destroyAvatar(Request $request)
    {
        $user = $request->user();
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        return back()->with('status', 'Doctor photo removed.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $user = $request->user();
        $previous = $user->logo_path;
        $path = $request->file('logo')->store('logos', 'public');

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'logo' => 'Could not store the clinic logo. Please try again.',
            ]);
        }

        $user->update(['logo_path' => $path]);

        if ($previous && $previous !== $path) {
            Storage::disk('public')->delete($previous);
        }

        return back()->with('status', 'Clinic logo updated on your prescription pad.');
    }

    public function destroyLogo(Request $request)
    {
        $user = $request->user();
        if ($user->logo_path) {
            Storage::disk('public')->delete($user->logo_path);
            $user->update(['logo_path' => null]);
        }

        return back()->with('status', 'Clinic logo removed. Default pad logo will be used.');
    }

    private function blankToNull(Request $request, array $keys): void
    {
        $merge = [];
        foreach ($keys as $key) {
            if ($request->exists($key) && trim((string) $request->input($key)) === '') {
                $merge[$key] = null;
            }
        }
        if ($merge) {
            $request->merge($merge);
        }
    }
}
