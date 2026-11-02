<?php

namespace App\Console\Commands;

use App\Mail\MoldReminderMail;
use App\Models\Mold;
use App\Models\User;
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
            ->filter(fn (Mold $m) => $m->status !== 'normal');

        if ($molds->isEmpty()) {
            $this->info('Tidak ada mold yang perlu dikirimi reminder.');
            return self::SUCCESS;
        }

        $atasan = User::where('role', 'Atasan')->whereNotNull('email')->get();
        $sent   = 0;

        // Satu email per PIC. Mold tanpa PIC dikirim ke Atasan.
        foreach ($molds->groupBy('pic_id') as $picId => $group) {
            $pic = $group->first()->pic;

            $to = $pic && $pic->email ? $pic : $atasan->first();
            if (! $to) {
                $this->warn('Tidak ada penerima untuk PIC id ' . $picId . ', dilewati.');
                continue;
            }

            $cc = $atasan->where('id', '!=', $to->id)->pluck('email')->all();

            Mail::to($to->email)
                ->cc($cc)
                ->send(new MoldReminderMail($group->values(), $to->name));

            Mold::whereIn('id', $group->pluck('id'))->update(['reminder_sent_at' => now()]);

            $sent += $group->count();
            $this->info('Terkirim ke ' . $to->email . ' (' . $group->count() . ' mold).');
        }

        $this->info("Selesai. Total mold yang dikirimi reminder: {$sent}.");

        return self::SUCCESS;
    }
}