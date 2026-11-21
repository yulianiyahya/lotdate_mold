@php
    $isAtasan = auth()->user()?->role === 'Atasan';
@endphp

<style>
    .rc-wrap { margin-top: 28px; }
    .rc-wrap h2 { margin: 0 0 4px; font-size: 18px; }
    .rc-form { display: flex; gap: 10px; flex-wrap: wrap; padding: 16px; border-bottom: 1px solid #f1f2f4; }
    .rc-form input {
        flex: 1; min-width: 200px; padding: 10px 12px; border-radius: 10px;
        border: 1px solid #d1d5db; font: inherit; font-size: 14px;
    }
    .rc-form input:focus { outline: 2px solid #fca5a5; border-color: #dc2626; }
    .rc-btn {
        padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer;
        border: 1px solid #e5e7eb; background: #fff; color: #111827;
    }
    .rc-btn:hover { background: #f9fafb; }
    .rc-btn.primary { background: #dc2626; border-color: #dc2626; color: #fff; }
    .rc-btn.primary:hover { background: #b91c1c; }
    .rc-btn.danger { color: #dc2626; border-color: #fecaca; }
    .rc-btn.danger:hover { background: #fef2f2; }
    .rc-btn.sm { padding: 6px 12px; font-size: 12px; }
    .rc-state { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
    .rc-state.on  { background: #dcfce7; color: #15803d; }
    .rc-state.off { background: #f3f4f6; color: #6b7280; }
    .rc-err { width: 100%; color: #dc2626; font-size: 12px; }
    .rc-ok  { margin: 12px 16px 0; padding: 10px 14px; border-radius: 10px; background: #dcfce7; color: #15803d; font-size: 14px; }
    .rc-actions { display: flex; gap: 8px; }
    .rc-actions form { margin: 0; }

    /* Dialog hapus */
    .rc-dialog {
        border: 0; padding: 0; border-radius: 20px; width: min(420px, calc(100vw - 32px));
        background: #fff; color: #111827; box-shadow: 0 24px 60px rgba(17,24,39,.28); overflow: hidden;
    }
    .rc-dialog[open] { animation: rc-pop .18s ease-out; }
    .rc-dialog::backdrop { background: rgba(17,24,39,.5); backdrop-filter: blur(3px); }
    @keyframes rc-pop { from { opacity: 0; transform: translateY(8px) scale(.97); } to { opacity: 1; transform: none; } }
    .rc-dialog-body { padding: 28px 28px 8px; text-align: center; }
    .rc-dialog-icon {
        width: 56px; height: 56px; margin: 0 auto 16px; border-radius: 50%;
        background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center;
    }
    .rc-dialog-icon svg { width: 28px; height: 28px; }
    .rc-dialog h3 { margin: 0 0 8px; font-size: 20px; }
    .rc-dialog p { margin: 0; font-size: 14px; line-height: 1.6; color: #6b7280; }
    .rc-dialog-name {
        display: inline-block; margin-top: 12px; padding: 6px 12px; border-radius: 10px;
        background: #f3f4f6; font-weight: 600; font-size: 14px; color: #111827; word-break: break-all;
    }
    .rc-dialog-actions { display: flex; gap: 10px; padding: 20px 28px 28px; }
    .rc-dialog-actions button {
        flex: 1; padding: 11px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer;
        border: 1px solid #e5e7eb; background: #fff; color: #111827;
    }
    .rc-dialog-actions button:hover { background: #f9fafb; }
    .rc-dialog-actions .go-red { background: #dc2626; border-color: #dc2626; color: #fff; }
    .rc-dialog-actions .go-red:hover { background: #b91c1c; }
</style>

<div class="rc-wrap">
    <h2>Penerima Reminder</h2>
    <p class="subtitle" style="margin:0 0 12px;">
        Reminder dikirim ke email yang berstatus aktif di bawah ini.
    </p>

    <div class="card card-flush">
        @if (session('recipient_success'))
            <div class="rc-ok">{{ session('recipient_success') }}</div>
        @endif

        @if ($isAtasan)
            <form method="POST" action="{{ route('reminders.recipients.store') }}" class="rc-form">
                @csrf
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama penerima (mis. Budi - PE)" maxlength="100" required>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="email@perusahaan.com" maxlength="150" required>
                <button type="submit" class="rc-btn primary">Tambah</button>
                @if ($errors->recipient->any())
                    <div class="rc-err">{{ $errors->recipient->first() }}</div>
                @endif
            </form>
        @endif

        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Status</th>
                        @if ($isAtasan) <th>Aksi</th> @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recipients as $r)
                        <tr>
                            <td>{{ $r->name }}</td>
                            <td>{{ $r->email }}</td>
                            <td>
                                <span class="rc-state {{ $r->is_active ? 'on' : 'off' }}">{{ $r->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            @if ($isAtasan)
                                <td>
                                    <div class="rc-actions">
                                        <form method="POST" action="{{ route('reminders.recipients.toggle', $r) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rc-btn sm">{{ $r->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('reminders.recipients.destroy', $r) }}"
                                              class="js-delete-recipient" data-email="{{ $r->email }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rc-btn sm danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAtasan ? 4 : 3 }}" style="text-align:center;color:#6b7280;padding:24px;">
                                Belum ada penerima reminder.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if ($isAtasan)
    {{-- Dialog hapus penerima --}}
    <dialog id="rcDeleteDialog" class="rc-dialog">
        <div class="rc-dialog-body">
            <div class="rc-dialog-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14M10 11v5M14 11v5"/></svg>
            </div>
            <h3>Hapus penerima reminder?</h3>
            <p>Email ini tidak akan menerima reminder lagi. Kamu bisa menambahkannya kembali kapan saja.</p>
            <span class="rc-dialog-name" id="rcDeleteEmail"></span>
        </div>
        <div class="rc-dialog-actions">
            <button type="button" id="rcDeleteCancel">Batal</button>
            <button type="button" id="rcDeleteOk" class="go-red">Ya, hapus</button>
        </div>
    </dialog>

    <script>
        (function () {
            const dialog = document.getElementById('rcDeleteDialog');
            const emailEl = document.getElementById('rcDeleteEmail');
            const okBtn = document.getElementById('rcDeleteOk');
            let pendingForm = null;

            document.querySelectorAll('.js-delete-recipient').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    pendingForm = form;
                    emailEl.textContent = form.dataset.email;
                    okBtn.disabled = false;
                    okBtn.textContent = 'Ya, hapus';
                    dialog.showModal();
                });
            });

            okBtn.addEventListener('click', function () {
                if (!pendingForm) return;
                okBtn.disabled = true;
                okBtn.textContent = 'Menghapus...';
                pendingForm.submit();
            });

            document.getElementById('rcDeleteCancel').addEventListener('click', function () { dialog.close(); });
            dialog.addEventListener('close', function () { pendingForm = null; });

            // Klik di luar kotak dialog = tutup
            dialog.addEventListener('click', function (e) {
                const r = dialog.getBoundingClientRect();
                const inside = e.clientX >= r.left && e.clientX <= r.right &&
                               e.clientY >= r.top  && e.clientY <= r.bottom;
                if (!inside) dialog.close();
            });
        })();
    </script>
@endif