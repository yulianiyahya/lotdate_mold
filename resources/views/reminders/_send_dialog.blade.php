<style>
    .sd-dialog {
        border: 0; padding: 0; border-radius: 20px; width: min(440px, calc(100vw - 32px));
        background: #fff; color: #111827; box-shadow: 0 24px 60px rgba(17,24,39,.28); overflow: hidden;
    }
    .sd-dialog[open] { animation: sd-pop .18s ease-out; }
    .sd-dialog::backdrop { background: rgba(17,24,39,.5); backdrop-filter: blur(3px); }
    @keyframes sd-pop { from { opacity: 0; transform: translateY(8px) scale(.97); } to { opacity: 1; transform: none; } }
    .sd-body { padding: 28px 28px 8px; text-align: center; }
    .sd-icon {
        width: 56px; height: 56px; margin: 0 auto 16px; border-radius: 50%;
        background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center;
    }
    .sd-icon svg { width: 28px; height: 28px; }
    .sd-dialog h3 { margin: 0 0 8px; font-size: 20px; }
    .sd-dialog p { margin: 0; font-size: 14px; line-height: 1.6; color: #6b7280; }
    .sd-actions { display: flex; gap: 10px; padding: 20px 28px 28px; }
    .sd-actions button {
        flex: 1; padding: 11px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer;
        border: 1px solid #e5e7eb; background: #fff; color: #111827;
    }
    .sd-actions button:hover { background: #f9fafb; }
    .sd-actions button:disabled { opacity: .7; cursor: wait; }
    .sd-actions .go-red { background: #dc2626; border-color: #dc2626; color: #fff; }
    .sd-actions .go-red:hover { background: #b91c1c; }
</style>

<dialog id="sdDialog" class="sd-dialog">
    <div class="sd-body">
        <div class="sd-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
        </div>
        <h3>Kirim reminder sekarang?</h3>
        <p>Email reminder akan dikirim ke semua penerima aktif untuk semua mold yang belum dikirimi.</p>
    </div>
    <div class="sd-actions">
        <button type="button" id="sdCancel">Batal</button>
        <button type="button" id="sdOk" class="go-red">Ya, kirim</button>
    </div>
</dialog>

<script>
    (function () {
        const form = document.querySelector('form[action$="/reminder/kirim"]');
        const dialog = document.getElementById('sdDialog');
        if (!form || !dialog) return;

        // Hapus popup bawaan browser (confirm) dari form lama
        form.removeAttribute('onsubmit');

        const okBtn = document.getElementById('sdOk');
        let confirmed = false;

        form.addEventListener('submit', function (e) {
            if (confirmed) return;
            e.preventDefault();
            okBtn.disabled = false;
            okBtn.textContent = 'Ya, kirim';
            dialog.showModal();
        });

        okBtn.addEventListener('click', function () {
            confirmed = true;
            okBtn.disabled = true;
            okBtn.textContent = 'Mengirim...';
            form.submit();
        });

        document.getElementById('sdCancel').addEventListener('click', function () { dialog.close(); });

        // Klik di luar kotak dialog = tutup
        dialog.addEventListener('click', function (e) {
            const r = dialog.getBoundingClientRect();
            const inside = e.clientX >= r.left && e.clientX <= r.right &&
                           e.clientY >= r.top  && e.clientY <= r.bottom;
            if (!inside) dialog.close();
        });
    })();
</script>