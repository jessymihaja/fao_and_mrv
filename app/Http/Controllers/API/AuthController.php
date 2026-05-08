<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'email' => 'required|email|unique:utilisateurs,email',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'role' => $request->role,
            'motDePasse' => Hash::make($request->password),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->motDePasse)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnecté',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function getUsersPaginated(Request $request)
{
    $perPage = $request->per_page ?? 15;

    $users = User::paginate($perPage);

    $users->getCollection()->transform(function ($user) {

        return [
            'id' => $user->id_utilisateur ?? $user->id,

            // IMPORTANT : le front attend probablement "name"
            'name' => trim(($user->prenom ?? '') . ' ' . ($user->nom ?? '')),

            'email' => $user->email,

            'role' => $user->role ?? null,

            'is_active' => $user->status ?? 'true',

            'created_at' => $user->created_at,
        ];
    });

    return response()->json($users);
}
}
