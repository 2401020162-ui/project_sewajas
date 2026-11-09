<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::where('role', Role::Customer)
            ->withCount('pesanan')
            ->when($request->filled('q'), function ($q) use ($request) {
                $cari = $request->string('q')->trim()->toString();
                $q->where(fn ($w) => $w->where('name', 'like', "%{$cari}%")
                    ->orWhere('email', 'like', "%{$cari}%")
                    ->orWhere('nomor_hp', 'like', "%{$cari}%"));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengguna.index', compact('users'));
    }

    public function show(User $user): View
    {
        abort_unless($user->isCustomer(), 404);

        $user->load(['pesanan.jas', 'pesanan.pembayaran']);

        return view('admin.pengguna.show', compact('user'));
    }
}
