<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display all registered users.
     */
    public function index(Request $request)
    {
        $query = User::with('role')
            ->orderBy('name');

        // Search berdasarkan nama atau email
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        $users = $query->paginate(15)->withQueryString();

        $roles = Role::orderBy('name')->get();

        $totalUsers = User::count();
        $totalRoles = Role::count();

        $usersToday = User::whereDate('created_at', today())
            ->count();

        return view('administrator.users.index', compact(
            'users',
            'roles',
            'totalUsers',
            'totalRoles',
            'usersToday'
        ));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('administrator.users.create', compact(
            'roles'
        ));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'role_id' => [
                'required',
                'exists:roles,id',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('administrator.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a user.
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();

        return view('administrator.users.edit', compact(
            'user',
            'roles'
        ));
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user)
    {
        $isCurrentUser = (int) $user->id === (int) Auth::id();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ];

        // Akun yang sedang login tidak boleh mengganti role sendiri.
        if (! $isCurrentUser) {
            $rules['role_id'] = [
                'required',
                'exists:roles,id',
            ];
        }

        // Password hanya diubah jika diisi.
        if ($request->filled('password')) {
            $rules['password'] = [
                'string',
                'min:8',
                'confirmed',
            ];
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! $isCurrentUser) {
            $user->role_id = $validated['role_id'];
        }

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('administrator.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }
}
