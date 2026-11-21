<?php

namespace App\Http\Controllers;

use App\Models\ReminderEmail;
use Illuminate\Http\Request;

class ReminderRecipientController extends Controller
{
    private function guard(): void
    {
        abort_unless(
            auth()->check() && auth()->user()->role === 'Atasan',
            403,
            'Hanya Atasan yang dapat mengelola penerima reminder.'
        );
    }

    public function store(Request $request)
    {
        $this->guard();

        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);

        $data = $request->validateWithBag('recipient', [
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:150', 'unique:reminder_emails,email'],
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email ini sudah terdaftar.',
        ]);

        ReminderEmail::create([
            'name'      => trim($data['name']),
            'email'     => $data['email'],
            'is_active' => true,
        ]);

        return redirect()->route('reminders.index')
            ->with('recipient_success', 'Penerima reminder ditambahkan.');
    }

    public function toggle(ReminderEmail $recipient)
    {
        $this->guard();

        $recipient->update(['is_active' => ! $recipient->is_active]);

        return redirect()->route('reminders.index')
            ->with('recipient_success', $recipient->is_active ? 'Penerima diaktifkan.' : 'Penerima dinonaktifkan.');
    }

    public function destroy(ReminderEmail $recipient)
    {
        $this->guard();

        $recipient->delete();

        return redirect()->route('reminders.index')
            ->with('recipient_success', 'Penerima dihapus.');
    }
}