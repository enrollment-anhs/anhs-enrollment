<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Section List{{ $activeYear ? ' - '.$activeYear->school_year : '' }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px; color: #17202a; font: 12px/1.45 Arial, sans-serif; }
        .no-print { margin-bottom: 20px; }
        .button { display: inline-block; margin-right: 8px; padding: 9px 14px; border: 0; border-radius: 6px; background: #296374; color: #fff; font: 600 12px Arial, sans-serif; text-decoration: none; cursor: pointer; }
        .button-secondary { background: #6b7280; }
        h1 { margin: 0; font-size: 20px; }
        .subtitle { margin: 4px 0 20px; color: #5b6470; }
        .summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 22px; }
        .summary-card { border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px; }
        .summary-card span { display: block; color: #64748b; font-size: 10px; font-weight: bold; letter-spacing: .06em; text-transform: uppercase; }
        .summary-card strong { display: block; margin-top: 5px; color: #296374; font-size: 24px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #eaf2f4; color: #1f4e5c; font-size: 10px; letter-spacing: .04em; text-transform: uppercase; }
        .muted { color: #64748b; }
        .status { display: inline-block; border-radius: 999px; padding: 3px 7px; background: #dcfce7; color: #166534; font-size: 10px; font-weight: bold; }
        .status-full { background: #fee2e2; color: #991b1b; }
        @media print { body { padding: 0; } .no-print { display: none !important; } }
    </style>
</head>
<body>
    @if (empty($forPdf))
        <div class="no-print">
            <button type="button" class="button" onclick="window.print()">Print</button>
            <a href="{{ route('guidance.sections.index').(request()->getQueryString() ? '?'.request()->getQueryString() : '') }}" class="button button-secondary">Back to Sections</a>
        </div>
    @endif

    <h1>Section List</h1>
    <p class="subtitle">School Year: <strong>{{ $activeYear?->school_year ?? 'No active school year' }}</strong></p>

    <section class="summary" aria-label="Current school year enrollment summary">
        <div class="summary-card"><span>Enrolled students</span><strong>{{ number_format($summary['enrolled'] ?? 0) }}</strong></div>
        <div class="summary-card"><span>Temporarily enrolled</span><strong>{{ number_format($summary['temporarily_enrolled'] ?? 0) }}</strong></div>
        <div class="summary-card"><span>Total students this school year</span><strong>{{ number_format($summary['total'] ?? 0) }}</strong></div>
    </section>

    <table>
        <thead>
            <tr>
                <th>Section</th>
                <th>Grade level</th>
                <th>Details</th>
                <th>Students</th>
                <th>Availability</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sections as $section)
                @php
                    $capacity = (int) ($section->capacity ?? 0);
                    $students = (int) ($section->enrollments_count ?? 0);
                    $isFull = $capacity > 0 && $students >= $capacity;
                    $adviser = $section->adviser ? trim($section->adviser->first_name.' '.$section->adviser->last_name) : 'No adviser assigned';
                @endphp
                <tr>
                    <td><strong>{{ $section->name }}</strong></td>
                    <td>{{ strtoupper(str_replace('grade_', 'Grade ', $section->grade_level)) }}</td>
                    <td>
                        <div>Room: {{ $section->room ?: 'Not assigned' }}</div>
                        <div>Adviser: {{ $adviser }}</div>
                        @if ($section->cluster?->name)<div>Cluster: {{ $section->cluster->name }}</div>@endif
                    </td>
                    <td><strong>{{ number_format($students) }}</strong>{{ $capacity ? ' / '.number_format($capacity) : ' / no limit' }}</td>
                    <td><span class="status {{ $isFull ? 'status-full' : '' }}">{{ $isFull ? 'Full' : 'Available' }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted" style="text-align:center; padding:20px;">No sections found for the selected filters.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
