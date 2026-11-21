<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'required'  => ':attribute wajib diisi.',
            'email'     => 'Format email tidak valid.',
            'unique'    => ':attribute sudah terdaftar.',
            'min'       => ':attribute minimal :min karakter.',
            'confirmed' => 'Konfirmasi password tidak cocok.',
        ], [
            'name'     => 'Nama',
            'email'    => 'Email',
            'password' => 'Password',
        ]);

        $user = new User();
        $user->forceFill([
            'name'     => $data['name'],
            'username' => $this->generateUsername($data['email']),
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'PIC', // pendaftaran mandiri selalu PIC
        ])->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Buat username unik dari bagian depan email.
     * Contoh: yulianiyahyarz@gmail.com -> yulianiyahyarz (atau yulianiyahyarz1, dst. jika sudah dipakai).
     */
    private function generateUsername(string $email): string
    {
        $base = Str::slug(Str::before($email, '@'), '') ?: 'user';
        $base = Str::limit($base, 40, '');

        $username = $base;
        $i = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $i++;
        }

        return $username;
    }
}