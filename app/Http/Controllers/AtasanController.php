<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AtasanController extends Controller
{
    public function __construct()
    {
        // Hanya Atasan yang boleh mengelola akun Atasan
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()->role === 'Atasan', 403);
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = User::where('role', 'Atasan');

        if ($request->filled('q')) {
            $kw = $request->q;
            $query->where(function ($w) use ($kw) {
                $w->where('name', 'like', "%{$kw}%")
                  ->orWhere('username', 'like', "%{$kw}%")
                  ->orWhere('email', 'like', "%{$kw}%");
            });
        }

        $items = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('atasan.index', compact('items'));
    }

    public function create()
    {
        return view('atasan.form', ['user' => new User()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'Atasan',
        ]);

        return redirect()->route('atasan.index')->with('success', 'Akun Atasan berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        abort_unless($user->role === 'Atasan', 404);

        return view('atasan.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'Atasan', 404);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email'    => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name     = $data['name'];
        $user->username = $data['username'];
        $user->email    = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('atasan.index')->with('success', 'Akun Atasan berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->role === 'Atasan', 404);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        if (User::where('role', 'Atasan')->count() <= 1) {
            return back()->with('error', 'Minimal harus ada satu akun Atasan.');
        }

        $user->delete();

        return redirect()->route('atasan.index')->with('success', 'Akun Atasan berhasil dihapus.');
    }
}