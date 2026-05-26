@extends('admin.layouts.app')
 
@section('title', 'Dashboard Overview')
 
@section('content')
 
<div class="space-y-8">
 
    <!-- Statistik Cards Grid -->
    <div class="grid grid-cols-4 gap-6">
 
        <!-- Card 1: Total Event -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex items-center justify-between transition hover:shadow-md duration-300">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                    Total Event
                </p>
                <h1 class="text-5xl font-extrabold text-gray-800 mt-3 leading-none tracking-tight">
                    {{ number_format($totalEvents) }}
                </h1>
                
                @php
                    $eventColor = str_contains($eventSubtext, '↗') ? 'text-green-500' : (str_contains($eventSubtext, '↘') ? 'text-red-500' : 'text-gray-400');
                @endphp
                <p class="{{ $eventColor }} font-bold mt-5 text-sm flex items-center gap-1.5 leading-none">
                    {{ $eventSubtext }}
                </p>
            </div>
 
            <div class="w-14 h-14 rounded-2xl bg-blue-50/50 border border-blue-100/50 flex items-center justify-center text-blue-600 shadow-sm">
                <!-- Outline Calendar Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
            </div>
        </div>
 
        <!-- Card 2: Total Peserta -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex items-center justify-between transition hover:shadow-md duration-300">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                    Total Peserta
                </p>
                <h1 class="text-5xl font-extrabold text-gray-800 mt-3 leading-none tracking-tight">
                    {{ number_format($totalParticipants) }}
                </h1>
                
                @php
                    $participantColor = str_contains($participantSubtext, '↗') ? 'text-green-500' : (str_contains($participantSubtext, '↘') ? 'text-red-500' : 'text-gray-400');
                @endphp
                <p class="{{ $participantColor }} font-bold mt-5 text-sm flex items-center gap-1.5 leading-none">
                    {{ $participantSubtext }}
                </p>
            </div>
 
            <div class="w-14 h-14 rounded-2xl bg-blue-50/50 border border-blue-100/50 flex items-center justify-center text-blue-600 shadow-sm">
                <!-- Outline User Group Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0 1 10.089 20.4c-2.114 0-4.082-.54-5.8-1.485a4.125 4.125 0 0 1 7.533-2.493c.501.91.786 1.957.786 3.07v-.003m-2.225-7.61c.642.457 1.412.728 2.247.728 2.21 0 4-1.79 4-4s-1.79-4-4-4c-.835 0-1.605.271-2.247.728m2.247 7.272a3.5 3.5 0 1 1-4.5 0H12.75a3.5 3.5 0 0 1-1.003-7.272M7.75 12a2.75 2.75 0 1 0 0-5.5 2.75 2.75 0 0 0 0 5.5Z" />
                </svg>
            </div>
        </div>
 
        <!-- Card 3: Total Check-In -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex items-center justify-between transition hover:shadow-md duration-300">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                    Total Check-In
                </p>
                <h1 class="text-5xl font-extrabold text-gray-800 mt-3 leading-none tracking-tight">
                    {{ number_format($totalCheckIn) }}
                </h1>
                
                <p class="text-blue-500 font-bold mt-5 text-sm flex items-center gap-1.5 leading-none">
                    <!-- Speedometer / Attendance circle icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-blue-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>{{ $attendancePercentage }}% Hadir</span>
                </p>
            </div>
 
            <div class="w-14 h-14 rounded-2xl bg-blue-50/50 border border-blue-100/50 flex items-center justify-center text-blue-600 shadow-sm">
                <!-- Outline Checklist Badge Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                </svg>
            </div>
        </div>
 
        <!-- Card 4: Total Certificate -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex items-center justify-between transition hover:shadow-md duration-300">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                    Total Certificate
                </p>
                <h1 class="text-5xl font-extrabold text-gray-800 mt-3 leading-none tracking-tight">
                    {{ number_format($totalCertificates) }}
                </h1>
                
                <p class="text-amber-500 font-bold mt-5 text-sm flex items-center gap-1.5 leading-none">
                    <!-- Clock pending icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-amber-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>{{ $pendingCertificates }} Pending</span>
                </p>
            </div>
 
            <div class="w-14 h-14 rounded-2xl bg-blue-50/50 border border-blue-100/50 flex items-center justify-center text-blue-600 shadow-sm">
                <!-- Outline Certificate / Book Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
        </div>
 
    </div>
 
    <!-- Chart Section -->
    <div class="grid grid-cols-3 gap-6">
 
        <!-- Bar Chart: Pertumbuhan Peserta -->
        <div class="col-span-2 bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex flex-col justify-between">
 
            <div class="flex justify-between items-center mb-10">
                <h2 class="text-3xl font-bold text-gray-800 tracking-tight">
                    Pertumbuhan Peserta
                </h2>
 
                <button class="border border-gray-200/80 px-5 py-2.5 rounded-xl text-gray-500 font-bold text-sm bg-white hover:bg-gray-50 transition">
                    7 Hari Terakhir
                </button>
            </div>
 
            <!-- Dynamic Chart -->
            <div class="flex items-end gap-6 h-80">
 
                @foreach($chartData as $day)
                <div class="flex flex-col items-center gap-3 flex-1 h-full justify-end">
                    <div class="w-full bg-[#528cf6] hover:bg-blue-600 transition-all duration-500 rounded-t-2xl relative group cursor-pointer" 
                         style="height: {{ $day['height'] }}%; min-height: 8px;">
                        
                        <!-- Tooltip on Hover -->
                        <span class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap z-10 shadow-lg">
                            {{ number_format($day['count']) }} Peserta
                        </span>
                    </div>
                    <span class="text-gray-400 font-bold text-xs">{{ $day['day'] }}</span>
                </div>
                @endforeach
 
            </div>
 
        </div>
 
        <!-- Donut Chart: Statistik Check-in -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex flex-col justify-between">
 
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 tracking-tight">
                    Statistik Check-in
                </h2>
 
                <button class="text-gray-300 text-xl font-bold hover:text-gray-500 transition">
                    ⋮
                </button>
            </div>
 
            <div class="flex justify-center my-6">
 
                <!-- Conic Gradient Donut Chart -->
                <div class="relative w-56 h-56 rounded-full flex items-center justify-center transition-all duration-500 hover:scale-105 shadow-[0_4px_20px_rgba(0,0,0,0.02)]" 
                     style="background: conic-gradient(#3b82f6 {{ $checkedInPercentage }}%, #e5e7eb {{ $checkedInPercentage }}%)">
 
                    <!-- Inner Card -->
                    <div class="absolute w-[180px] h-[180px] rounded-full bg-white flex flex-col items-center justify-center shadow-inner">
 
                        <h1 class="text-5xl font-extrabold text-gray-800 tracking-tight">
                            {{ $checkedInPercentage }}%
                        </h1>
 
                        <p class="text-gray-400 text-xs mt-1.5 font-bold uppercase tracking-widest">
                            CHECK-IN
                        </p>
 
                    </div>
 
                </div>
 
            </div>
 
            <!-- Legend aligned EXACTLY as screenshot -->
            <div class="mt-8 space-y-4 border-t border-gray-100 pt-6">
 
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                        <span class="font-bold text-gray-500 text-sm">Hadir</span>
                    </div>
                    <span class="font-extrabold text-gray-800 text-sm">{{ number_format($checkedInCount) }} Peserta</span>
                </div>
 
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-gray-200 rounded-full"></div>
                        <span class="font-bold text-gray-500 text-sm">Belum Hadir</span>
                    </div>
                    <span class="font-extrabold text-gray-800 text-sm">{{ number_format($notCheckedInCount) }} Peserta</span>
                </div>
 
            </div>
 
        </div>
 
    </div>
 
    <!-- Bottom Section: Event Terbaru & Aktivitas Terbaru -->
    <div class="grid grid-cols-3 gap-6">
 
        <!-- Table: Event Terbaru -->
        <div class="col-span-2 bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex flex-col justify-between">
 
            <div>
                <div class="flex justify-between items-center mb-8">
 
                    <h2 class="text-3xl font-bold text-gray-800 tracking-tight">
                        Event Terbaru
                    </h2>
 
                    <a href="/admin/events" class="text-blue-600 font-bold text-sm hover:text-blue-700 transition flex items-center gap-1">
                        Lihat Semua <span class="text-base leading-none">→</span>
                    </a>
 
                </div>
 
                <table class="w-full">
 
                    <thead class="text-gray-400 uppercase text-xs font-bold tracking-wider border-b border-gray-100 pb-4">
 
                        <tr>
 
                            <th class="text-left pb-4">
                                Nama Event
                            </th>
 
                            <th class="text-left pb-4">
                                Tanggal
                            </th>
 
                            <th class="text-left pb-4">
                                Lokasi
                            </th>
 
                            <th class="text-left pb-4">
                                Peserta
                            </th>
 
                            <th class="text-left pb-4">
                                Status
                            </th>
 
                        </tr>
 
                    </thead>
 
                    <tbody class="divide-y divide-gray-50">
 
                        @forelse($latestEvents as $event)
                        <tr class="hover:bg-gray-50/40 transition duration-150">
 
                            <td class="py-5 font-bold text-gray-800 text-sm">
                                {{ $event->name }}
                            </td>
 
                            <td class="py-5 text-gray-500 font-semibold text-sm">
                                {{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}
                            </td>
 
                            <td class="py-5 text-sm">
                                <span class="flex items-center gap-1">
                                    <!-- Outline Pin Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 flex-shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                    <span class="truncate text-gray-500 font-semibold max-w-[160px]">{{ $event->location }}</span>
                                </span>
                            </td>
 
                            <td class="py-5 text-gray-800 font-extrabold text-sm">
                                {{ number_format($event->registrations_count) }}
                            </td>
 
                            <td class="py-5">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-amber-50 text-amber-600 border border-amber-100/50',
                                        'open' => 'bg-green-50 text-green-600 border border-green-100/50',
                                        'closed' => 'bg-red-50 text-red-600 border border-red-100/50',
                                        'finished' => 'bg-blue-50 text-blue-600 border border-blue-100/50',
                                    ];
                                    $statusLabel = [
                                        'draft' => 'Draft',
                                        'open' => 'Aktif',
                                        'closed' => 'Tutup',
                                        'finished' => 'Selesai',
                                    ];
                                    $colorClass = $statusColors[$event->status] ?? 'bg-gray-50 text-gray-600 border border-gray-100';
                                    $label = $statusLabel[$event->status] ?? ucfirst($event->status);
                                @endphp
                                <span class="{{ $colorClass }} px-3 py-1.5 rounded-full text-xs font-bold tracking-wide">
                                    {{ $label }}
                                </span>
                            </td>
 
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 font-bold text-sm">
                                Belum ada event terdaftar.
                            </td>
                        </tr>
                        @endforelse
 
                    </tbody>
 
                </table>
            </div>
 
        </div>
 
        <!-- Card: Aktivitas Terbaru -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex flex-col justify-between">
 
            <div>
                <h2 class="text-3xl font-bold text-gray-800 tracking-tight mb-8">
                    Aktivitas Terbaru
                </h2>
 
                <div class="space-y-6">
 
                    @forelse($sortedActivities as $activity)
                    <div class="flex gap-4 items-center">
 
                        <div class="w-12 h-12 rounded-full {{ $activity['bg_color'] }} flex-shrink-0 flex items-center justify-center text-xl shadow-sm border border-gray-100/20">
                            @if(str_contains($activity['bg_color'], 'green'))
                            <!-- Green checklist SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-green-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            @else
                            <!-- Blue user SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-blue-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            @endif
                        </div>
 
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-700 text-sm leading-relaxed">
                                {!! $activity['title'] !!}
                            </p>
 
                            <p class="text-gray-400 text-xs mt-1.5 font-bold flex items-center gap-1">
                                <!-- Outline clock icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>{{ $activity['time'] }}</span>
                            </p>
                        </div>
 
                    </div>
                    @empty
                    <div class="text-center py-12 text-gray-400 font-semibold text-sm">
                        Belum ada aktivitas terbaru.
                    </div>
                    @endforelse
 
                </div>
            </div>
 
        </div>
 
    </div>
 
</div>
 
@endsection