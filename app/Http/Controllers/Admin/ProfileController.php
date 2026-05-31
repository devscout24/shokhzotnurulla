<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user      = auth()->user();
        $google2fa = app('pragmarx.google2fa');

        $QR_Image = null;
        $secret   = null;

        if (! $user->google2fa_secret) {
            // Generate a new secret and store in session temporarily
            if (! session('google2fa_secret_setup')) {
                session(['google2fa_secret_setup' => $google2fa->generateSecretKey()]);
            }
            $secret = session('google2fa_secret_setup');

            // Generate the QR code URL
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );

            // Generate inline QR Code image using BaconQrCode
            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $writer   = new \BaconQrCode\Writer($renderer);
            $QR_Image = base64_encode($writer->writeString($qrCodeUrl));
        }

        return view('admin.pages.profile', compact('user', 'QR_Image', 'secret'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'password'   => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name  = $validated['last_name'];
        $user->email      = $validated['email'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
