<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:utilisateurs,email',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);

        $user = User::create([
            'nom' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'motDePasse' => Hash::make($request->password),
        ]);


        return response()->json([
            'user' => $user,
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
public function toggle(int $id): JsonResponse
{
    $user = User::whereNotIn('role', ['super_admin'])->findOrFail($id);

    if ($user->id === auth()->id()) {
        return response()->json([
            'message' => 'Impossible de désactiver votre propre compte.'
        ], 403);
    }

    $user->update([
        'status' => $user->status === 'active' ? 'inactive' : 'active'
    ]);

    return response()->json(['data' => $user]);
}
public function updateRole(Request $request, int $id): JsonResponse
{
    $user = User::whereNotIn('role', ['super_admin'])->findOrFail($id);

    $request->validate([
        'role' => ['required', 'in:admin,gestionnaire,utilisateur'],
    ]);

    $user->update(['role' => $request->role]);

    return response()->json(['data' => $user]);
}
public function update(Request $request, int $id): JsonResponse
{
    $user = User::whereNotIn('role', ['super_admin'])->findOrFail($id);

    $validated = $request->validate([
        'name' => ['sometimes', 'required', 'string', 'max:255'],
        'email' => ['sometimes', 'required', 'email', Rule::unique('utilisateurs', 'email')->ignore($id, 'id_utilisateur')],
        'password' => ['nullable', 'confirmed', 'min:6'],
        'role' => ['sometimes', 'required', 'in:admin,gestionnaire,utilisateur'],
    ]);

    $data = [];

    // mapping name -> nom (DB)
    if (isset($validated['name'])) {
        $data['nom'] = $validated['name'];
    }

    // email direct
    if (isset($validated['email'])) {
        $data['email'] = $validated['email'];
    }

    // role direct
    if (isset($validated['role'])) {
        $data['role'] = $validated['role'];
    }

    // password -> motDePasse (DB)
    if (!empty($validated['password'])) {
        $data['motDePasse'] = Hash::make($validated['password']);
    }

    $user->update($data);

    return response()->json([
        'user' => $user
    ]);
}


}
