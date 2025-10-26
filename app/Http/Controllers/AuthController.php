<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // 🔹 Registro de usuario
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        // Crear usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generar token de verificación
        $verificationToken = $user->generateVerificationToken();

        // Enviar email de verificación
        $this->sendVerificationEmail($user, $verificationToken);

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado correctamente. Por favor verifica tu email.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 201);
    }

    // 🔹 Verificar email
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $user = User::where('email_verification_token', $request->token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token de verificación inválido o expirado.'
            ], 400);
        }

        if ($user->verifyEmail($request->token)) {
            return response()->json([
                'success' => true,
                'message' => 'Email verificado correctamente. Ya puedes iniciar sesión.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Error al verificar el email.'
        ], 400);
    }

    // 🔹 Reenviar verificación
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No existe un usuario con este email.'
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Este email ya está verificado.'
            ], 400);
        }

        // Generar nuevo token si no existe
        if (!$user->email_verification_token) {
            $user->generateVerificationToken();
        }

        // Reenviar email
        $this->sendVerificationEmail($user, $user->email_verification_token);

        return response()->json([
            'success' => true,
            'message' => 'Email de verificación reenviado correctamente.'
        ]);
    }

    // 🔹 Enviar email de verificación
    private function sendVerificationEmail($user, $token)
    {
        $verificationUrl = url("/api/verify-email?token={$token}");

        Mail::send('emails.verification', [
            'user' => $user,
            'verificationUrl' => $verificationUrl
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verifica tu cuenta - Studia');
        });
    }

    // 🔹 Login (actualizado para requerir verificación)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales son incorrectas.'],
            ]);
        }

        // Verificar si el email está verificado
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor verifica tu email antes de iniciar sesión.',
                'needs_verification' => true
            ], 403);
        }

        // Crear token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user,
        ]);
    }

    // 🔹 Perfil del usuario autenticado
    public function perfil(Request $request)
    {
        return response()->json($request->user());
    }

    // 🔹 Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}
