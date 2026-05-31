@extends('admin.layouts.app')
 
@section('title', 'Dashboard Overview')
 
@section('content')
 
<div class="space-y-8">
 
    <!-- Statistik Cards Grid -->
    <div class="grid grid-cols-4 gap-6">
 
        <!-- Card 1: Event On-Going -->
        <div id="ongoingEventsCard" class="block bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex items-center justify-between transition hover:shadow-md hover:border-blue-200/80 duration-300 cursor-pointer group">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest group-hover:text-blue-500 transition-colors">
                    Event On-Going
                </p>
                <h1 class="text-5xl font-extrabold text-gray-800 mt-3 leading-none tracking-tight">
                    {{ number_format($ongoingEventsCount) }}
                </h1>
                
                @php
                    $eventColor = str_contains($eventSubtext, '⚡') ? 'text-blue-500' : (str_contains($eventSubtext, '↗') ? 'text-green-500' : (str_contains($eventSubtext, '↘') ? 'text-red-500' : 'text-gray-400'));
                @endphp
                <p class="{{ $eventColor }} font-bold mt-5 text-sm flex items-center gap-1.5 leading-none">
                    {{ $eventSubtext }}
                </p>
            </div>
 
            <div class="w-14 h-14 rounded-2xl bg-blue-50/50 border border-blue-100/50 flex items-center justify-center text-blue-600 shadow-sm group-hover:bg-blue-100/50 transition-colors flex-shrink-0">
                <!-- Outline Calendar Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
            </div>
        </div>

        <!-- Card 2: Total Event -->
        <a href="/admin/events" class="relative bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:border-blue-200/80 hover:-translate-y-1 transform cursor-pointer group">
            
            <!-- Tooltip saat hover -->
            <span class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap z-30 shadow-md pointer-events-none">
                Lihat semua event
            </span>

            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest group-hover:text-blue-500 transition-colors">
                    Total Event
                </p>
                <h1 class="text-5xl font-extrabold text-gray-800 mt-3 leading-none tracking-tight">
                    {{ number_format($totalEventsCount) }}
                </h1>
                
                @php
                    $totalEventsColor = str_contains($totalEventsSubtext, '↗') ? 'text-green-500' : (str_contains($totalEventsSubtext, '↘') ? 'text-red-500' : 'text-gray-400');
                @endphp
                <p class="{{ $totalEventsColor }} font-bold mt-5 text-sm flex items-center gap-1.5 leading-none">
                    {{ $totalEventsSubtext }}
                </p>
            </div>

            <div class="w-14 h-14 rounded-2xl bg-blue-50/50 border border-blue-100/50 flex items-center justify-center text-blue-600 shadow-sm group-hover:bg-blue-100/50 transition-colors flex-shrink-0">
                <!-- Outline User Group Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0 1 10.089 20.4c-2.114 0-4.082-.54-5.8-1.485a4.125 4.125 0 0 1 7.533-2.493c.501.91.786 1.957.786 3.07v-.003m-2.225-7.61c.642.457 1.412.728 2.247.728 2.21 0 4-1.79 4-4s-1.79-4-4-4c-.835 0-1.605.271-2.247.728m2.247 7.272a3.5 3.5 0 1 1-4.5 0H12.75a3.5 3.5 0 0 1-1.003-7.272M7.75 12a2.75 2.75 0 1 0 0-5.5 2.75 2.75 0 0 0 0 5.5Z" />
                </svg>
            </div>
        </a>
 
        <!-- Card 3: Tiket Terjual -->
        <button id="finishedEventsReportCard" class="bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:border-blue-200/80 hover:-translate-y-1 transform cursor-pointer group text-left w-full">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest group-hover:text-blue-500 transition-colors">
                    Tiket Terjual
                </p>
                <h1 class="text-5xl font-extrabold text-gray-800 mt-3 leading-none tracking-tight">
                    {{ number_format($ticketsSold) }}
                </h1>
                
                @php
                    $ticketsColor = str_contains($ticketsSubtext, '↗') ? 'text-green-500' : (str_contains($ticketsSubtext, '↘') ? 'text-red-500' : 'text-gray-400');
                @endphp
                <p class="{{ $ticketsColor }} font-bold mt-5 text-sm flex items-center gap-1.5 leading-none">
                    {{ $ticketsSubtext }}
                </p>
            </div>
 
            <div class="w-14 h-14 rounded-2xl bg-blue-50/50 border border-blue-100/50 flex items-center justify-center text-blue-600 shadow-sm group-hover:bg-blue-100/50 transition-colors flex-shrink-0">
                <!-- Outline Ticket Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M3 7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v9a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 16.5v-9Z" />
                </svg>
            </div>
        </button>
 
        <!-- Card 4: Pendapatan -->
        <button id="finishedRevenueReportCard" class="bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:border-blue-200/80 hover:-translate-y-1 transform cursor-pointer group text-left w-full">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest group-hover:text-blue-500 transition-colors">
                    Pendapatan
                </p>
                <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-800 mt-3.5 leading-none tracking-tight">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h1>
                
                @php
                    $revenueColor = str_contains($revenueSubtext, '↗') ? 'text-green-500' : (str_contains($revenueSubtext, '↘') ? 'text-red-500' : 'text-gray-400');
                @endphp
                <p class="{{ $revenueColor }} font-bold mt-5 text-sm flex items-center gap-1.5 leading-none">
                    {{ $revenueSubtext }}
                </p>
            </div>

            <div class="w-14 h-14 rounded-2xl bg-blue-50/50 border border-blue-100/50 flex items-center justify-center text-blue-600 shadow-sm group-hover:bg-blue-100/50 transition-colors flex-shrink-0">
                <!-- Outline Banknotes Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5a1.5 1.5 0 0 1 1.5 1.5v12a1.5 1.5 0 0 1-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V6a1.5 1.5 0 0 1 1.5-1.5Zm13.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                </svg>
            </div>
        </button>
 
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
 
        <!-- Table: Upcoming Events -->
        <div class="col-span-2 bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex flex-col justify-between">
 
            <div>
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 tracking-tight">
                            Upcoming Events
                        </h2>
                        <p class="text-gray-400 text-xs font-semibold mt-1">
                            Daftar event yang akan segera berlangsung
                        </p>
                    </div>
 
                    <a href="/admin/events" class="text-blue-600 font-bold text-sm hover:text-blue-700 transition flex items-center gap-1 mt-1">
                        Lihat Semua <span class="text-base leading-none">→</span>
                    </a>
 
                </div>
 
                <table class="w-full">
 
                    <thead class="text-gray-400 uppercase text-[10px] font-bold tracking-wider border-b border-gray-100 pb-4">
 
                        <tr>
                            <th class="text-left pb-4">NAMA EVENT</th>
                            <th class="text-left pb-4 pl-2">KATEGORI</th>
                            <th class="text-left pb-4 pl-2">TANGGAL EVENT</th>
                            <th class="text-left pb-4 pl-2">LOKASI</th>
                            <th class="text-left pb-4 pl-2">CAPACITY</th>
                            <th class="text-right pb-4 pr-2">COUNTDOWN</th>
                        </tr>
 
                    </thead>
 
                    <tbody class="divide-y divide-gray-50">
 
                        @forelse($upcomingEvents as $event)
                        <tr class="hover:bg-slate-50/65 transition duration-155">
 
                            <td class="py-5 font-bold text-gray-800 text-sm">
                                {{ $event->name }}
                            </td>
 
                            <td class="py-5 pl-2">
                                @php
                                    $categoryColors = [
                                        'Entertainment' => 'bg-purple-50 text-purple-600 border border-purple-100/50',
                                        'Education' => 'bg-indigo-50 text-indigo-600 border border-indigo-100/50',
                                        'Sports' => 'bg-emerald-50 text-emerald-600 border border-emerald-100/50',
                                        'Business' => 'bg-blue-50 text-blue-600 border border-blue-100/50',
                                        'Community' => 'bg-amber-50 text-amber-600 border border-amber-100/50',
                                    ];
                                    $catColor = $categoryColors[$event->category] ?? 'bg-gray-50 text-gray-600 border border-gray-150';
                                @endphp
                                <span class="{{ $catColor }} px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider whitespace-nowrap">
                                    {{ $event->category }}
                                </span>
                            </td>
 
                            <td class="py-5 pl-2 text-gray-500 font-semibold text-sm whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}
                            </td>
 
                            <td class="py-5 pl-2 text-sm">
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 flex-shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                    <span class="truncate text-gray-500 font-semibold max-w-[140px]">{{ $event->location }}</span>
                                </span>
                            </td>
 
                            <td class="py-5 pl-2 text-gray-800 font-extrabold text-sm whitespace-nowrap">
                                {{ number_format($event->capacity) }}
                            </td>
 
                            <td class="py-5 text-right pr-2">
                                @php
                                    $startDateTime = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time);
                                    $now = \Carbon\Carbon::now();
                                    
                                    $daysDiff = $now->diffInDays($startDateTime, false);
                                    $hoursDiff = $now->diffInHours($startDateTime, false);
                                    $minutesDiff = $now->diffInMinutes($startDateTime, false);
                                    
                                    if ($startDateTime->isToday()) {
                                        if ($hoursDiff > 0) {
                                            $countdownText = "Starts in {$hoursDiff} " . ($hoursDiff == 1 ? 'hour' : 'hours');
                                        } else {
                                            $countdownText = "Starts in " . max(1, $minutesDiff) . " " . ($minutesDiff == 1 ? 'minute' : 'minutes');
                                        }
                                        $countdownClass = 'text-amber-600 bg-amber-50 border border-amber-100';
                                    } elseif ($startDateTime->isTomorrow()) {
                                        $countdownText = "Starts tomorrow";
                                        $countdownClass = 'text-blue-600 bg-blue-50 border border-blue-100';
                                    } else {
                                        $displayDays = max(1, $startDateTime->diffInDays($now));
                                        $countdownText = "Starts in {$displayDays} days";
                                        $countdownClass = 'text-blue-600 bg-blue-50 border border-blue-100';
                                    }
                                @endphp
                                <span class="{{ $countdownClass }} px-2.5 py-1 rounded-full text-[10px] font-extrabold whitespace-nowrap">
                                    {{ $countdownText }}
                                </span>
                            </td>
 
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                        </svg>
                                    </div>
                                    <div class="font-bold text-sm text-gray-600">No upcoming events</div>
                                    <div class="text-xs font-semibold text-gray-400 max-w-[200px] mx-auto">There are no scheduled events starting soon.</div>
                                </div>
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

