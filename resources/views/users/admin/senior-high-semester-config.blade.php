@php
    $statusBadgeClass = fn (?string $slug) => match ($slug) {
        'open' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'active' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'closed' => 'bg-amber-50 text-amber-700 ring-amber-200',
        default => 'bg-gray-100 text-gray-600 ring-gray-200',
    };
@endphp

<div class="max-w-5xl">
    <div class="mb-6">
        <h2 class="text-xl font-bold tracking-tight text-gray-800 md:text-2xl">Senior High Semesters</h2>
        <p class="mt-1 text-sm text-gray-500">Configure the availability and active semester for Senior High School. Grading terms are not used.</p>
    </div>

    <section class="overflow-hidden rounded-xl border border-gray-300 bg-white shadow-lg shadow-gray-200/70">
        <div class="border-b border-gray-100 px-6 py-5">
            <h3 class="text-lg font-bold text-gray-900">Semesters</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[580px] border-collapse text-left">
                <thead>
                    <tr class="border-b border-gray-300 bg-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-600">
                        <th class="border-r border-gray-200 px-5 py-4">Semester</th>
                        <th class="border-r border-gray-200 px-5 py-4">Availability</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @foreach ($seniorHighSemesters as $semester)
                        @php
                            $isCurrentSemester = (int) $settings->semester_ID === (int) $semester->semester_ID;
                            $isOpen = $semester->status?->slug === 'open';
                        @endphp
                        <tr class="bg-white transition even:bg-gray-50/70 hover:bg-[#296374]/[0.06]">
                            <td class="border-r border-gray-100 px-5 py-4 font-semibold text-gray-900">
                                {{ $semester->label }}
                                @if ($isCurrentSemester)
                                    <p class="mt-0.5 text-xs font-semibold text-[#296374]">Active semester</p>
                                @endif
                            </td>
                            <td class="border-r border-gray-100 px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold ring-1 {{ $statusBadgeClass($semester->status?->slug) }}">{{ $semester->status?->name ?? 'Available' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    @if (! $isCurrentSemester && $semester->isActive())
                                        <form action="{{ route($managementRoutePrefix.'grading-term-config.senior-high.semester.update') }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="semester_ID" value="{{ $semester->semester_ID }}">
                                            <button type="submit" class="h-9 rounded-lg bg-[#296374] px-3 text-xs font-bold text-white transition hover:opacity-90">Set active</button>
                                        </form>
                                    @endif
                                    <form action="{{ route($managementRoutePrefix.'grading-term-config.senior-high.semester.status', $semester) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="{{ $isOpen ? 'closed' : 'open' }}">
                                        <button type="submit" class="h-9 rounded-lg px-3 text-xs font-bold text-white transition {{ $isOpen ? 'bg-amber-600 hover:bg-amber-700' : 'bg-[#296374] hover:opacity-90' }}">{{ $isOpen ? 'Close' : 'Open' }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
