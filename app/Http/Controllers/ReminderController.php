<?php

namespace App\Http\Controllers;

use App\Models\Mold;
use App\Models\ReminderEmail;
use Illuminate\Support\Facades\Artisan;

class ReminderController extends Controller
{
    public function index()
    {
        $this->guard();

        $molds = Mold::with('pic')
            ->where('is_active', true)
            ->get()
            ->filter(fn (Mold $m) => $m->status !== 'normal')
            ->sortBy('next_due_date');

        $recipients = ReminderEmail::orderBy('name')->get();

        return view('reminders.index', [
            'molds'      => $molds,
            'recipients' => $recipients,
        ]);
    }

    public function send()
    {
        $this->guard();

        try {
            $code    = Artisan::call('reminders:send');
            $message = trim(Artisan::output()) ?: 'Proses reminder selesai.';
        } catch (\Throwable $e) {
            return redirect()->route('reminders.index')->with('error', 'Gagal mengirim: ' . $e->getMessage());
        }

        if ($code !== 0) {
            return redirect()->route('reminders.index')->with('error', 'Gagal mengirim: ' . $message);
        }

        return redirect()->route('reminders.index')->with('success', $message);
    }

    private function guard(): void
    {
        abort_unless(auth()->user()->role === 'Atasan', 403);
    }
}