<!-- Modal Ongoing Events -->
<div id="ongoingEventsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 md:p-6 opacity-0 pointer-events-none transition-all duration-300">
    <!-- Backdrop Overlay -->
    <div id="ongoingEventsModalBackdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity duration-300"></div>

    <!-- Modal Content Box -->
    <div id="ongoingEventsModalBox" class="bg-white w-full max-w-4xl rounded-xl border border-gray-100 shadow-2xl flex flex-col max-h-[85vh] overflow-hidden transform scale-95 transition-all duration-300 z-10">
        
        <!-- Header -->
        <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100/80 bg-gray-50/50">
            <div class="flex items-center gap-3">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <h2 class="text-xl font-extrabold text-gray-800 tracking-tight">Ongoing Events</h2>
            </div>
            
            <button id="closeOngoingEventsModal" class="text-gray-400 hover:text-gray-600 bg-white hover:bg-gray-100 border border-gray-100 p-2 rounded-full transition duration-200 cursor-pointer shadow-sm flex items-center justify-center">
                <!-- X icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-6 bg-slate-50/30">
            @if($ongoingEvents->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($ongoingEvents as $event)
                        <div class="bg-white rounded-xl p-5 border border-gray-150 shadow-sm flex flex-col justify-between hover:shadow-md transition duration-300 relative overflow-hidden group">
                            <!-- Left vertical accent bar -->
                            <div class="absolute left-0 inset-y-0 w-1 bg-gradient-to-b from-green-500 to-emerald-500"></div>

                            <div class="space-y-3 flex-1 flex flex-col justify-between">
                                <div class="space-y-3">
                                    <!-- Top Header: LIVE + Category Badge -->
                                    <div class="flex items-center gap-2">
                                        <!-- LIVE Badge -->
                                        <span class="flex items-center gap-1.5 bg-green-50 text-green-600 border border-green-150 px-2 py-0.5 rounded-full text-[9px] font-extrabold tracking-wider uppercase">
                                            <span class="relative flex h-1.5 w-1.5">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-green-500"></span>
                                            </span>
                                            LIVE
                                        </span>

                                        <!-- Category Badge -->
                                        @php
                                            $categoryColors = [
                                                'Entertainment' => 'bg-purple-50 text-purple-600 border border-purple-100/50',
                                                'Education' => 'bg-indigo-50 text-indigo-600 border border-indigo-100/50',
                                                'Sports' => 'bg-emerald-50 text-emerald-600 border border-emerald-100/50',
                                                'Business' => 'bg-blue-50 text-blue-600 border border-blue-100/50',
                                                'Community' => 'bg-amber-50 text-amber-600 border border-amber-100/50',
                                            ];
                                            $catColor = $categoryColors[$event->category] ?? 'bg-gray-50 text-gray-600 border border-gray-150';
                                        @endphp
                                        <span class="{{ $catColor }} px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider">
                                            {{ $event->category }}
                                        </span>
                                    </div>

                                    <!-- Event Name -->
                                    <h3 class="text-base font-extrabold text-gray-800 tracking-tight leading-snug group-hover:text-blue-600 transition-colors line-clamp-2 min-h-[2.5rem] flex items-center">
                                        <a href="{{ route('admin.events.ongoing', $event->id) }}">
                                            {{ $event->name }}
                                        </a>
                                    </h3>

                                    <!-- Location & Date -->
                                    <div class="space-y-1.5 text-xs font-semibold text-gray-500">
                                        <div class="flex items-center gap-2">
                                            <span class="flex-shrink-0">📍</span>
                                            <span class="truncate">{{ $event->location }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="flex-shrink-0">📅</span>
                                            <span class="truncate">
                                                {{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}
                                                ({{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }})
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Registered & Countdown -->
                                    <div class="space-y-1.5 pt-2.5 border-t border-gray-105 text-xs font-semibold text-gray-500">
                                        <!-- Registered Participants -->
                                        <div class="flex items-center gap-2">
                                            <span class="flex-shrink-0">👥</span>
                                            <span class="truncate">
                                                Registered: <strong class="text-gray-700 font-extrabold">{{ number_format($event->registrations_count) }}</strong> / {{ number_format($event->capacity) }} Participants
                                            </span>
                                        </div>
                                        <!-- Countdown -->
                                        <div class="flex items-center gap-2 text-rose-500 font-extrabold">
                                            <span class="flex-shrink-0">⏳</span>
                                            <span>Ends in: <span data-modal-countdown="{{ $event->end_date }} {{ $event->end_time }}">Calculating...</span></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Attendance Progress -->
                                @php
                                    $attendanceProgress = $event->registrations_count > 0 
                                        ? round(($event->attended_count / $event->registrations_count) * 100) 
                                        : 0;
                                @endphp
                                <div class="space-y-1.5 pt-2">
                                    <div class="flex justify-between items-center text-[9px] font-extrabold text-gray-400 uppercase tracking-widest">
                                        <span>Attendance Progress</span>
                                        <span class="text-gray-700 font-extrabold text-xs">
                                            {{ $attendanceProgress }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden shadow-inner relative">
                                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-full rounded-full transition-all duration-700" 
                                             style="width: {{ $attendanceProgress }}%"></div>
                                    </div>
                                    <div class="flex justify-between items-center text-[9px] text-gray-400 font-bold">
                                        <span>{{ number_format($event->attended_count) }} Checked-In</span>
                                        <span>{{ number_format($event->registrations_count) }} Registered</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Monitor Event Button -->
                            <div class="mt-4 pt-3 border-t border-gray-100">
                                <a href="{{ route('admin.events.ongoing', $event->id) }}" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-sm hover:shadow transition duration-200 cursor-pointer flex items-center justify-center gap-1.5 whitespace-nowrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-white">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span>Monitor Event</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl border border-gray-100/80 p-16 text-center flex flex-col items-center justify-center shadow-sm w-full">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-500 mb-5 border border-amber-100/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">No Ongoing Events</h3>
                    <p class="text-gray-400 font-semibold text-sm mt-2">
                        Saat ini tidak ada event yang sedang berlangsung.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Finished Events Report -->
<div id="finishedEventsReportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 md:p-6 opacity-0 pointer-events-none transition-all duration-300">
    <!-- Backdrop Overlay -->
    <div id="finishedEventsReportModalBackdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity duration-300"></div>

    <!-- Modal Content Box -->
    <div id="finishedEventsReportModalBox" class="bg-white w-full max-w-2xl rounded-2xl border border-gray-100 shadow-2xl flex flex-col max-h-[85vh] overflow-hidden transform scale-95 transition-all duration-300 z-10">
        
        <!-- Header -->
        <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100/80 bg-gray-50/50">
            <div class="flex items-center gap-3">
                <span class="text-2xl">📊</span>
                <h2 class="text-xl font-extrabold text-gray-800 tracking-tight">Laporan Penjualan Event Selesai</h2>
            </div>
            
            <button id="closeFinishedEventsReportModal" class="text-gray-400 hover:text-gray-600 bg-white hover:bg-gray-100 border border-gray-100 p-2 rounded-full transition duration-200 cursor-pointer shadow-sm flex items-center justify-center">
                <!-- X icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-6 bg-slate-50/30">
            @if($finishedEventsReport->isNotEmpty())
                <div class="overflow-x-auto border border-gray-150 rounded-2xl bg-white shadow-sm">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="bg-slate-50 text-gray-400 uppercase text-[10px] font-bold tracking-wider border-b border-gray-150">
                                <th class="py-4 px-6">Nama Event</th>
                                <th class="py-4 px-6 text-center">Total Tiket Terjual</th>
                                <th class="py-4 px-6 text-right">Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($finishedEventsReport as $report)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-4 px-6 font-bold text-gray-800">{{ $report->name }}</td>
                                    <td class="py-4 px-6 text-center text-gray-600 font-semibold">{{ number_format($report->tickets_sold) }}</td>
                                    <td class="py-4 px-6 text-right text-gray-800 font-extrabold">Rp {{ number_format($report->revenue, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Summary Section -->
                <div class="mt-6 p-6 bg-blue-50/30 border border-blue-100/50 rounded-2xl grid grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Tiket Terjual</span>
                        <h3 class="text-3xl font-extrabold text-gray-800 leading-none">{{ number_format($totalFinishedTicketsSold) }}</h3>
                    </div>
                    <div class="space-y-1 text-right">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Pendapatan</span>
                        <h3 class="text-3xl font-extrabold text-blue-600 leading-none">Rp {{ number_format($totalFinishedRevenue, 0, ',', '.') }}</h3>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-2xl border border-gray-100/80 p-12 text-center flex flex-col items-center justify-center shadow-sm w-full">
                    <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 mb-5 border border-slate-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-extrabold text-gray-800 tracking-tight">Tidak Ada Event Selesai</h3>
                    <p class="text-gray-400 font-semibold text-xs mt-2">
                        Belum ada laporan untuk event dengan status selesai.
                    </p>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-100/80 bg-gray-50/50 flex justify-end">
            <button id="closeFinishedEventsReportModalBtn" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-xs shadow-sm hover:shadow transition duration-200 cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Finished Revenue Report -->
<div id="finishedRevenueReportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 md:p-6 opacity-0 pointer-events-none transition-all duration-300">
    <!-- Backdrop Overlay -->
    <div id="finishedRevenueReportModalBackdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity duration-300"></div>

    <!-- Modal Content Box -->
    <div id="finishedRevenueReportModalBox" class="bg-white w-full max-w-2xl rounded-2xl border border-gray-100 shadow-2xl flex flex-col max-h-[85vh] overflow-hidden transform scale-95 transition-all duration-300 z-10">
        
        <!-- Header -->
        <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100/80 bg-gray-50/50">
            <div class="flex items-center gap-3">
                <span class="text-2xl">💰</span>
                <h2 class="text-xl font-extrabold text-gray-800 tracking-tight">Laporan Pendapatan Event</h2>
            </div>
            
            <button id="closeFinishedRevenueReportModal" class="text-gray-400 hover:text-gray-600 bg-white hover:bg-gray-100 border border-gray-100 p-2 rounded-full transition duration-200 cursor-pointer shadow-sm flex items-center justify-center">
                <!-- X icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-6 bg-slate-50/30">
            @if($sortedFinishedEvents->isNotEmpty())
                <!-- Ringkasan Statistik Grid -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <!-- Total Pendapatan -->
                    <div class="p-4 bg-emerald-50/50 border border-emerald-100/50 rounded-2xl flex items-center gap-3">
                        <span class="text-2xl">💰</span>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Pendapatan</span>
                            <h4 class="text-base font-extrabold text-emerald-600">Rp {{ number_format($totalFinishedRevenue, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <!-- Event Pendapatan Tertinggi -->
                    <div class="p-4 bg-purple-50/50 border border-purple-100/50 rounded-2xl flex items-center gap-3">
                        <span class="text-2xl">🏆</span>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Event Terlaris</span>
                            <h4 class="text-xs font-extrabold text-purple-700 truncate max-w-[200px]" title="{{ $highestRevenueEventName }}">{{ $highestRevenueEventName }}</h4>
                        </div>
                    </div>
                    <!-- Total Tiket Terjual -->
                    <div class="p-4 bg-blue-50/50 border border-blue-100/50 rounded-2xl flex items-center gap-3">
                        <span class="text-2xl">🎟️</span>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Tiket Terjual</span>
                            <h4 class="text-base font-extrabold text-blue-600">{{ number_format($totalFinishedTicketsSold) }}</h4>
                        </div>
                    </div>
                    <!-- Rata-rata Pendapatan per Event -->
                    <div class="p-4 bg-amber-50/50 border border-amber-100/50 rounded-2xl flex items-center gap-3">
                        <span class="text-2xl">📈</span>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Rerata Pendapatan</span>
                            <h4 class="text-base font-extrabold text-amber-600">Rp {{ number_format($averageFinishedRevenue, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto border border-gray-150 rounded-2xl bg-white shadow-sm">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="bg-slate-50 text-gray-400 uppercase text-[10px] font-bold tracking-wider border-b border-gray-150">
                                <th class="py-4 px-6">Event</th>
                                <th class="py-4 px-6 text-right">Pendapatan</th>
                                <th class="py-4 px-6 text-center">Tiket Terjual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($sortedFinishedEvents as $report)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-4 px-6 font-bold text-gray-800">{{ $report->name }}</td>
                                    <td class="py-4 px-6 text-right text-emerald-600 font-extrabold">Rp {{ number_format($report->revenue, 0, ',', '.') }}</td>
                                    <td class="py-4 px-6 text-center text-gray-600 font-semibold">{{ number_format($report->tickets_sold) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-2xl border border-gray-100/80 p-12 text-center flex flex-col items-center justify-center shadow-sm w-full">
                    <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 mb-5 border border-slate-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-extrabold text-gray-800 tracking-tight">Tidak Ada Event Selesai</h3>
                    <p class="text-gray-400 font-semibold text-xs mt-2">
                        Belum ada laporan untuk event dengan status selesai.
                    </p>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-100/80 bg-gray-50/50 flex justify-end">
            <button id="closeFinishedRevenueReportModalBtn" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-xs shadow-sm hover:shadow transition duration-200 cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Interactivity & Countdown Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Modal Ongoing Events
        const ongoingEventsCard = document.getElementById('ongoingEventsCard');
        const modal = document.getElementById('ongoingEventsModal');
        const backdrop = document.getElementById('ongoingEventsModalBackdrop');
        const modalBox = document.getElementById('ongoingEventsModalBox');
        const closeBtn = document.getElementById('closeOngoingEventsModal');

        function openModal() {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            modalBox.classList.remove('scale-95');
            modalBox.classList.add('scale-100');
            document.body.style.overflow = 'hidden'; // Lock background scroll
        }

        function closeModal() {
            modal.classList.add('opacity-0', 'pointer-events-none');
            modal.classList.remove('opacity-100', 'pointer-events-auto');
            modalBox.classList.add('scale-95');
            modalBox.classList.remove('scale-100');
            document.body.style.overflow = ''; // Unlock background scroll
        }

        if (ongoingEventsCard) ongoingEventsCard.addEventListener('click', openModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (backdrop) backdrop.addEventListener('click', closeModal);

        // 2. Modal Finished Events Report
        const finishedEventsReportCard = document.getElementById('finishedEventsReportCard');
        const finishedModal = document.getElementById('finishedEventsReportModal');
        const finishedBackdrop = document.getElementById('finishedEventsReportModalBackdrop');
        const finishedModalBox = document.getElementById('finishedEventsReportModalBox');
        const finishedCloseBtn = document.getElementById('closeFinishedEventsReportModal');
        const finishedCloseBtnBtn = document.getElementById('closeFinishedEventsReportModalBtn');

        function openFinishedModal() {
            finishedModal.classList.remove('opacity-0', 'pointer-events-none');
            finishedModal.classList.add('opacity-100', 'pointer-events-auto');
            finishedModalBox.classList.remove('scale-95');
            finishedModalBox.classList.add('scale-100');
            document.body.style.overflow = 'hidden'; // Lock background scroll
        }

        function closeFinishedModal() {
            finishedModal.classList.add('opacity-0', 'pointer-events-none');
            finishedModal.classList.remove('opacity-100', 'pointer-events-auto');
            finishedModalBox.classList.add('scale-95');
            finishedModalBox.classList.remove('scale-100');
            document.body.style.overflow = ''; // Unlock background scroll
        }

        if (finishedEventsReportCard) finishedEventsReportCard.addEventListener('click', openFinishedModal);
        if (finishedCloseBtn) finishedCloseBtn.addEventListener('click', closeFinishedModal);
        if (finishedCloseBtnBtn) finishedCloseBtnBtn.addEventListener('click', closeFinishedModal);
        if (finishedBackdrop) finishedBackdrop.addEventListener('click', closeFinishedModal);

        // 3. Modal Finished Revenue Report
        const finishedRevenueReportCard = document.getElementById('finishedRevenueReportCard');
        const finishedRevenueModal = document.getElementById('finishedRevenueReportModal');
        const finishedRevenueBackdrop = document.getElementById('finishedRevenueReportModalBackdrop');
        const finishedRevenueModalBox = document.getElementById('finishedRevenueReportModalBox');
        const finishedRevenueCloseBtn = document.getElementById('closeFinishedRevenueReportModal');
        const finishedRevenueCloseBtnBtn = document.getElementById('closeFinishedRevenueReportModalBtn');

        function openFinishedRevenueModal() {
            finishedRevenueModal.classList.remove('opacity-0', 'pointer-events-none');
            finishedRevenueModal.classList.add('opacity-100', 'pointer-events-auto');
            finishedRevenueModalBox.classList.remove('scale-95');
            finishedRevenueModalBox.classList.add('scale-100');
            document.body.style.overflow = 'hidden'; // Lock background scroll
        }

        function closeFinishedRevenueModal() {
            finishedRevenueModal.classList.add('opacity-0', 'pointer-events-none');
            finishedRevenueModal.classList.remove('opacity-100', 'pointer-events-auto');
            finishedRevenueModalBox.classList.add('scale-95');
            finishedRevenueModalBox.classList.remove('scale-100');
            document.body.style.overflow = ''; // Unlock background scroll
        }

        if (finishedRevenueReportCard) finishedRevenueReportCard.addEventListener('click', openFinishedRevenueModal);
        if (finishedRevenueCloseBtn) finishedRevenueCloseBtn.addEventListener('click', closeFinishedRevenueModal);
        if (finishedRevenueCloseBtnBtn) finishedRevenueCloseBtnBtn.addEventListener('click', closeFinishedRevenueModal);
        if (finishedRevenueBackdrop) finishedRevenueBackdrop.addEventListener('click', closeFinishedRevenueModal);

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (modal && !modal.classList.contains('opacity-0')) {
                    closeModal();
                }
                if (finishedModal && !finishedModal.classList.contains('opacity-0')) {
                    closeFinishedModal();
                }
                if (finishedRevenueModal && !finishedRevenueModal.classList.contains('opacity-0')) {
                    closeFinishedRevenueModal();
                }
            }
        });

        // Realtime countdown timer inside modal
        function updateModalCountdowns() {
            const elements = document.querySelectorAll('[data-modal-countdown]');
            
            elements.forEach(el => {
                const endDateStr = el.getAttribute('data-modal-countdown');
                const endDate = new Date(endDateStr.replace(' ', 'T')).getTime();
                const now = new Date().getTime();
                const diff = endDate - now;
                
                if (isNaN(endDate) || diff <= 0) {
                    el.innerHTML = "Ended";
                    el.className = "text-slate-400 font-extrabold text-xs";
                } else {
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    
                    let text = "";
                    if (days > 0) {
                        text += `${days}d `;
                    }
                    text += `${hours.toString().padStart(2, '0')}h ${minutes.toString().padStart(2, '0')}m ${seconds.toString().padStart(2, '0')}s`;
                    el.innerHTML = text;
                }
            });
        }

        // Run countdown update
        updateModalCountdowns();
        setInterval(updateModalCountdowns, 1000);
    });
</script>

@endsection