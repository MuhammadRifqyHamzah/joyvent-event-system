@extends('admin.layouts.app')

@section('title', $event->name . ' - Finished Details')

@section('content')

@php
    $statusColor = 'bg-slate-100 text-slate-700 border border-slate-200/60';
    $gradientColor = 'from-slate-600 to-slate-800';

    // Dynamic Gradient Category Cover
    switch($event->category) {
        case 'Entertainment':
            $coverGradient = 'from-purple-600 to-indigo-600';
            break;
        case 'Education':
            $coverGradient = 'from-blue-600 to-cyan-600';
            break;
        case 'Sports':
            $coverGradient = 'from-emerald-600 to-teal-600';
            break;
        case 'Business':
            $coverGradient = 'from-amber-600 to-orange-600';
            break;
        case 'Community':
            $coverGradient = 'from-rose-600 to-red-600';
            break;
        default:
            $coverGradient = $gradientColor;
    }
@endphp

<div class="space-y-8">

    <!-- Top Action Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">Event Overview (Finished)</h1>
            <p class="text-gray-400 text-sm mt-1.5 font-semibold">Review laporan final kehadiran dan aktivitas event JoyVent Anda.</p>
        </div>
        <a href="{{ route('admin.events') }}" 
            class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-5 py-3.5 rounded-2xl font-bold text-sm shadow-sm transition flex items-center gap-2 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-gray-550">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    @include('admin.events.partials.header')

    <!-- Tab 1 Panel: Overview -->
    <div id="tab-panel-overview" class="tab-panel space-y-8 mt-6">
        <!-- Quick Stats Cards Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Card 1: Total Registrasi -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-655 flex items-center justify-center border border-slate-100/50 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477" />
                    </svg>
                </div>
                <div>
                    <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider block">Total Registrasi</span>
                    <span class="text-xl font-black text-gray-800 block mt-1">{{ number_format($totalParticipants) }}</span>
                </div>
            </div>

            <!-- Card 2: Total Hadir -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center border border-green-100/50 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3" />
                    </svg>
                </div>
                <div>
                    <span class="text-[9px] text-green-550 font-extrabold uppercase tracking-wider block">Total Hadir</span>
                    <span class="text-xl font-black text-gray-800 block mt-1">{{ number_format($attended_count) }}</span>
                </div>
            </div>

            <!-- Card 3: Persentase Kehadiran -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100/50 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                    </svg>
                </div>
                <div>
                    <span class="text-[9px] text-blue-550 font-extrabold uppercase tracking-wider block">Persentase Kehadiran</span>
                    <span class="text-xl font-black text-gray-800 block mt-1">{{ $attendancePercentage }}%</span>
                </div>
            </div>

            <!-- Card 4: Total Tiket Terjual -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-655 flex items-center justify-center border border-red-100/50 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-12v.75m0 3v.75m0 3v.75m0 3V18M3 8.25a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 8.25V15.75a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15.75V8.25Z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[9px] text-red-550 font-extrabold uppercase tracking-wider block">Total Tiket Terjual</span>
                    <span class="text-xl font-black text-gray-800 block mt-1">{{ number_format($ticketsSold) }}</span>
                </div>
            </div>

            <!-- Card 5: Total Pendapatan -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100/50 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5M3 18.75h18" />
                    </svg>
                </div>
                <div>
                    <span class="text-[9px] text-amber-500 font-extrabold uppercase tracking-wider block">Total Pendapatan</span>
                    <span class="text-sm font-black text-gray-800 block mt-1">Rp {{ number_format($totalRevenue) }}</span>
                </div>
            </div>
        </div>

        <!-- 2 Columns Layout for Progress & Activity Log -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <!-- Attendance Progress Card -->
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Kehadiran Peserta</h3>
                            <span class="text-xs text-gray-400 font-semibold">Rasio peserta check-in terhadap total registrasi.</span>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-black text-green-600">{{ $attendancePercentage }}%</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 h-4 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-full rounded-full transition-all duration-700" style="width: {{ $attendancePercentage }}%"></div>
                    </div>
                </div>

                <!-- Event Details card -->
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 space-y-4">
                    <h3 class="text-lg font-bold text-gray-800">Detail Pelaksanaan Event</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-semibold">
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <span class="text-gray-450 block">Waktu Mulai:</span>
                            <span class="text-gray-700 mt-1 block font-bold">{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }} - {{ $event->start_time }}</span>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <span class="text-gray-455 block">Waktu Selesai:</span>
                            <span class="text-gray-700 mt-1 block font-bold">{{ \Carbon\Carbon::parse($event->end_date)->format('d M Y') }} - {{ $event->end_time }}</span>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <span class="text-gray-450 block">Lokasi:</span>
                            <span class="text-gray-700 mt-1 block font-bold">{{ $event->location }}</span>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <span class="text-gray-450 block">Kategori:</span>
                            <span class="text-gray-700 mt-1 block font-bold">{{ $event->category }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Log Column -->
            <div class="lg:col-span-1 bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 flex flex-col justify-between min-h-[450px]">
                <div>
                    <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Recent Activity Log</h3>
                    <p class="text-gray-400 text-xs mt-1.5 font-semibold leading-relaxed">Aktivitas presensi dan transaksi realtime untuk event ini.</p>
                </div>

                <div class="my-6 flex-grow overflow-y-auto max-h-[350px] pr-2 space-y-5">
                    @forelse($recentActivities as $act)
                        <div class="flex gap-3.5 items-center">
                            <div class="w-10 h-10 rounded-full {{ $act['bg_color'] }} flex-shrink-0 flex items-center justify-center text-lg shadow-sm border border-gray-100/20">
                                @if(str_contains($act['bg_color'], 'green') || str_contains($act['text_color'], 'green'))
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4.5 h-4.5 text-green-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4.5 h-4.5 text-blue-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-700 text-xs leading-relaxed">
                                    {!! $act['title'] !!}
                                </p>
                                <span class="text-[9px] text-gray-400 font-semibold mt-1 block">{{ $act['time'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center py-16 text-gray-400 font-semibold text-sm">
                            Belum ada aktivitas terekam.
                        </div>
                    @endforelse
                </div>
                
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 text-center">
                    <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-widest block">Status Event</span>
                    <span class="text-xs font-bold text-gray-700 block mt-1">Event Selesai Dilaksanakan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2 Panel: Participants -->
    <div id="tab-panel-participants" class="tab-panel hidden space-y-8 mt-6">
        <!-- Complete Attendance List -->
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-10 overflow-hidden">
            <div class="pb-6 border-b border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Final Attendance List</h3>
                    <p class="text-xs text-gray-400 font-semibold mt-1.5">Laporan status kehadiran akhir seluruh peserta.</p>
                </div>

                <!-- Client side search -->
                <div class="relative w-64 h-[38px] flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input 
                        type="text" 
                        id="participantSearch" 
                        placeholder="Cari peserta..." 
                        class="border border-gray-250 bg-gray-50/50 rounded-full pl-11 pr-4 w-full h-full focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-semibold text-gray-700 placeholder-gray-400"
                    >
                </div>
            </div>

            <div class="overflow-x-auto">
                @if($event->registrations->isEmpty())
                    <div class="text-center py-16 text-gray-400 font-bold text-sm">
                        Belum ada peserta terdaftar.
                    </div>
                @else
                    <table class="w-full">
                        <thead class="text-gray-400 uppercase text-xs font-bold tracking-wider border-b border-gray-100 pb-4">
                            <tr>
                                <th class="text-left pb-4">Peserta</th>
                                <th class="text-left pb-4 pl-4">Email</th>
                                <th class="text-left pb-4 pl-4">Kelas Tiket</th>
                                <th class="text-left pb-4 pl-4">Status Kehadiran</th>
                                <th class="text-left pb-4">Check-In Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" id="participantTableBody">
                            @foreach($event->registrations as $reg)
                                <tr class="hover:bg-gray-50/30 transition duration-150 participant-row"
                                    data-name="{{ strtolower($reg->user->name ?? '') }}"
                                    data-email="{{ strtolower($reg->user->email ?? '') }}">
                                    <td class="py-4 pr-4">
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-9 h-9 rounded-2xl bg-blue-50/50 text-blue-600 flex items-center justify-center font-black text-sm border border-blue-100/30">
                                                {{ strtoupper(substr($reg->user->name ?? 'G', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-bold text-gray-800 text-sm block truncate max-w-[200px]">
                                                    {{ $reg->user->name ?? 'Guest' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 pl-4 pr-4">
                                        <span class="text-gray-655 font-bold text-xs">
                                            {{ $reg->user->email ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-4 pl-4 pr-4">
                                        <span class="bg-indigo-50 text-indigo-600 border border-indigo-100/30 px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap">
                                            {{ $reg->ticketCategory->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-4 pl-4 pr-4">
                                        @if($reg->is_checked_in)
                                            <span class="bg-green-50 text-green-600 border border-green-150 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap">
                                                Hadir
                                            </span>
                                        @else
                                            <span class="bg-red-50/70 text-red-500 border border-red-100/40 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap">
                                                Absent
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4">
                                        <span class="text-gray-400 font-bold text-xs">
                                            {{ $reg->checked_in_at ? \Carbon\Carbon::parse($reg->checked_in_at)->format('d M Y, H:i') : '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <!-- Tab 3 Panel: Seats -->
    @if($event->has_seat_layout)
        @php
            $totalSeatsCount = 0;
            $bookedSeatsCount = 0;
            $availableSeatsCount = 0;
            $blockedSeatsCount = 0;
            if (isset($groupedSeats)) {
                $flatSeats = $groupedSeats->flatten();
                $totalSeatsCount = $flatSeats->count();
                $bookedSeatsCount = $flatSeats->where('status', 'booked')->count();
                $availableSeatsCount = $flatSeats->where('status', 'available')->count();
                $blockedSeatsCount = $flatSeats->where('status', 'blocked')->count();
            }
            $occupancyRate = $totalSeatsCount > 0 ? round(($bookedSeatsCount / $totalSeatsCount) * 100, 1) : 0;
        @endphp

        <div id="tab-panel-seats" class="tab-panel hidden space-y-8 mt-6">
            <!-- Seating Occupancy Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                <div class="bg-white border border-gray-100 shadow-sm rounded-3xl p-6 text-center">
                    <span class="text-[10px] text-gray-400 font-extrabold block uppercase tracking-wider">Total Kursi</span>
                    <span class="text-2xl font-black text-gray-800 block mt-1" id="seat-stat-total">{{ $totalSeatsCount }}</span>
                </div>
                <div class="bg-blue-50/60 border border-blue-100/40 rounded-3xl p-6 text-center">
                    <span class="text-[10px] text-blue-500 font-extrabold block uppercase tracking-wider font-semibold">Kursi Terisi</span>
                    <span class="text-2xl font-black text-blue-600 block mt-1" id="seat-stat-booked">{{ $bookedSeatsCount }}</span>
                </div>
                <div class="bg-green-50/60 border border-green-100/40 rounded-3xl p-6 text-center">
                    <span class="text-[10px] text-green-555 font-extrabold block uppercase tracking-wider font-semibold">Kursi Kosong</span>
                    <span class="text-2xl font-black text-green-600 block mt-1" id="seat-stat-available">{{ $availableSeatsCount }}</span>
                </div>
                <div class="bg-red-50/60 border border-red-100/40 rounded-3xl p-6 text-center">
                    <span class="text-[10px] text-red-500 font-extrabold block uppercase tracking-wider font-semibold">Kursi Diblok</span>
                    <span class="text-2xl font-black text-red-655 block mt-1" id="seat-stat-blocked">{{ $blockedSeatsCount }}</span>
                </div>
                <div class="bg-indigo-50/60 border border-indigo-100/40 rounded-3xl p-6 text-center col-span-2 md:col-span-1">
                    <span class="text-[10px] text-indigo-500 font-extrabold block uppercase tracking-wider font-semibold">Okupansi</span>
                    <span class="text-2xl font-black text-indigo-600 block mt-1" id="seat-stat-rate">{{ $occupancyRate }}%</span>
                </div>
            </div>

            <!-- Seat Layout Grid Box -->
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-10 space-y-8">
                <div class="text-center max-w-md mx-auto space-y-2">
                    <div class="w-full h-2.5 bg-gray-100 rounded-full shadow-inner relative overflow-hidden">
                        <div class="absolute inset-0 bg-blue-500 transition-all duration-500" style="width: 100%"></div>
                    </div>
                    <span class="text-[10px] text-gray-400 font-extrabold tracking-widest uppercase block">Panggung Utama / Screen</span>
                </div>

                <div class="overflow-x-auto py-4">
                    <div class="min-w-[650px] flex flex-col gap-3">
                        @foreach($groupedSeats as $rowLetter => $rowSeats)
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center font-extrabold text-gray-400 text-xs shadow-xs">
                                    {{ $rowLetter }}
                                </div>
                                <div class="flex-1 flex gap-2 justify-between">
                                    @foreach($rowSeats as $seat)
                                        @php
                                            $booking = $seatBookings[$seat->seat_number] ?? null;
                                            
                                            $seatColor = 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200';
                                            if ($seat->status === 'booked') {
                                                $seatColor = 'bg-blue-600 text-white border-blue-600';
                                            } elseif ($seat->status === 'blocked') {
                                                $seatColor = 'bg-red-500 text-white border-red-500';
                                            }
                                        @endphp
                                        <div 
                                            class="w-10 h-10 rounded-xl border flex flex-col items-center justify-center font-bold text-[10px] transition duration-200 relative group cursor-pointer shadow-xs select-none {{ $seatColor }}"
                                            title="Kursi {{ $seat->seat_number }}"
                                        >
                                            <span>{{ $seat->column }}</span>
                                            
                                            @if($booking)
                                                <div class="absolute bottom-full mb-2 hidden group-hover:block bg-slate-900 text-white text-[9px] font-bold py-2 px-3.5 rounded-xl shadow-lg z-50 whitespace-nowrap leading-relaxed pointer-events-none">
                                                    <span class="block">Kursi: {{ $seat->seat_number }}</span>
                                                    <span class="block font-black text-[10px] mt-0.5 text-blue-300">{{ $booking->user->name ?? 'Guest' }}</span>
                                                    <span class="block text-[8px] text-gray-400 font-semibold mt-0.5">{{ $booking->ticketCategory->name ?? '-' }}</span>
                                                </div>
                                            @elseif($seat->status === 'blocked')
                                                <div class="absolute bottom-full mb-2 hidden group-hover:block bg-red-900 text-white text-[9px] font-bold py-2 px-3.5 rounded-xl shadow-lg z-50 whitespace-nowrap leading-relaxed pointer-events-none">
                                                    <span>Kursi Dihalang / Diblok</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-gray-50 pt-6 flex flex-wrap justify-center gap-6 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-md bg-slate-50 border border-slate-200 block"></span>
                        <span>Tersedia</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-md bg-blue-600 block"></span>
                        <span>Terisi</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-md bg-red-500 block"></span>
                        <span>Diblok</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab 4 Panel: Lucky Draw Winners -->
    @if($event->has_lucky_draw)
        <div id="tab-panel-lucky_draw" class="tab-panel hidden space-y-8 mt-6">
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 space-y-6">
                <div class="pb-5 border-b border-gray-100">
                    <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Lucky Draw Winners 🏆</h3>
                    <p class="text-xs text-gray-400 font-semibold mt-1">Pemenang undian berhadiah pada event ini.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @forelse($winners as $win)
                        <div class="bg-indigo-50/50 border border-indigo-100/40 rounded-3xl p-5 flex items-center gap-4">
                            <div class="text-3xl">🎁</div>
                            <div class="min-w-0">
                                <h4 class="font-extrabold text-gray-800 text-sm truncate">
                                    {{ $win->registration->user->name ?? 'Guest' }}
                                </h4>
                                <p class="text-xs text-indigo-600 font-bold mt-0.5 truncate">
                                    {{ $win->prize_name }}
                                </p>
                                <span class="text-[9px] text-gray-400 font-semibold block mt-1">
                                    Won at {{ \Carbon\Carbon::parse($win->won_at)->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-4 text-center py-8 bg-slate-50 border border-dashed border-slate-200 rounded-3xl text-gray-400 font-bold text-sm">
                            Tidak ada pemenang undian terekam.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- Tab 5 Panel: Issued Certificates -->
    @if($event->has_certificate)
        <div id="tab-panel-certificates" class="tab-panel hidden space-y-8 mt-6">
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 space-y-6">
                <div class="pb-5 border-b border-gray-100">
                    <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Issued Certificates 🏆</h3>
                    <p class="text-xs text-gray-400 font-semibold mt-1">Daftar sertifikat keikutsertakaan yang telah diterbitkan.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($certificates as $cert)
                        <div class="bg-emerald-50/30 border border-emerald-100/45 rounded-3xl p-5 flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <h4 class="font-extrabold text-gray-800 text-sm truncate">
                                    {{ $cert->registration->user->name ?? 'Guest' }}
                                </h4>
                                <p class="text-[10px] text-gray-450 mt-1 font-semibold truncate">Code: {{ $cert->certificate_code }}</p>
                                <span class="text-[9px] text-gray-400 font-semibold block mt-1">
                                    Issued {{ $cert->created_at->format('d M Y, H:i') }}
                                </span>
                            </div>
                            <div>
                                <span class="bg-green-50 text-green-600 border border-green-150 px-2 py-1 rounded-lg text-[9px] font-black uppercase">
                                    Valid
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8 bg-slate-50 border border-dashed border-slate-200 rounded-3xl text-gray-400 font-bold text-sm">
                            Tidak ada sertifikat diterbitkan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- Tab 6 Panel: Refunds -->
    <div id="tab-panel-refunds" class="tab-panel hidden space-y-8 mt-6">
        @include('admin.events.partials.refunds')
    </div>

</div>

<script>
    // Tab switching logic
    function switchTab(tabName) {
        // Hide all panels
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.add('hidden');
        });
        // Show target panel
        const targetPanel = document.getElementById(`tab-panel-${tabName}`);
        if (targetPanel) {
            targetPanel.classList.remove('hidden');
        }

        // Toggle button active state
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-blue-50', 'text-blue-600', 'font-extrabold', 'shadow-xs');
            btn.classList.add('text-gray-500', 'hover:bg-gray-50', 'hover:text-gray-800', 'font-bold');
        });

        const activeBtn = document.getElementById(`tab-btn-${tabName}`);
        if (activeBtn) {
            activeBtn.classList.add('bg-blue-50', 'text-blue-600', 'shadow-xs', 'font-extrabold');
            activeBtn.classList.remove('text-gray-500', 'hover:bg-gray-50', 'hover:text-gray-800', 'font-bold');
        }

        // Update URL query parameter without page reload
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url);
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Tab initialization on load
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            const activeBtn = document.getElementById(`tab-btn-${tab}`);
            if (activeBtn) {
                switchTab(tab);
            } else {
                switchTab('overview');
            }
        } else {
            switchTab('overview');
        }

        // Search logic
        const searchInput = document.getElementById('participantSearch');
        const rows = document.querySelectorAll('.participant-row');

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                rows.forEach(row => {
                    const name = row.getAttribute('data-name');
                    const email = row.getAttribute('data-email');
                    if (name.includes(query) || email.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>

@endsection
