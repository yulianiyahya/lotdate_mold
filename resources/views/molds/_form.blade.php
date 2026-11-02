@php $m = $mold ?? null; @endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($m)
        @method('PUT')
    @endif

    <div class="grid-2">
        <div class="field">
            <label for="part_name">Part name / kode mold</label>
            <input type="text" id="part_name" name="part_name"
                   value="{{ old('part_name', $m?->part_name) }}" placeholder="Contoh: MLD-150" required>
            @error('part_name') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="part_no">Part no</label>
            <input type="text" id="part_no" name="part_no"
                   value="{{ old('part_no', $m?->part_no) }}">
            <div class="hint">Kalau lebih dari satu, pisahkan dengan koma.</div>
            @error('part_no') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="category">Kategori</label>
            <select id="category" name="category">
                <option value="">- Pilih kategori -</option>
                @foreach ($categories as $c)
                    <option value="{{ $c }}" {{ old('category', $m?->category) === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
            @error('category') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="type">Tipe</label>
            <input type="text" id="type" name="type"
                   value="{{ old('type', $m?->type) }}">
            @error('type') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="location">Lokasi</label>
            <input type="text" id="location" name="location"
                   value="{{ old('location', $m?->location) }}">
            @error('location') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="kebutuhan">Kebutuhan (pcs)</label>
            <input type="number" min="0" id="kebutuhan" name="kebutuhan"
                   value="{{ old('kebutuhan', $m?->kebutuhan) }}">
            @error('kebutuhan') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="supplier">Supplier</label>
            <input type="text" id="supplier" name="supplier"
                   value="{{ old('supplier', $m?->supplier) }}">
            @error('supplier') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="last_replaced_date">Penggantian terakhir</label>
            <input type="date" id="last_replaced_date" name="last_replaced_date" max="{{ now()->toDateString() }}"
                   value="{{ old('last_replaced_date', $m?->last_replaced_date?->toDateString()) }}" required>
            @error('last_replaced_date') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="frequency_value">Frekuensi penggantian</label>
            <div style="display:flex; gap:8px;">
                <input type="number" min="1" id="frequency_value" name="frequency_value"
                       value="{{ old('frequency_value', $m?->frequency_value ?? 1) }}" required>
                <select id="frequency_unit" name="frequency_unit" required>
                    @foreach ($units as $u)
                        <option value="{{ $u }}" {{ old('frequency_unit', $m?->frequency_unit ?? 'Year') === $u ? 'selected' : '' }}>
                            {{ \App\Models\Mold::UNITS[$u] }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('frequency_value') <div class="error">{{ $message }}</div> @enderror
            @error('frequency_unit') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="lead_time_value">Lead time (mulai warning sebelum jatuh tempo)</label>
            <div style="display:flex; gap:8px;">
                <input type="number" min="1" id="lead_time_value" name="lead_time_value"
                       value="{{ old('lead_time_value', $m?->lead_time_value ?? 2) }}" required>
                <select id="lead_time_unit" name="lead_time_unit" required>
                    @foreach ($units as $u)
                        <option value="{{ $u }}" {{ old('lead_time_unit', $m?->lead_time_unit ?? 'Month') === $u ? 'selected' : '' }}>
                            {{ \App\Models\Mold::UNITS[$u] }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('lead_time_value') <div class="error">{{ $message }}</div> @enderror
            @error('lead_time_unit') <div class="error">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="field">
        <label for="due_preview">Jatuh tempo berikutnya</label>
        <input type="text" id="due_preview" value="Dihitung otomatis" readonly>
        <div class="hint">Satuan Tahun: 31 Desember di tahun (penggantian + frekuensi). Bulan/Hari: dihitung dari tanggal penggantian.</div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan mold</button>
        <a href="{{ route('molds.index') }}" class="btn">Batal</a>
    </div>
</form>

<script>
    (function () {
        const date = document.getElementById('last_replaced_date');
        const val  = document.getElementById('frequency_value');
        const unit = document.getElementById('frequency_unit');
        const out  = document.getElementById('due_preview');

        const pad = n => String(n).padStart(2, '0');

        function update() {
            const n = parseInt(val.value, 10);
            if (!date.value || !n) { out.value = 'Dihitung otomatis'; return; }

            const [y, mo, d] = date.value.split('-').map(Number);
            let r;

            if (unit.value === 'Year') {
                r = new Date(y + n, 11, 31);
            } else if (unit.value === 'Month') {
                const lastDay = new Date(y, mo - 1 + n + 1, 0).getDate();
                r = new Date(y, mo - 1 + n, Math.min(d, lastDay));
            } else {
                r = new Date(y, mo - 1, d + n);
            }

            out.value = pad(r.getDate()) + '/' + pad(r.getMonth() + 1) + '/' + r.getFullYear();
        }

        [date, val, unit].forEach(el => el.addEventListener('input', update));
        unit.addEventListener('change', update);
        update();
    })();
</script>