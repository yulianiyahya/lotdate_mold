<?php

namespace App\Http\Controllers;

use App\Models\Mold;
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

        return view('reminders.index', ['molds' => $molds]);
    }

    public function send()
    {
        $this->guard();

        try {
            Artisan::call('reminders:send');
            $message = trim(Artisan::output()) ?: 'Proses reminder selesai.';
        } catch (\Throwable $e) {
            return redirect()->route('reminders.index')->with('error', 'Gagal mengirim: ' . $e->getMessage());
        }

        return redirect()->route('reminders.index')->with('success', $message);
    }

    private function guard(): void
    {
        abort_unless(auth()->user()->role === 'Atasan', 403);
    }
}