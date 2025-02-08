<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::role('admin')->get();
        return response()->json($admins);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            // 'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'permissions' => 'array'
        ]);

        $admin = User::create([
            'username' => $request->username,
            // 'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $admin->assignRole('admin');

        if ($request->permissions) {
            $admin->syncPermissions($request->permissions);
        }

        return response()->json(['message' => 'Admin created successfully', 'admin' => $admin], 201);
    }

    public function show(User $admin)
    {
        $admin->load('permissions');
        return response()->json($admin);
    }

    public function update(Request $request, User $admin)
    {
        $request->validate([
            'username' => 'string|max:255',
            // 'email' => 'email|unique:users,email,' . $admin->id,
            'permissions' => 'array'
        ]);

        $admin->update($request->only('username'));

        if ($request->permissions) {
            $admin->syncPermissions($request->permissions);
        }

        return response()->json(['message' => 'Admin updated successfully', 'admin' => $admin]);
    }

    public function destroy(User $admin)
    {
        $admin->delete();
        return response()->json(['message' => 'Admin deleted successfully']);
    }

    public function getPermissions()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }
}
