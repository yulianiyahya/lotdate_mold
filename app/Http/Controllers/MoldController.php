<?php

namespace App\Http\Controllers;

use App\Models\Mold;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MoldController extends Controller
{
    private const CATEGORIES = ['Blow', 'Inject'];
    private const UNITS      = ['Day', 'Month', 'Year'];

    public function index(Request $request)
    {
        $query = Mold::query()->where('is_active', true);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($w) => $w
                ->where('part_name', 'like', "%{$q}%")
                ->orWhere('part_no', 'like', "%{$q}%"));
        }

        if ($request->filled('kategori')) {
            $query->where('category', $request->kategori);
        }

        $today = Carbon::today()->toDateString();
        $warn  = Mold::warningStartSql();

        if ($request->status === 'expired') {
            $query->whereDate('next_due_date', '<', $today);
        } elseif ($request->status === 'warning') {
            $query->whereDate('next_due_date', '>=', $today)
                  ->whereRaw("DATE({$warn}) <= ?", [$today]);
        } elseif ($request->status === 'normal') {
            $query->whereRaw("DATE({$warn}) > ?", [$today]);
        }

        $molds = $query->orderBy('next_due_date')
            ->orderBy('part_name')
            ->paginate(10)
            ->withQueryString();

        return view('molds.index', [
            'molds'      => $molds,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function create()
    {
        $this->authorizePic();

        return view('molds.create', [
            'categories' => self::CATEGORIES,
            'types'      => self::CATEGORIES,
            'units'      => self::UNITS,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizePic();

        $data = $request->validate($this->rules(), $this->messages(), $this->attributes());

        Mold::create($data + [
            'next_due_date' => Mold::calculateDueDate($data['last_replaced_date'], (int) $data['frequency_value'], $data['frequency_unit']),
            'pic_id'        => auth()->id(),
            'is_active'     => true,
        ]);

        return redirect()->route('molds.index')->with('success', 'Mold berhasil ditambahkan.');
    }

    public function edit(Mold $mold)
    {
        $this->authorizePic();

        return view('molds.edit', [
            'mold'       => $mold,
            'categories' => self::CATEGORIES,
            'types'      => self::CATEGORIES,
            'units'      => self::UNITS,
        ]);
    }

    public function update(Request $request, Mold $mold)
    {
        $this->authorizePic();

        $data = $request->validate($this->rules(), $this->messages(), $this->attributes());

        $mold->update($data + [
            'next_due_date' => Mold::calculateDueDate($data['last_replaced_date'], (int) $data['frequency_value'], $data['frequency_unit']),
        ]);

        return redirect()->route('molds.index')->with('success', 'Data mold berhasil diperbarui.');
    }

    // Tidak menghapus permanen, supaya riwayat penggantian tetap aman.
    public function destroy(Mold $mold)
    {
        $this->authorizePic();

        $mold->update(['is_active' => false]);

        return redirect()->route('molds.index')->with('success', 'Mold berhasil dihapus.');
    }

    private function authorizePic(): void
    {
        abort_unless(auth()->user()->isPic(), 403);
    }

    private function rules(): array
    {
        return [
            'part_name'          => ['required', 'string', 'max:150'],
            'part_no'            => ['nullable', 'string', 'max:150'],
            'category'           => ['nullable', Rule::in(self::CATEGORIES)],
            'type'               => ['nullable', 'string', 'max:100'],
            'location'           => ['nullable', 'string', 'max:100'],
            'kebutuhan'          => ['nullable', 'integer', 'min:0'],
            'supplier'           => ['nullable', 'string', 'max:150'],
            'frequency_value'    => ['required', 'integer', 'min:1'],
            'frequency_unit'     => ['required', Rule::in(self::UNITS)],
            'lead_time_value'    => ['required', 'integer', 'min:1'],
            'lead_time_unit'     => ['required', Rule::in(self::UNITS)],
            'last_replaced_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    private function messages(): array
    {
        return [
            'required'      => ':attribute wajib diisi.',
            'max'           => ':attribute terlalu panjang.',
            'min'           => ':attribute harus lebih dari 0.',
            'integer'       => ':attribute harus berupa angka.',
            'kebutuhan.min' => 'Kebutuhan tidak boleh negatif.',
            'date'          => ':attribute tidak valid.',
            'last_replaced_date.before_or_equal' => 'Penggantian terakhir tidak boleh melewati hari ini.',
        ];
    }

    private function attributes(): array
    {
        return [
            'part_name'          => 'Part name',
            'part_no'            => 'Part no',
            'category'           => 'Kategori',
            'type'               => 'Tipe',
            'location'           => 'Lokasi',
            'kebutuhan'          => 'Kebutuhan',
            'supplier'           => 'Supplier',
            'frequency_value'    => 'Frekuensi',
            'frequency_unit'     => 'Satuan frekuensi',
            'lead_time_value'    => 'Lead time',
            'lead_time_unit'     => 'Satuan lead time',
            'last_replaced_date' => 'Penggantian terakhir',
        ];
    }
}