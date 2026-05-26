<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function edit()
    {
        return view('profile.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        try {
            $request->user()->update([
                'password' => Hash::make($request->password),
            ]);

            Log::info("Contraseña cambiada para usuario ID: " . $request->user()->id);

            return redirect()->route('profile.edit')->with('success', 'Contraseña actualizada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al cambiar contraseña: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo actualizar la contraseña.');
        }
    }
}
