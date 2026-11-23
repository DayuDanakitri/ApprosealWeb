<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserWallet;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    /**
     * Display all registered wallets.
     */
    public function index(Request $request)
    {
        $query = UserWallet::with('user')
            ->latest();

        // Search berdasarkan nama pengguna, email, atau alamat wallet.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('wallet_address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter berdasarkan pengguna.
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan status verifikasi.
        if ($request->filled('verification')) {
            if ($request->verification === 'verified') {
                $query->where('is_verified', true);
            } elseif ($request->verification === 'unverified') {
                $query->where('is_verified', false);
            }
        }

        $wallets = $query->paginate(15)->withQueryString();

        $users = User::orderBy('name')->get();

        $totalWallets = UserWallet::count();

        $verifiedWallets = UserWallet::where('is_verified', true)
            ->count();

        $unverifiedWallets = UserWallet::where('is_verified', false)
            ->count();

        $primaryWallets = UserWallet::where('is_primary', true)
            ->count();

        return view('administrator.wallets.index', compact(
            'wallets',
            'users',
            'totalWallets',
            'verifiedWallets',
            'unverifiedWallets',
            'primaryWallets'
        ));
    }

    /**
     * Show the form for creating a wallet.
     */
    public function create()
    {
        $users = User::with('role')
            ->orderBy('name')
            ->get();

        return view('administrator.wallets.create', compact(
            'users'
        ));
    }

    /**
     * Store a new wallet.
     */
    public function store(Request $request, AuditLogService $auditLog)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
            ],
            'wallet_address' => [
                'required',
                'string',
                'size:42',
                'regex:/^0x[a-fA-F0-9]{40}$/',
                'unique:user_wallets,wallet_address',
            ],
            'network_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'chain_id' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'is_primary' => [
                'nullable',
                'boolean',
            ],
        ]);

        DB::transaction(function () use ($validated, $auditLog) {
            $user = User::whereKey($validated['user_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $makePrimary = (bool) ($validated['is_primary'] ?? false);

            // Wallet pertama pengguna otomatis menjadi primary.
            if (! $user->wallets()->exists()) {
                $makePrimary = true;
            }

            // Hanya boleh ada satu primary wallet per pengguna.
            if ($makePrimary) {
                $user->wallets()->update([
                    'is_primary' => false,
                ]);
            }

            $wallet = UserWallet::create([
                'user_id' => $user->id,
                'wallet_address' => $validated['wallet_address'],
                'network_name' => $validated['network_name'] ?? null,
                'chain_id' => $validated['chain_id'] ?? null,
                'is_primary' => $makePrimary,
                'is_verified' => false,
                'verified_at' => null,
            ]);

            $auditLog->record(
                Auth::user(),
                'wallet_created',
                'Menambahkan wallet baru ke akun pengguna.',
                $wallet,
                [
                    'user_name' => $user->name,
                    'wallet_address' => $wallet->wallet_address,
                    'network_name' => $wallet->network_name,
                    'chain_id' => $wallet->chain_id,
                    'is_primary' => $wallet->is_primary,
                ]
            );
        });

        return redirect()
            ->route('administrator.wallets.index')
            ->with('success', 'Wallet berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a wallet.
     */
    public function edit(UserWallet $wallet)
    {
        $users = User::with('role')
            ->orderBy('name')
            ->get();

        return view('administrator.wallets.edit', compact(
            'wallet',
            'users'
        ));
    }

    /**
     * Update an existing wallet.
     */
    public function update(
        Request $request,
        UserWallet $wallet,
        AuditLogService $auditLog
    ) {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
            ],
            'wallet_address' => [
                'required',
                'string',
                'size:42',
                'regex:/^0x[a-fA-F0-9]{40}$/',
                Rule::unique('user_wallets', 'wallet_address')
                    ->ignore($wallet->id),
            ],
            'network_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'chain_id' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'is_primary' => [
                'nullable',
                'boolean',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $wallet,
            $auditLog
        ) {
            $lockedWallet = UserWallet::whereKey($wallet->id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldAddress = $lockedWallet->wallet_address;
            $oldUserId = $lockedWallet->user_id;

            $newUser = User::whereKey($validated['user_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $addressChanged = strtolower($oldAddress) !==
                strtolower($validated['wallet_address']);

            $userChanged = (int) $oldUserId !== (int) $newUser->id;

            $makePrimary = (bool) ($validated['is_primary'] ?? false);

            // Jika wallet dipindahkan ke pengguna lain dan
            // belum memiliki wallet lain, jadikan primary.
            $otherWalletExists = $newUser->wallets()
                ->where('id', '!=', $lockedWallet->id)
                ->exists();

            if (! $otherWalletExists) {
                $makePrimary = true;
            }

            if ($makePrimary) {
                $newUser->wallets()
                    ->where('id', '!=', $lockedWallet->id)
                    ->update([
                        'is_primary' => false,
                    ]);
            }

            // Jika primary wallet dipindahkan ke pengguna lain,
            // pilih wallet lama lainnya sebagai primary.
            if ($userChanged && $lockedWallet->is_primary) {
                $oldUser = User::whereKey($oldUserId)
                    ->lockForUpdate()
                    ->first();

                $replacementWallet = $oldUser?->wallets()
                    ->where('id', '!=', $lockedWallet->id)
                    ->first();

                if ($replacementWallet) {
                    $replacementWallet->update([
                        'is_primary' => true,
                    ]);
                }
            }

            $lockedWallet->update([
                'user_id' => $newUser->id,
                'wallet_address' => $validated['wallet_address'],
                'network_name' => $validated['network_name'] ?? null,
                'chain_id' => $validated['chain_id'] ?? null,
                'is_primary' => $makePrimary,
                'is_verified' => $addressChanged
                    ? false
                    : $lockedWallet->is_verified,
                'verified_at' => $addressChanged
                    ? null
                    : $lockedWallet->verified_at,
            ]);

            $auditLog->record(
                Auth::user(),
                'wallet_updated',
                'Memperbarui informasi wallet pengguna.',
                $lockedWallet,
                [
                    'previous_wallet_address' => $oldAddress,
                    'new_wallet_address' => $lockedWallet->wallet_address,
                    'previous_user_id' => $oldUserId,
                    'new_user_id' => $newUser->id,
                    'address_changed' => $addressChanged,
                    'is_primary' => $lockedWallet->is_primary,
                    'is_verified' => $lockedWallet->is_verified,
                ]
            );
        });

        return redirect()
            ->route('administrator.wallets.index')
            ->with('success', 'Informasi wallet berhasil diperbarui.');
    }
}
