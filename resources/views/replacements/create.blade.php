@extends('layouts.app')

@section('title', 'Catat penggantian')

@section('content')
    @php
        $unitKey   = $mold->frequency_unit ?: 'Year';
        $unitLabel = \App\Models\Mold::UNITS[$unitKey] ?? 'Tahun';
        $partNos   = implode(', ', $mold->part_no_list);
        $cfg       = [
            'unit'      => $unitKey,
            'unitLabel' => strtolower($unitLabel),
            'leadValue' => (int) $mold->lead_time_value,
            'leadUnit'  => $mold->lead_time_unit,
        ];
    @endphp

    <div class="breadcrumb"><a href="{{ route('molds.index') }}">Daftar Mold</a> / Catat penggantian</div>
    <h1 class="title">Catat penggantian</h1>
    <p class="subtitle">Isi data setelah lot date mold selesai diganti di lapangan.</p>

    @if (session('error'))
        <div class="alert">{{ session('error') }}</div>
    @endif

    <div class="two-col">
        <div class="card">
            <form method="POST" action="{{ route('replacements.store', $mold) }}" enctype="multipart/form-data">
                @csrf

                <div class="grid-2">
                    <div class="field">
                        <label>Kode mold</label>
                        <input type="text" value="{{ $mold->part_name }}" readonly>
                    </div>
                    <div class="field">
                        <label>Part no</label>
                        <input type="text" value="{{ $partNos ?: '-' }}" readonly>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="field">
                        <label>Kategori</label>
                        <input type="text" value="{{ $mold->category ?: '-' }}" readonly>
                    </div>
                    <div class="field">
                        <label>Tipe</label>
                        <input type="text" value="{{ $mold->type ?: '-' }}" readonly>
                    </div>
                </div>

                <div class="field">
                    <label for="replacement_date">Tanggal penggantian</label>
                    <input type="date" id="replacement_date" name="replacement_date"
                           @if ($mold->last_replaced_date) min="{{ $mold->last_replaced_date->toDateString() }}" @endif
                           max="{{ now()->toDateString() }}"
                           value="{{ old('replacement_date', now()->toDateString()) }}" required>
                    @error('replacement_date') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="grid-2">
                    <div class="field">
                        <label for="frequency_value">Frekuensi penggantian</label>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span>Setiap</span>
                            <input type="number" id="frequency_value" name="frequency_value"
                                   min="1" max="{{ $maxFrequency }}" step="1" style="max-width:120px;"
                                   value="{{ old('frequency_value', $mold->frequency_value) }}" required>
                            <span>{{ strtolower($unitLabel) }}</span>
                        </div>
                        <div class="hint">Per berapa {{ strtolower($unitLabel) }} mold ini diganti.</div>
                        @error('frequency_value') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label>Jatuh tempo berikutnya</label>
                        <input type="text" id="new_due" value="Dihitung otomatis" readonly>
                        <div class="hint">
                            @if ($unitKey === 'Year')
                                Selalu akhir tahun: 31 Desember tahun penggantian ditambah frekuensi.
                            @elseif ($unitKey === 'Month')
                                Dihitung dari tanggal penggantian ditambah jumlah bulan.
                            @else
                                Dihitung dari tanggal penggantian ditambah jumlah hari.
                            @endif
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label>Foto sebelum dan sesudah</label>
                    <div class="grid-2" style="gap:0 16px;">
                        <div>
                            <label class="dropzone">
                                <strong>Sebelum</strong>
                                <span class="btn btn-outline btn-sm">Pilih foto</span>
                                <small class="fname">JPG atau PNG</small>
                                <img alt="Pratinjau foto sebelum">
                                <input type="file" name="before_photo" accept="image/png,image/jpeg" required>
                            </label>
                            @error('before_photo') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="dropzone">
                                <strong>Sesudah</strong>
                                <span class="btn btn-outline btn-sm">Pilih foto</span>
                                <small class="fname">JPG atau PNG</small>
                                <img alt="Pratinjau foto sesudah">
                                <input type="file" name="after_photo" accept="image/png,image/jpeg" required>
                            </label>
                            @error('after_photo') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label for="notes">Catatan (opsional)</label>
                    <textarea id="notes" name="notes" placeholder="Contoh: penggantian dilakukan saat maintenance rutin">{{ old('notes') }}</textarea>
                    @error('notes') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan penggantian</button>
                    <a href="{{ route('molds.index') }}" class="btn">Batal</a>
                </div>
            </form>
        </div>

        <div class="card summary">
            <h2>Ringkasan mold</h2>
            <p class="hint" style="margin:0 0 10px;">Perubahan setelah disimpan.</p>

            <div class="summary-row"><span>Kode mold</span><b>{{ $mold->part_name }}</b></div>
            <div class="summary-row"><span>Part no</span><b>{{ $partNos ?: '-' }}</b></div>
            <div class="summary-row"><span>Kategori</span><b>{{ $mold->category ?: '-' }}</b></div>
            <div class="summary-row"><span>Tipe</span><b>{{ $mold->type ?: '-' }}</b></div>
            <div class="summary-row"><span>Jatuh tempo lama</span><b>{{ $mold->next_due_date->format('d/m/Y') }}</b></div>
            <div class="summary-row"><span>Status saat ini</span><span class="badge badge-{{ $mold->status }}">{{ $mold->status_label }}</span></div>
            <div class="summary-row"><span>Status setelah disetujui</span><span id="sum_status">-</span></div>
            <div class="summary-row"><span>Frekuensi</span><b id="sum_freq">-</b></div>
            <div class="summary-row"><span>Jatuh tempo baru</span><b id="sum_due">-</b></div>
            <div class="summary-row"><span>Reminder email</span><b>Direset setelah disetujui</b></div>

            <div class="approvers">
                <h3>Menunggu persetujuan dari</h3>
                @forelse ($approvers as $i => $approver)
                    <div class="approver"><span class="step">{{ $i + 1 }}</span>{{ $approver->name }}</div>
                @empty
                    <div class="hint">Belum ada akun Atasan aktif sebagai approver.</div>
                @endforelse
                <div class="hint">Disetujui berurutan. Status mold berubah setelah approver terakhir menyetujui.</div>
            </div>

            <p class="hint" style="margin-top:16px;">Penggantian akan masuk ke riwayat mold ini.</p>
        </div>
    </div>

    <script>
        (function () {
            // Aturan sama persis dengan Mold::calculateDueDate() dan Mold::getWarningStartAttribute()
            const cfg = @json($cfg);

            const date      = document.getElementById('replacement_date');
            const freq      = document.getElementById('frequency_value');
            const newDue    = document.getElementById('new_due');
            const sumDue    = document.getElementById('sum_due');
            const sumFreq   = document.getElementById('sum_freq');
            const sumStatus = document.getElementById('sum_status');

            function parseDate(s) {
                const p = s.split('-').map(Number);
                return new Date(p[0], p[1] - 1, p[2]);
            }

            // Tambah bulan tanpa meluap ke bulan berikutnya (setara addMonthsNoOverflow)
            function addMonths(d, n) {
                const t = new Date(d.getFullYear(), d.getMonth() + n, 1);
                const last = new Date(t.getFullYear(), t.getMonth() + 1, 0).getDate();
                t.setDate(Math.min(d.getDate(), last));
                return t;
            }

            function dueDate(d, n) {
                if (cfg.unit === 'Day')   return new Date(d.getFullYear(), d.getMonth(), d.getDate() + n);
                if (cfg.unit === 'Month') return addMonths(d, n);
                return new Date(d.getFullYear() + n, 11, 31);
            }

            function warnStart(due) {
                const n = cfg.leadValue;
                if (cfg.leadUnit === 'Day')  return new Date(due.getFullYear(), due.getMonth(), due.getDate() - n);
                if (cfg.leadUnit === 'Year') return addMonths(due, -12 * n);
                return addMonths(due, -n);
            }

            function fmt(d) {
                const dd = String(d.getDate()).padStart(2, '0');
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                return dd + '/' + mm + '/' + d.getFullYear();
            }

            function reset() {
                newDue.value = 'Dihitung otomatis';
                sumDue.textContent = '-';
                sumFreq.textContent = '-';
                sumStatus.textContent = '-';
                sumStatus.className = '';
            }

            function update() {
                const n = parseInt(freq.value, 10);

                if (!date.value || isNaN(n) || n < 1) {
                    reset();
                    return;
                }

                sumFreq.textContent = 'Setiap ' + n + ' ' + cfg.unitLabel;

                const due = dueDate(parseDate(date.value), n);
                newDue.value = fmt(due);
                sumDue.textContent = fmt(due);

                const today = new Date(); today.setHours(0, 0, 0, 0);
                const ws = warnStart(due);

                let key = 'normal', label = 'Normal';
                if (today > due) { key = 'expired'; label = 'Expired'; }
                else if (today >= ws) { key = 'warning'; label = 'Warning'; }

                sumStatus.textContent = label;
                sumStatus.className = 'badge badge-' + key;
            }

            date.addEventListener('input', update);
            freq.addEventListener('input', update);
            update();

            document.querySelectorAll('.dropzone input').forEach(function (input) {
                input.addEventListener('change', function () {
                    const box = input.closest('.dropzone');
                    const file = input.files[0];
                    if (!file) return;
                    box.querySelector('.fname').textContent = file.name;
                    const img = box.querySelector('img');
                    img.src = URL.createObjectURL(file);
                    img.style.display = 'block';
                });
            });
        })();
    </script>
@endsection