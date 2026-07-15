<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * GET /api/users  (admin)
     * Daftar akun admin & petugas (bukan siswa — siswa dikelola lewat /api/siswa).
     */
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'petugas'])->latest()->paginate(20);

        return UserResource::collection($users)->additional(['success' => true]);
    }

    /**
     * POST /api/users  (admin)
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => "User '{$user->name}' berhasil ditambahkan.",
            'data' => new UserResource($user),
        ], 201);
    }

    /**
     * GET /api/users/{user}  (admin)
     */
    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    /**
     * PUT/PATCH /api/users/{user}  (admin)
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->safe()->only(['name', 'email', 'role']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => "User '{$user->name}' berhasil diperbarui.",
            'data' => new UserResource($user),
        ]);
    }

    /**
     * DELETE /api/users/{user}  (admin)
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun sendiri.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus.',
        ]);
    }
}
