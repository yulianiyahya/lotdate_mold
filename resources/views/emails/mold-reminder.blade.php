<div style="font-family: Arial, sans-serif; font-size: 14px; color: #1e293b;">
    <p>Halo {{ $recipientName }},</p>
    <p>Mold berikut sudah masuk masa Warning atau sudah melewati jatuh tempo penggantian:</p>

    <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; border-color: #e2e6ec;">
        <thead style="background: #f8fafc;">
            <tr>
                <th align="left">Part Number</th>
                <th align="left">Part Name</th>
                <th align="left">Jatuh Tempo</th>
                <th align="left">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($molds as $m)
                <tr>
                    <td>{{ $m->part_number }}</td>
                    <td>{{ $m->part_name ?? '-' }}</td>
                    <td>{{ $m->next_due_date->format('d/m/Y') }}</td>
                    <td>{{ $m->status_label }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Mohon PIC segera mencatat penggantian di aplikasi LotDate Mold.</p>
</div>