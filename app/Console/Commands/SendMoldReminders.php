<?php

namespace App\Console\Commands;

use App\Mail\MoldReminderMail;
use App\Models\Mold;
use App\Models\ReminderEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMoldReminders extends Command
{
    protected $signature = 'reminders:send';

    protected $description = 'Kirim email reminder untuk mold berstatus Warning atau Expired';

    public function handle(): int
    {
        $molds = Mold::with('pic')
            ->where('is_active', true)
            ->whereNull('reminder_sent_at')
            ->get()
            ->filter(fn (Mold $m) => $m->status !== 'normal')
            ->values();

        if ($molds->isEmpty()) {
            $this->info('Tidak ada mold yang perlu dikirimi reminder.');
            return self::SUCCESS;
        }

        $recipients = ReminderEmail::where('is_active', true)->orderBy('name')->get();

        if ($recipients->isEmpty()) {
            $this->error('Belum ada penerima reminder yang aktif. Tambahkan penerima di halaman Reminder.');
            return self::FAILURE;
        }

        $success = [];
        $failed  = [];

        // Satu email per penerima, jadi satu kegagalan tidak menggagalkan yang lain.
        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient->email)
                    ->send(new MoldReminderMail($molds, $recipient->name));

                $success[] = $recipient->email;
            } catch (\Throwable $e) {
                $failed[] = $recipient->email . ' (' . $e->getMessage() . ')';
            }
        }

        // Tidak ada satu pun yang berhasil: jangan tandai mold sebagai terkirim.
        if (empty($success)) {
            $this->error('Semua pengiriman gagal.');
            foreach ($failed as $line) {
                $this->error($line);
            }
            return self::FAILURE;
        }

        Mold::whereIn('id', $molds->pluck('id'))->update(['reminder_sent_at' => now()]);

        $this->info('Terkirim ke ' . count($success) . ' penerima: ' . implode(', ', $success) . '.');

        if (! empty($failed)) {
            $this->warn('Gagal ke ' . count($failed) . ' penerima: ' . implode('; ', $failed));
        }

        $this->info('Selesai. Total mold yang dikirimi reminder: ' . $molds->count() . '.');

        return self::SUCCESS;
    }
}