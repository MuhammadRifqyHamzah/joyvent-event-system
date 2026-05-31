@extends('admin.layouts.app')

@section('title', $event->name . ' - Live Monitoring')

@section('content')

@php
    $statusColor = 'bg-green-50 text-green-600 border border-green-150';
    $gradientColor = 'from-green-600 to-emerald-600';

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

<div class="space-y-8 relative">
    
    <!-- Toast Notification for real-time check-ins -->
    <div id="checkInToast" class="fixed top-6 right-6 z-55 max-w-sm bg-slate-900/95 backdrop-blur text-white px-6 py-4 rounded-2xl shadow-2xl border border-slate-700/50 flex items-center gap-3 transition-all duration-300 transform translate-y-[-100px] opacity-0 pointer-events-none">
        <span class="text-2xl animate-bounce">⚡</span>
        <div>
            <h5 class="font-extrabold text-sm text-green-400">New Check-In!</h5>
            <p class="text-xs text-slate-300 mt-0.5" id="toastMessage">Rifqy Hamzah baru saja check-in.</p>
        </div>
    </div>

    {{-- Success Alert Notification --}}
    @if(session('success'))
        <div id="sessionSuccessAlert" class="bg-green-50 border border-green-100 text-green-600 px-6 py-4.5 rounded-2xl flex items-center gap-3 shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-green-600 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Top Action Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">Live Monitoring</h1>
                <span class="relative flex h-3.5 w-3.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-green-500"></span>
                </span>
                <span id="syncPulse" class="flex items-center gap-1 text-[10px] font-extrabold text-green-600 bg-green-50 px-2.5 py-1 rounded-full border border-green-150/40 uppercase tracking-wider animate-pulse">
                    LIVE SYNC ACTIVE
                </span>
            </div>
            <p class="text-gray-400 text-sm mt-1.5 font-semibold">Pusat monitoring realtime kehadiran, tempat duduk, dan undian peserta.</p>
        </div>
        <div class="flex items-center gap-3">
            <button id="manualSyncBtn" onclick="pollRealtimeStats(true)" 
                class="bg-white border border-gray-250 hover:bg-gray-50 text-gray-700 px-5 py-3 rounded-2xl font-bold text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                <svg id="syncIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>Sync Now</span>
            </button>
            <a href="{{ route('admin.events') }}" 
                class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-5 py-3.5 rounded-2xl font-bold text-sm shadow-sm transition flex items-center gap-2 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-gray-550">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    @include('admin.events.partials.header')

    <!-- Tab 1 Panel: Overview -->
    <div id="tab-panel-overview" class="tab-panel space-y-8">
        <!-- Quick Stats Cards Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Registered -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100/50 flex-shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0 1 10.089 20.4c-2.114 0-4.082-.54-5.8-1.485a4.125 4.125 0 0 1 7.533-2.493c.501.91.786 1.957.786 3.07v-.003m-2.225-7.61c.642.457 1.412.728 2.247.728 2.21 0 4-1.79 4-4s-1.79-4-4-4" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-extrabold uppercase tracking-wider block">Total Registrasi</span>
                    <span id="stat-tickets-sold" class="text-2xl font-black text-gray-800 block mt-1">{{ number_format($totalParticipants) }}</span>
                </div>
            </div>

            <!-- Card 2: Attended -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center border border-green-100/50 flex-shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-green-550 font-extrabold uppercase tracking-wider block">Total Check-In</span>
                    <span id="stat-attended" class="text-2xl font-black text-gray-800 block mt-1">{{ number_format($attended_count) }}</span>
                </div>
            </div>

            <!-- Card 3: Absent -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100/50 flex-shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-rose-500 font-extrabold uppercase tracking-wider block">Belum Check-In</span>
                    <span id="stat-absent" class="text-2xl font-black text-gray-800 block mt-1">{{ number_format($notAttendedCount) }}</span>
                </div>
            </div>

            <!-- Card 4: Attendance Rate -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100/50 flex-shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-indigo-500 font-extrabold uppercase tracking-wider block">Persentase Kehadiran</span>
                    <span id="stat-attendance-percentage" class="text-2xl font-black text-gray-800 block mt-1">{{ $attendancePercentage }}%</span>
                </div>
            </div>
        </div>

        <!-- Realtime Check-In Progress Bar Card -->
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">
                <div>
                    <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Realtime Attendance Progress</h3>
                    <p class="text-xs text-gray-400 font-semibold mt-1">Persentase kehadiran di lokasi secara langsung dari total kuota tiket terjual.</p>
                </div>
                <div class="text-right">
                    <span class="text-xl font-black text-green-600">
                        <span id="progress-attended">{{ $attended_count }}</span> <span class="text-gray-400 text-sm font-semibold">/ <span id="progress-total">{{ $totalParticipants }}</span> Hadir</span>
                    </span>
                    <span id="progress-percentage-pill" class="bg-green-50 text-green-600 px-2.5 py-1 rounded-lg text-xs font-black block mt-1 text-center w-full">
                        {{ $attendancePercentage }}%
                    </span>
                </div>
            </div>
            <!-- Progress bar -->
            <div class="w-full bg-gray-100 h-4 rounded-full overflow-hidden shadow-inner">
                <div id="stat-attendance-bar" class="bg-gradient-to-r from-green-500 to-emerald-500 h-full rounded-full transition-all duration-700" style="width: {{ min(100, $attendancePercentage) }}%"></div>
            </div>
        </div>

        <!-- Quick Actions Panel -->
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">⚡ Aksi Cepat Presensi</h3>
                <p class="text-xs text-gray-400 font-semibold mt-1">Pindai kode QR tiket peserta atau lakukan check-in secara manual di daftar peserta.</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap w-full md:w-auto">
                <button onclick="openQrScanner()" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-2xl font-bold text-xs shadow-sm hover:shadow transition flex items-center justify-center gap-2 cursor-pointer">
                    📷 <span>Tombol Scan QR</span>
                </button>
                <button onclick="focusParticipantSearch()" class="w-full sm:w-auto bg-white border border-gray-250 hover:bg-gray-50 text-gray-700 px-5 py-3 rounded-2xl font-bold text-xs shadow-sm transition flex items-center justify-center gap-2 cursor-pointer">
                    ✍️ <span>Tombol Check-In Manual</span>
                </button>
            </div>
        </div>

        @if($event->has_certificate)
            <!-- Certificate Generation section -->
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-10 space-y-6" id="certificateSection">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-5 border-b border-gray-100">
                    <div>
                        <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Auto Certificate Dispatch</h3>
                        <p class="text-xs text-gray-400 font-semibold mt-1">Terbitkan sertifikat keikutsertakaan secara massal bagi seluruh peserta yang hadir di lokasi.</p>
                    </div>
                    <form action="{{ route('admin.certificates.generate') }}" method="POST" class="flex items-center gap-3">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        
                        <button type="submit" @if($certCandidates->isEmpty()) disabled @endif
                            class="px-5 py-3 bg-blue-650 hover:bg-blue-700 text-white rounded-2xl font-bold text-xs shadow-sm transition disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed select-none flex items-center gap-2">
                            <span>Mass Issue Certificates 🚀</span>
                        </button>
                    </form>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left template preview -->
                    <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 flex flex-col justify-center items-center text-center">
                        <span class="text-xs font-bold text-gray-450 block uppercase tracking-wider mb-3">Template Certificate</span>
                        @if($templateUrl)
                            <div class="relative max-w-[280px] rounded-xl overflow-hidden shadow border border-gray-200">
                                <img src="{{ $templateUrl }}" alt="Template" class="w-full object-cover">
                            </div>
                        @else
                            <div class="w-48 h-32 rounded-xl bg-slate-200 flex items-center justify-center text-slate-450 font-semibold text-xs border border-dashed border-slate-350">
                                No template uploaded
                            </div>
                        @endif
                    </div>

                    <!-- Right statistics of certificates -->
                    <div class="lg:col-span-2 flex flex-col justify-between">
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Certificate Statistics</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 text-center">
                                    <span class="text-[10px] text-gray-400 font-extrabold uppercase tracking-wider">Certificates Issued</span>
                                    <span class="text-2xl font-black text-gray-800 block mt-1">{{ $certificates->count() }}</span>
                                </div>
                                <div class="bg-amber-50/40 border border-amber-100/30 rounded-2xl p-4 text-center">
                                    <span class="text-[10px] text-amber-500 font-extrabold uppercase tracking-wider">Pending Generation</span>
                                    <span id="certificate-pending" class="text-2xl font-black text-amber-600 block mt-1">{{ $certCandidates->count() }}</span>
                                </div>
                            </div>
                        </div>

                            <span>Title: <strong>{{ $event->certificate_title ?? '-' }}</strong></span>
                            <span>Organizer: <strong>{{ $event->organizer_name ?? '-' }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

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
                            <span class="text-2xl font-black text-green-600" id="stat-progress-percent">{{ $attendancePercentage }}%</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 h-4 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-full rounded-full transition-all duration-700" style="width: {{ $attendancePercentage }}%" id="stat-progress-bar"></div>
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
                            <span class="text-gray-450 block">Waktu Selesai:</span>
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

                <div class="my-6 flex-grow overflow-y-auto max-h-[350px] pr-2 space-y-5" id="recent-activities-container">
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
                    <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-widest block">Status Monitoring</span>
                    <span class="text-xs font-bold text-gray-700 block mt-1">Auto-refreshing statistics...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2 Panel: Participants -->
    <div id="tab-panel-participants" class="tab-panel hidden space-y-8">
        <!-- Quick Actions Panel -->
        @if(auth()->user()->role === 'admin')
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 space-y-6">
                <div class="pb-4 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Quick Actions Panel</h3>
                        <p class="text-xs text-gray-400 font-semibold mt-1">Lakukan check-in instan, scan tiket QR, atau reset presensi event.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- Scanner Trigger -->
                    <button onclick="openQrScanner()" class="p-6 bg-blue-50/40 hover:bg-blue-50 border border-blue-100/40 hover:border-blue-200 rounded-3xl flex items-center gap-4 text-left transition duration-300 cursor-pointer shadow-xs">
                        <div class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center text-lg shadow-md">
                            📷
                        </div>
                        <div>
                            <span class="font-extrabold text-sm text-gray-800 block">Scan Tiket QR</span>
                            <span class="text-[10px] text-gray-400 font-semibold block mt-0.5">Buka kamera scan presensi.</span>
                        </div>
                    </button>

                    <!-- Fast Check-in Search Modal / Trigger -->
                    <div class="p-6 bg-green-50/30 border border-green-100/30 rounded-3xl flex items-center gap-4 text-left shadow-xs">
                        <div class="w-12 h-12 bg-green-600 text-white rounded-2xl flex items-center justify-center text-lg shadow-md">
                            ⚡
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="font-extrabold text-sm text-gray-800 block">Fast Check-In</span>
                            <span class="text-[10px] text-gray-400 font-semibold block mt-0.5">Ketik di pencarian checklist bawah.</span>
                        </div>
                    </div>

                    <!-- Reset Attendants -->
                    <form action="{{ route('admin.participants.reset_check_in', $event->id) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin me-reset data kehadiran event ini?')" class="w-full p-6 bg-red-50/40 hover:bg-red-50 border border-red-100/40 hover:border-red-200 rounded-3xl flex items-center gap-4 text-left transition duration-300 cursor-pointer shadow-xs select-none">
                            <div class="w-12 h-12 bg-red-500 text-white rounded-2xl flex items-center justify-center text-lg shadow-md">
                                ↺
                            </div>
                            <div>
                                <span class="font-extrabold text-sm text-red-65 block">Reset Kehadiran</span>
                                <span class="text-[10px] text-gray-450 font-semibold block mt-0.5">Kosongkan data kehadiran event.</span>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Live check-in list -->
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-10 overflow-hidden">
            <div class="pb-6 border-b border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Live Participant Checklist</h3>
                    <p class="text-xs text-gray-400 font-semibold mt-1">Daftar presensi peserta. Lakukan check-in manual instan secara aman.</p>
                </div>
                
                <div class="relative w-60 h-[38px] flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input 
                        type="text" 
                        id="participantSearch" 
                        placeholder="Cari nama/email..." 
                        class="border border-gray-250 bg-gray-50/50 rounded-full pl-11 pr-4 w-full h-full focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-semibold text-gray-700 placeholder-gray-400"
                    >
                </div>
            </div>

            <div class="overflow-x-auto max-h-[550px] overflow-y-auto pr-2">
                @if($event->registrations->isEmpty())
                    <div class="text-center py-16 text-gray-400 font-bold text-sm">
                        No registrations for this event yet.
                    </div>
                @else
                    <table class="w-full">
                        <thead class="text-gray-400 uppercase text-xs font-bold tracking-wider border-b border-gray-105 pb-3">
                            <tr>
                                <th class="text-left pb-3">Peserta</th>
                                <th class="text-left pb-3 pl-4">Kelas Tiket</th>
                                <th class="text-left pb-3 pl-4">Status</th>
                                <th class="text-right pb-3 pr-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" id="participantTableBody">
                            @foreach($event->registrations as $reg)
                                <tr class="hover:bg-gray-50/30 transition duration-150 participant-row"
                                    data-id="{{ $reg->id }}"
                                    data-name="{{ strtolower($reg->user->name ?? '') }}"
                                    data-email="{{ strtolower($reg->user->email ?? '') }}">
                                    
                                    <td class="py-4 pr-4">
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-9 h-9 rounded-2xl bg-blue-50/50 text-blue-600 flex items-center justify-center font-black text-xs border border-blue-100/30 flex-shrink-0">
                                                {{ strtoupper(substr($reg->user->name ?? 'G', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-bold text-gray-800 text-sm block truncate max-w-[180px]">
                                                    {{ $reg->user->name ?? 'Guest' }}
                                                </span>
                                                <span class="text-[10px] text-gray-400 font-medium block truncate max-w-[180px] mt-0.5">
                                                    {{ $reg->user->email ?? '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-4 pl-4 pr-4">
                                        <span class="bg-indigo-50 text-indigo-600 border border-indigo-100/30 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap">
                                            {{ $reg->ticketCategory->name ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="py-4 pl-4 pr-4 checkin-status-cell">
                                        @if($reg->is_checked_in)
                                            <span class="bg-green-50 text-green-600 border border-green-150 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap block text-center max-w-[110px]">
                                                Hadir 🟢
                                            </span>
                                            <span class="text-[9px] text-gray-400 block font-semibold text-center max-w-[110px] mt-1">
                                                {{ \Carbon\Carbon::parse($reg->checked_in_at)->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="bg-slate-50 text-slate-400 border border-slate-200 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap block text-center max-w-[110px]">
                                                Absent ⏳
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-4 text-right pr-2 checkin-action-cell">
                                        <form action="{{ route('admin.participants.check_in', $reg->id) }}" method="POST" class="inline">
                                            @csrf
                                            @if($reg->is_checked_in)
                                                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-650 border border-red-100 px-3.5 py-2 rounded-xl text-xs font-bold transition cursor-pointer select-none">
                                                    Cancel
                                                </button>
                                            @else
                                                <button type="submit" class="bg-green-50 hover:bg-green-100 text-green-600 border border-green-150 px-3.5 py-2 rounded-xl text-xs font-bold transition cursor-pointer select-none">
                                                    Check-In
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <!-- Tab 5 Panel: Certificates -->
    @if($event->has_certificate)
    <div id="tab-panel-certificates" class="tab-panel hidden space-y-8">
        <!-- Certificates Generated section -->
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
    <div id="tab-panel-refunds" class="tab-panel hidden space-y-8">
        @include('admin.events.partials.refunds')
    </div>

    <!-- Tab 2 Panel: Monitor Kursi -->
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

        <div id="tab-panel-seats" class="tab-panel hidden space-y-8">
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
                    <span class="text-[10px] text-green-500 font-extrabold block uppercase tracking-wider font-semibold">Kursi Kosong</span>
                    <span class="text-2xl font-black text-green-600 block mt-1" id="seat-stat-available">{{ $availableSeatsCount }}</span>
                </div>
                <div class="bg-red-50/60 border border-red-100/40 rounded-3xl p-6 text-center">
                    <span class="text-[10px] text-red-500 font-extrabold block uppercase tracking-wider font-semibold">Kursi Diblokir</span>
                    <span class="text-2xl font-black text-red-655 block mt-1" id="seat-stat-blocked">{{ $blockedSeatsCount }}</span>
                </div>
                <div class="bg-indigo-50/60 border border-indigo-100/40 rounded-3xl p-6 text-center col-span-2 md:col-span-1">
                    <span class="text-[10px] text-indigo-500 font-extrabold block uppercase tracking-wider">Okupansi Rate</span>
                    <span class="text-2xl font-black text-indigo-650 block mt-1" id="seat-stat-rate">{{ $occupancyRate }}%</span>
                </div>
            </div>

            <!-- Seating Layout Map -->
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-10 space-y-8" id="seatSection">
                <div>
                    <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Realtime Seat Occupancy Map</h3>
                    <p class="text-gray-400 text-xs mt-1.5 font-semibold">
                        Peta denah visual keterisian tempat duduk peserta. Warna biru menandakan terisi, hijau kosong, dan merah diblokir.
                    </p>
                </div>

                <div class="flex flex-col gap-4 items-center justify-center p-8 bg-slate-50 border border-slate-100 rounded-[32px] overflow-auto select-none min-h-[300px]" id="seatMapContainer">
                    <!-- Panggung -->
                    <div class="w-full max-w-sm bg-slate-205 bg-slate-200 border border-slate-300/40 text-slate-500 text-[10px] font-bold py-2 rounded-lg text-center tracking-[4px] uppercase mb-12 shadow-sm">
                        STAGE / PANGGUNG UTAMA
                    </div>

                    <!-- Row Seats mapping -->
                    @foreach($groupedSeats as $rowLabel => $seats)
                        <div class="flex items-center gap-3">
                            <span class="w-8 text-right font-extrabold text-xs text-slate-450 uppercase select-none mr-2">
                                {{ $rowLabel }}
                            </span>
                            
                            <div class="flex items-center gap-2">
                                @foreach($seats as $seat)
                                    @php
                                        $status = $seat->status;
                                        $booking = $seatBookings->get($seat->seat_number);
                                        if ($status === 'booked') {
                                            $btnColor = 'bg-blue-50 border-blue-200 text-blue-600 hover:bg-blue-100/70 hover:border-blue-300';
                                        } elseif ($status === 'blocked') {
                                            $btnColor = 'bg-red-50 border-red-200 text-red-655 hover:bg-red-100/70 hover:border-red-300';
                                        } else {
                                            $btnColor = 'bg-green-50 border-green-200 text-green-600 hover:bg-green-100/70 hover:border-green-300';
                                        }
                                    @endphp
                                    <div class="relative group">
                                        <form action="{{ route('admin.seats.toggle_status', $seat->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                {{ $status === 'booked' ? 'disabled' : '' }}
                                                class="w-9 h-9 rounded-xl border flex items-center justify-center font-bold text-xs tracking-tight transition shadow-sm cursor-pointer disabled:cursor-not-allowed select-none {{ $btnColor }}">
                                                {{ $seat->column }}
                                            </button>
                                        </form>
                                        
                                        @if($status === 'booked' && $booking && $booking->user)
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 hidden group-hover:block z-30 w-52 bg-slate-900/95 backdrop-blur text-white p-3 rounded-2xl shadow-xl border border-slate-700/50 text-center">
                                                <div class="font-extrabold text-xs truncate leading-snug">{{ $booking->user->name }}</div>
                                                <div class="text-[9px] text-slate-400 mt-1 truncate">{{ $booking->user->email }}</div>
                                                <div class="inline-block mt-2 bg-blue-500/20 text-blue-400 border border-blue-400/30 text-[8px] px-2.5 py-0.5 rounded-full font-bold uppercase">
                                                    {{ $booking->ticketCategory->name ?? 'Ticket' }}
                                                </div>
                                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-t-[6px] border-t-slate-900/95"></div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <!-- Pintu masuk -->
                    <div class="text-[9px] text-slate-400 font-extrabold uppercase tracking-[2px] mt-8">
                        🚪 ENTRANCE
                    </div>
                </div>

                <div class="flex items-center justify-center gap-8 pt-4 border-t border-gray-50 flex-wrap">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-md bg-green-50 border border-green-200"></div>
                        <span class="text-xs font-bold text-gray-500">Kosong</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-md bg-blue-50 border border-blue-200"></div>
                        <span class="text-xs font-bold text-gray-500">Terisi</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-md bg-red-50 border border-red-200"></div>
                        <span class="text-xs font-bold text-gray-500">Diblokir</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab 3 Panel: Undian Berhadiah -->
    @if($event->has_lucky_draw)
        <div id="tab-panel-lucky_draw" class="tab-panel hidden space-y-8">
            <!-- Cyberpunk Neon Lucky Draw Slot Machine Section -->
            <div class="bg-slate-950 rounded-[32px] border border-slate-800 shadow-2xl p-10 text-center relative overflow-hidden min-h-[420px] flex flex-col justify-between" id="luckyDrawSection">
                <canvas id="confettiCanvas" class="absolute inset-0 w-full h-full pointer-events-none z-50 rounded-[32px]"></canvas>
                <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px] pointer-events-none"></div>
                <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-pink-500/10 rounded-full blur-[100px] pointer-events-none"></div>

                <div class="z-10">
                    <span class="inline-block bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-[10px] px-3.5 py-1 rounded-full font-bold uppercase tracking-widest">
                        JoyVent Live Raffle 🎰
                    </span>
                    <h3 class="text-2xl font-extrabold text-white tracking-tight mt-2">Undian Hadiah Kehadiran</h3>
                </div>

                <div class="my-6 z-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Slot display -->
                    <div class="lg:col-span-2 bg-slate-900 border-2 border-indigo-500/30 rounded-[32px] p-8 shadow-2xl shadow-indigo-500/5 relative flex flex-col justify-center min-h-[220px]">
                        <div class="absolute inset-0 bg-gradient-to-b from-indigo-500/5 to-transparent rounded-[32px] pointer-events-none"></div>
                        <div id="slotDisplay" class="text-3xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 tracking-wide uppercase select-none min-h-[60px] flex items-center justify-center">
                            READY TO DRAW 🎰
                        </div>
                        <div id="slotSubDisplay" class="text-slate-400 text-xs mt-3 font-semibold min-h-[16px]">
                            Masukkan hadiah, lalu putar undian
                        </div>
                    </div>

                    <!-- Right winners history inside slot -->
                    <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-6 text-left flex flex-col justify-between min-h-[220px]">
                        <div>
                            <span class="text-[9px] text-slate-500 font-extrabold block uppercase tracking-widest">Riwayat Pemenang</span>
                            <div class="mt-3 overflow-y-auto max-h-[120px] space-y-2 pr-1" id="winnersListContainer">
                                @forelse($winners as $win)
                                    <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-3 flex items-center justify-between gap-2 group">
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-xs text-slate-200 truncate">
                                                {{ $win->registration->user->name }}
                                            </div>
                                            <div class="text-[9px] font-semibold text-indigo-400 mt-0.5 truncate">
                                                🎁 {{ $win->prize_name }}
                                            </div>
                                        </div>
                                        <form action="{{ route('admin.lucky_draw.destroy', $win->id) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition flex-shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 hover:bg-red-950 hover:text-red-400 text-slate-500 rounded cursor-pointer transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="py-6 text-center text-slate-500 text-[10px] font-bold uppercase tracking-wide">
                                        Belum Ada Pemenang
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Candidates Count / Total Eligible -->
                        <div class="mt-4 pt-3 border-t border-slate-800 flex justify-between items-center text-[10px] font-bold text-slate-400">
                            <span>Total Peserta Eligible:</span>
                            <span id="lucky-draw-candidates" class="text-indigo-400 font-extrabold text-sm">{{ $candidates->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="z-10 max-w-md mx-auto w-full space-y-4">
                    <div class="flex gap-4">
                        <input 
                            type="text" 
                            id="prizeInput" 
                            value="{{ $event->prize_name ?? 'Merchandise Eksklusif' }}" 
                            placeholder="Hadiah Undian..." 
                            class="w-full bg-slate-900 border border-slate-800 rounded-xl px-5 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-bold text-white placeholder-slate-600"
                        >
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        @if($candidates->isEmpty())
                            <button id="drawBtn" disabled class="w-full bg-slate-800 text-slate-500 font-extrabold py-3.5 px-6 rounded-2xl text-xs tracking-widest uppercase cursor-not-allowed select-none shadow-md">
                                Tidak Ada Kandidat (Harus Hadir) 💡
                            </button>
                        @else
                            <button id="drawBtn" onclick="startLuckyDraw()" class="w-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 hover:scale-[1.01] hover:shadow-indigo-500/20 hover:shadow-lg text-white font-extrabold py-3.5 px-6 rounded-2xl text-xs tracking-widest uppercase transition duration-300 cursor-pointer shadow-md">
                                Tombol Mulai Undian 🎰
                            </button>
                        @endif
                        
                        <button id="redrawBtn" onclick="triggerRedraw()" @if($winners->isEmpty()) disabled @endif class="w-full sm:w-48 bg-slate-900 border border-slate-800 hover:bg-slate-800 hover:border-slate-700 disabled:opacity-50 text-white font-extrabold py-3.5 px-6 rounded-2xl text-xs tracking-widest uppercase transition duration-300 cursor-pointer shadow-md disabled:cursor-not-allowed">
                            Tombol Undi Ulang 🔄
                        </button>
                    </div>
                </div>
            </div>

            {{-- Winner celebration Modal --}}
            <div id="winnerModal" class="fixed inset-0 z-55 hidden flex items-center justify-center px-4 bg-slate-950/70 backdrop-blur-sm transition">
                <div class="bg-white rounded-[32px] p-10 max-w-md w-full border border-gray-150 text-center shadow-2xl relative overflow-hidden transform scale-95 transition-transform duration-300">
                    <div class="w-20 h-20 bg-amber-50 border border-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-4xl animate-pulse">👑</span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 tracking-tight">Selamat Pemenang!</h3>
                    <p class="text-gray-400 text-xs font-semibold mt-1 uppercase tracking-widest" id="modalPrizeName">HADIAH</p>
                    
                    <div class="my-6 bg-slate-50 border border-slate-100 rounded-2xl p-6 max-w-full overflow-hidden">
                        <div class="text-xl sm:text-2xl font-black text-indigo-650" style="word-break: break-word; overflow-wrap: break-word;" id="modalWinnerName">Nama Pemenang</div>
                        <div class="text-[10px] text-slate-450 mt-1.5 font-semibold" style="word-break: break-all; overflow-wrap: break-word;" id="modalWinnerEmail">email@pemenang.com</div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button onclick="closeWinnerModal()" class="w-full bg-slate-100 hover:bg-slate-200 text-gray-700 font-extrabold py-3.5 px-6 rounded-2xl text-xs tracking-wider uppercase transition cursor-pointer shadow-sm">
                            Tutup
                        </button>
                        <button onclick="modalRedraw()" class="w-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white font-extrabold py-3.5 px-6 rounded-2xl text-xs tracking-wider uppercase transition cursor-pointer shadow-sm">
                            Undi Ulang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- QR Scanner Modal -->
    <div id="qrScannerModal" class="fixed inset-0 z-55 hidden flex items-center justify-center px-4 bg-slate-950/70 backdrop-blur-sm transition">
        <div class="bg-white rounded-[32px] p-8 max-w-md w-full border border-gray-150 shadow-2xl relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-gray-800 tracking-tight flex items-center gap-2">
                    <span>📷</span> Scan QR Code Check-In
                </h3>
                <button onclick="closeQrScannerModal()" class="text-gray-400 hover:text-gray-600 bg-slate-100 hover:bg-slate-200 p-1.5 rounded-full transition cursor-pointer flex items-center justify-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="bg-slate-55 bg-slate-50 border border-slate-100 rounded-2xl overflow-hidden p-2 flex flex-col items-center justify-center">
                <!-- QR Scanner camera viewport -->
                <div id="qr-reader" class="w-full rounded-xl overflow-hidden shadow-inner border border-slate-200" style="min-height: 250px;"></div>
                <div id="qr-reader-results" class="text-xs text-gray-455 font-bold mt-4 text-center px-4 leading-relaxed">
                    Dekatkan QR code tiket ke kamera untuk memindai check-in secara otomatis.
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button id="cancelQrBtn" class="w-full bg-slate-100 hover:bg-slate-200 text-gray-700 font-extrabold py-3.5 px-6 rounded-2xl text-xs tracking-wider uppercase transition cursor-pointer shadow-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Confetti Canvas logic & shuffler, real-time polling -->
<script src="https://unpkg.com/html5-qrcode"></script>
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

    // QR Code scanner logic
    let html5QrcodeScanner = null;

    function openQrScanner() {
        const modal = document.getElementById('qrScannerModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        // Reset instructions text
        const resultsEl = document.getElementById('qr-reader-results');
        if (resultsEl) {
            resultsEl.innerHTML = "Dekatkan QR code tiket ke kamera untuk memindai check-in secara otomatis.";
        }

        // Start scanner
        if (typeof Html5Qrcode !== 'undefined') {
            html5QrcodeScanner = new Html5Qrcode("qr-reader");
            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                // Stop scanning immediately to prevent multiple scans
                closeQrScannerModal();
                // Process the QR code checkin
                processQrCheckIn(decodedText);
            };
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            // Start scanning with environment/back camera preferred
            html5QrcodeScanner.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
                .catch(err => {
                    console.error("Camera access error:", err);
                    const resultsElErr = document.getElementById('qr-reader-results');
                    if (resultsElErr) {
                        resultsElErr.innerHTML = `<span class="text-red-500 font-bold">Kamera gagal diakses. Pastikan izin kamera aktif.</span>`;
                    }
                });
        } else {
            alert("Library Scanner belum termuat. Silakan tunggu sebentar.");
        }
    }

    function closeQrScannerModal() {
        const stopPromise = (html5QrcodeScanner && html5QrcodeScanner.isScanning)
            ? html5QrcodeScanner.stop()
            : Promise.resolve();

        stopPromise.then(() => {
            // Stop any stray tracks from video element to avoid camera remaining active
            try {
                const videoElem = document.querySelector('#qr-reader video');
                if (videoElem && videoElem.srcObject) {
                    const stream = videoElem.srcObject;
                    const tracks = stream.getTracks();
                    tracks.forEach(track => track.stop());
                    videoElem.srcObject = null;
                }
            } catch (e) {
                console.error("Error stopping tracks manually:", e);
            }

            if (html5QrcodeScanner) {
                try {
                    html5QrcodeScanner.clear();
                } catch (e) {
                    console.error("Error clearing scanner instance:", e);
                }
                html5QrcodeScanner = null;
            }

            const modal = document.getElementById('qrScannerModal');
            if (modal) {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }).catch(err => {
            console.error("Error during closing scanner:", err);
            // Fallback: hide modal anyway
            const modal = document.getElementById('qrScannerModal');
            if (modal) {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        });
    }

    function processQrCheckIn(qrCode) {
        const checkinUrl = "{{ route('admin.events.check_in_qr', $event->id) }}";
        
        // Show scanning indicator/toast
        showCheckInToast("Memproses scan...");

        fetch(checkinUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                qr_code: qrCode
            })
        })
        .then(response => response.json())
        .then(res => {
            closeQrScannerModal();
            if (res.success) {
                // Trigger check-in toast with user name
                showCheckInToast(res.registration.name + " berhasil Check-In! 🏆");
                // Reload stats immediately
                pollRealtimeStats(true);
            } else {
                alert(res.message || "Gagal melakukan check-in via QR.");
            }
        })
        .catch(err => {
            console.error("QR Check-in error:", err);
            closeQrScannerModal();
            alert("Terjadi kesalahan koneksi saat melakukan check-in.");
        });
    }

    // Smooth scroll manual check-in focus
    function focusParticipantSearch() {
        const searchInput = document.getElementById('participantSearch');
        if (searchInput) {
            searchInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => {
                searchInput.focus();
                searchInput.classList.add('ring-4', 'ring-blue-500/30');
                setTimeout(() => {
                    searchInput.classList.remove('ring-4', 'ring-blue-500/30');
                }, 1500);
            }, 800);
        }
    }

    // Trigger redraw (undian ulang)
    function triggerRedraw() {
        const container = document.getElementById('winnersListContainer');
        const firstWinnerForm = container ? container.querySelector('form') : null;
        
        if (!firstWinnerForm) {
            alert('Tidak ada pemenang untuk diundi ulang!');
            return;
        }

        if (confirm('Apakah Anda yakin ingin membatalkan pemenang terakhir dan mengundi ulang?')) {
            const actionUrl = firstWinnerForm.getAttribute('action');
            
            fetch(actionUrl, {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: new FormData(firstWinnerForm) // contains DELETE method override & csrf
            })
            .then(() => {
                pollRealtimeStats(true);
                showCheckInToast("Pemenang dibatalkan, siap undi ulang. 🔄");
            })
            .catch(err => {
                console.error("Redraw error:", err);
                alert("Gagal membatalkan pemenang.");
            });
        }
    }
    // Config shuffler variables (if active)
    let candidatesList = @json($candidates->map(fn($c) => [
        'registration_id' => $c->id,
        'name' => $c->user->name,
        'email' => $c->user->email
    ]));

    let luckyDrawState = 'idle'; // 'idle', 'drawing', 'completed'
    let shuffleInterval = null;
    let confettiTimer = null;

    // Polling intervals & last checked check-in counts
    let lastAttendedCount = {{ $attended_count }};
    const statsUrl = "{{ route('admin.events.ongoing.stats', $event->id) }}";

    function startLuckyDraw() {
        if (luckyDrawState === 'drawing' || candidatesList.length === 0) return;

        const prizeName = document.getElementById('prizeInput').value.trim();
        if (!prizeName) {
            alert('Silakan masukkan nama hadiah terlebih dahulu!');
            return;
        }

        luckyDrawState = 'drawing';
        
        // Disable draw buttons & actions to prevent double clicks/conflicts
        const drawBtn = document.getElementById('drawBtn');
        const redrawBtn = document.getElementById('redrawBtn');
        if (drawBtn) {
            drawBtn.disabled = true;
            drawBtn.innerText = 'DRAWING...';
        }
        if (redrawBtn) {
            redrawBtn.disabled = true;
        }

        const display = document.getElementById('slotDisplay');
        const subDisplay = document.getElementById('slotSubDisplay');
        
        let counter = 0;
        
        subDisplay.innerText = 'Mengacak nama kandidat...';

        // Clear any old intervals
        if (shuffleInterval) {
            clearTimeout(shuffleInterval);
            shuffleInterval = null;
        }

        function shuffle() {
            if (candidatesList.length === 0) return;
            const randomIndex = Math.floor(Math.random() * candidatesList.length);
            const candidate = candidatesList[randomIndex];
            
            display.innerText = candidate.name;
            
            counter++;
            // Deceleration rolling animation
            if (counter < 30) {
                shuffleInterval = setTimeout(shuffle, 50);
            } else if (counter < 40) {
                shuffleInterval = setTimeout(shuffle, 150);
            } else if (counter < 45) {
                shuffleInterval = setTimeout(shuffle, 350);
            } else if (counter < 48) {
                shuffleInterval = setTimeout(shuffle, 650);
            } else {
                const finalWinner = candidatesList[randomIndex];
                saveWinner(finalWinner, prizeName);
            }
        }
        
        shuffle();
    }

    function saveWinner(winner, prizeName) {
        fetch("{{ route('admin.lucky_draw.draw') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                event_id: "{{ $event->id }}",
                registration_id: winner.registration_id,
                prize_name: prizeName
            })
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                // Change status to completed and lock/unlock buttons appropriately
                luckyDrawState = 'completed';
                
                const drawBtn = document.getElementById('drawBtn');
                const subDisplay = document.getElementById('slotSubDisplay');
                if (drawBtn) {
                    drawBtn.disabled = false;
                    drawBtn.innerText = 'SELESAI';
                }
                if (subDisplay) {
                    subDisplay.innerHTML = "🏆 Pemenang Ditemukan! <span class='text-indigo-400 font-extrabold'>" + winner.name + "</span>";
                }

                document.getElementById('modalWinnerName').innerText = winner.name;
                document.getElementById('modalWinnerEmail').innerText = winner.email;
                document.getElementById('modalPrizeName').innerText = prizeName.toUpperCase();
                
                const modal = document.getElementById('winnerModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
                
                startConfettiEffect();
            } else {
                alert('Gagal mencatat pemenang undian!');
                resetDrawBtn();
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan!');
            resetDrawBtn();
        });
    }

    function resetDrawBtn() {
        luckyDrawState = 'idle';
        const drawBtn = document.getElementById('drawBtn');
        const redrawBtn = document.getElementById('redrawBtn');
        if (drawBtn) {
            drawBtn.disabled = candidatesList.length === 0;
            drawBtn.innerText = 'PUTAR UNDIAN 🎰';
        }
        if (redrawBtn) {
            redrawBtn.disabled = false; // re-enable check
        }
    }

    function closeWinnerModal() {
        const modal = document.getElementById('winnerModal');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
        
        stopConfettiEffect();
        pollRealtimeStats(true); // force poll update to reload candidates & winners cleanly
        resetDrawBtn();
    }

    // Modal redraw logic
    function modalRedraw() {
        // Stop confetti and close winner modal first
        closeWinnerModal();

        const container = document.getElementById('winnersListContainer');
        const firstWinnerForm = container ? container.querySelector('form') : null;
        
        if (firstWinnerForm) {
            const actionUrl = firstWinnerForm.getAttribute('action');
            
            fetch(actionUrl, {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: new FormData(firstWinnerForm) // contains DELETE method override & csrf
            })
            .then(() => {
                pollRealtimeStats(true);
                showCheckInToast("Pemenang dibatalkan. Mengundi ulang secara otomatis... 🔄");
                
                // Automatically trigger a redraw after state sync
                setTimeout(() => {
                    startLuckyDraw();
                }, 1000);
            })
            .catch(err => {
                console.error("Modal redraw error:", err);
                alert("Gagal membatalkan pemenang.");
            });
        } else {
            alert('Tidak ada pemenang untuk diundi ulang!');
        }
    }

    // Canvas Confetti
    const canvas = document.getElementById('confettiCanvas');
    let ctx = null;
    let particles = [];
    let animationFrame = null;

    function startConfettiEffect() {
        if (!canvas) return;
        ctx = canvas.getContext('2d');
        canvas.width = canvas.parentElement.clientWidth;
        canvas.height = canvas.parentElement.clientHeight;
        
        const colors = ['#6366f1', '#a855f7', '#ec4899', '#3b82f6', '#10b981', '#f59e0b'];
        
        for (let i = 0; i < 80; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height - canvas.height,
                r: Math.random() * 5 + 3,
                d: Math.random() * canvas.height,
                color: colors[Math.floor(Math.random() * colors.length)],
                tilt: Math.random() * 10 - 5,
                tiltAngleIncremental: Math.random() * 0.07 + 0.02,
                tiltAngle: 0
            });
        }

        function drawConfetti() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            particles.forEach((p, index) => {
                p.tiltAngle += p.tiltAngleIncremental;
                p.y += (Math.cos(p.d) + 3 + p.r / 2) / 2;
                p.tilt = Math.sin(p.tiltAngle) * 15;

                if (p.y > canvas.height) {
                    particles[index] = {
                        x: Math.random() * canvas.width,
                        y: -20,
                        r: p.r,
                        d: p.d,
                        color: p.color,
                        tilt: p.tilt,
                        tiltAngleIncremental: p.tiltAngleIncremental,
                        tiltAngle: p.tiltAngle
                    };
                }

                ctx.beginPath();
                ctx.lineWidth = p.r;
                ctx.strokeStyle = p.color;
                ctx.moveTo(p.x + p.tilt + p.r / 2, p.y);
                ctx.lineTo(p.x + p.tilt, p.y + p.tilt + p.r / 2);
                ctx.stroke();
            });

            animationFrame = requestAnimationFrame(drawConfetti);
        }
        
        drawConfetti();

        // Auto-stop confetti after 4 seconds to prevent performance drop and memory leak
        if (confettiTimer) clearTimeout(confettiTimer);
        confettiTimer = setTimeout(() => {
            stopConfettiEffect();
        }, 4000);
    }

    function stopConfettiEffect() {
        if (animationFrame) {
            cancelAnimationFrame(animationFrame);
            animationFrame = null;
        }
        if (confettiTimer) {
            clearTimeout(confettiTimer);
            confettiTimer = null;
        }
        if (ctx && canvas) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
        particles = [];
    }

    // Realtime AJAX Polling
    function pollRealtimeStats(isForce = false) {
        const syncIcon = document.getElementById('syncIcon');
        if (syncIcon) {
            syncIcon.classList.add('animate-spin');
        }

        fetch(statsUrl)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update stats counters
                    document.getElementById('stat-tickets-sold').innerText = parseInt(data.ticketsSold).toLocaleString();
                    document.getElementById('stat-attended').innerText = parseInt(data.attended_count).toLocaleString();
                    document.getElementById('stat-absent').innerText = parseInt(data.notAttendedCount).toLocaleString();
                    document.getElementById('stat-attendance-percentage').innerText = data.attendancePercentage + '%';
                    
                    // Update progress bar
                    document.getElementById('progress-attended').innerText = data.attended_count;
                    document.getElementById('progress-total').innerText = data.ticketsSold;
                    
                    const progressPill = document.getElementById('progress-percentage-pill');
                    if (progressPill) {
                        progressPill.innerText = data.attendancePercentage + '%';
                    }

                    const attendanceBar = document.getElementById('stat-attendance-bar');
                    if (attendanceBar) {
                        attendanceBar.style.width = Math.min(100, data.attendancePercentage) + '%';
                    }

                    // Update Lucky Draw candidates count and list
                    const luckyDrawCandidates = document.getElementById('lucky-draw-candidates');
                    if (luckyDrawCandidates) {
                        luckyDrawCandidates.innerText = data.candidatesCount;
                    }
                    
                    if (document.getElementById('certificate-pending')) {
                        document.getElementById('certificate-pending').innerText = data.pendingCertificates;
                    }

                    // If candidates changed or forced, sync local candidates list for raffle
                    if (data.candidatesCount !== candidatesList.length || isForce) {
                        // Filter checked in participants who haven't won
                        const winnersIds = data.winners.map(w => parseInt(w.registration_id || 0));
                        candidatesList = data.participants
                            .filter(p => p.is_checked_in && !winnersIds.includes(p.id))
                            .map(p => ({
                                registration_id: p.id,
                                name: p.name,
                                email: p.email
                            }));

                        const drawBtn = document.getElementById('drawBtn');
                        if (drawBtn && !isDrawing) {
                            if (candidatesList.length === 0) {
                                drawBtn.disabled = true;
                                drawBtn.className = "w-full bg-slate-800 text-slate-500 font-extrabold py-3.5 px-6 rounded-2xl text-xs tracking-widest uppercase cursor-not-allowed select-none shadow-md";
                                drawBtn.innerText = "Tidak Ada Kandidat (Harus Hadir & Belum Menang) 💡";
                            } else {
                                drawBtn.disabled = false;
                                drawBtn.className = "w-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 hover:scale-[1.01] hover:shadow-indigo-500/20 hover:shadow-lg text-white font-extrabold py-3.5 px-6 rounded-2xl text-xs tracking-widest uppercase transition duration-300 cursor-pointer shadow-md";
                                drawBtn.innerText = "PUTAR UNDIAN 🎰";
                            }
                        }
                    }

                    // Sync Winners UI
                    const winnersContainer = document.getElementById('winnersListContainer');
                    if (winnersContainer) {
                        if (data.winners.length === 0) {
                            winnersContainer.innerHTML = `<div class="py-6 text-center text-slate-500 text-[10px] font-bold uppercase tracking-wide">No Winners yet</div>`;
                        } else {
                            let winnersHtml = '';
                            data.winners.forEach(w => {
                                winnersHtml += `
                                    <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-3 flex items-center justify-between gap-2 group">
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-xs text-slate-200 truncate">
                                                ${escapeHtml(w.name)}
                                            </div>
                                            <div class="text-[9px] font-semibold text-indigo-400 mt-0.5 truncate">
                                                🎁 ${escapeHtml(w.prize_name)}
                                            </div>
                                        </div>
                                        <form action="${w.destroy_url}" method="POST" class="opacity-0 group-hover:opacity-100 transition flex-shrink-0">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="p-1 hover:bg-red-950 hover:text-red-400 text-slate-500 rounded cursor-pointer transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                `;
                            });
                            winnersContainer.innerHTML = winnersHtml;
                        }
                    }

                    // Update redraw button state
                    const redrawBtn = document.getElementById('redrawBtn');
                    if (redrawBtn) {
                        redrawBtn.disabled = data.winners.length === 0;
                    }

                    // Toast and Notification if Attended count increased
                    if (data.attended_count > lastAttendedCount && !isForce) {
                        // Find newly checked in participant to show toast
                        const diff = data.attended_count - lastAttendedCount;
                        // Display toast
                        const newlyCheckedIn = data.participants
                            .filter(p => p.is_checked_in)
                            .sort((a,b) => new Date(b.checked_in_at) - new Date(a.checked_in_at))[0];
                        
                        if (newlyCheckedIn) {
                            showCheckInToast(newlyCheckedIn.name);
                        }
                    }
                    lastAttendedCount = data.attended_count;

                    // Sync Participant table statuses and actions
                    syncParticipantTable(data.participants);

                    // Sync Refunds stats count cards
                    if (document.getElementById('stat-refunds-pending')) {
                        document.getElementById('stat-refunds-pending').innerText = parseInt(data.pendingRefundsCount).toLocaleString();
                    }
                    if (document.getElementById('stat-refunds-approved')) {
                        document.getElementById('stat-refunds-approved').innerText = parseInt(data.approvedRefundsCount).toLocaleString();
                    }
                    if (document.getElementById('stat-refunds-rejected')) {
                        document.getElementById('stat-refunds-rejected').innerText = parseInt(data.rejectedRefundsCount).toLocaleString();
                    }

                    // Sync Refunds table rows
                    const refundTableBody = document.getElementById('refundTableBody');
                    if (refundTableBody && data.refunds) {
                        if (data.refunds.length === 0) {
                            refundTableBody.innerHTML = `
                                <tr>
                                    <td colspan="6" class="text-center py-16 text-gray-400 font-bold text-sm">
                                        No refund requests found.
                                    </td>
                                </tr>
                            `;
                        } else {
                            let refundsHtml = '';
                            data.refunds.forEach(r => {
                                let badgeHtml = '';
                                if (r.status === 'pending') {
                                    badgeHtml = `<span class="bg-amber-50 text-amber-600 border border-amber-100 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap">Pending ⏳</span>`;
                                } else if (r.status === 'approved') {
                                    badgeHtml = `<span class="bg-green-50 text-green-600 border border-green-150 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap">Approved 🟢</span>`;
                                } else {
                                    badgeHtml = `<span class="bg-rose-50 text-rose-600 border border-rose-100 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap">Rejected 🔴</span>`;
                                }

                                refundsHtml += `
                                    <tr class="hover:bg-gray-50/30 transition duration-150 refund-row" data-status="${r.status}" data-id="${r.id}">
                                        <td class="py-4 pr-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-2xl bg-slate-50 border border-slate-100/50 flex items-center justify-center font-black text-xs text-slate-600">
                                                    ${escapeHtml(r.participant_name.substring(0, 1).toUpperCase())}
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-gray-800 text-sm block truncate max-w-[150px]">
                                                        ${escapeHtml(r.participant_name)}
                                                    </span>
                                                    <span class="text-[10px] text-gray-400 font-medium block truncate max-w-[150px] mt-0.5">
                                                        ${escapeHtml(r.participant_email)}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 pl-4 pr-4">
                                            <span class="bg-indigo-50 text-indigo-600 border border-indigo-100/30 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap">
                                                ${escapeHtml(r.ticket_type)}
                                            </span>
                                        </td>
                                        <td class="py-4 pl-4 pr-4">
                                            <span class="text-gray-700 text-xs font-semibold block max-w-[200px] truncate">
                                                ${escapeHtml(r.reason)}
                                            </span>
                                        </td>
                                        <td class="py-4 pl-4 pr-4">
                                            <span class="text-gray-400 text-xs font-semibold whitespace-nowrap">
                                                ${escapeHtml(r.request_date.split(',')[0])}
                                            </span>
                                        </td>
                                        <td class="py-4 pl-4 pr-4 refund-status-badge-cell">
                                            ${badgeHtml}
                                        </td>
                                        <td class="py-4 text-right pr-2">
                                            <button onclick="viewRefundDetails(${escapeHtml(JSON.stringify(r))})" class="bg-blue-50 hover:bg-blue-100/80 text-blue-600 border border-blue-100 px-3.5 py-2 rounded-xl text-xs font-bold transition cursor-pointer select-none">
                                                View Detail
                                            </button>
                                        </td>
                                    </tr>
                                `;
                            });
                            refundTableBody.innerHTML = refundsHtml;

                            // Re-apply filter
                            const currentActiveFilterBtn = document.querySelector('.refund-filter-btn.text-blue-600');
                            if (currentActiveFilterBtn) {
                                const activeFilter = currentActiveFilterBtn.id.replace('refund-filter-', '');
                                filterRefunds(activeFilter);
                            }
                        }
                    }

                    // Sync Recent Activity feed
                    const activityContainer = document.getElementById('recent-activities-container');
                    if (activityContainer) {
                        if (data.recentActivities.length === 0) {
                            activityContainer.innerHTML = `<div class="h-full flex flex-col items-center justify-center text-center py-16 text-gray-400 font-semibold text-sm">Belum ada aktivitas terekam.</div>`;
                        } else {
                            let activityHtml = '';
                            data.recentActivities.forEach(act => {
                                const isGreen = act.bg_color.includes('green') || act.text_color.includes('green');
                                const iconSvg = isGreen 
                                    ? `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4.5 h-4.5 text-green-600"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>`
                                    : `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4.5 h-4.5 text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>`;
                                
                                activityHtml += `
                                    <div class="flex gap-3.5 items-center">
                                        <div class="w-10 h-10 rounded-full ${act.bg_color} flex-shrink-0 flex items-center justify-center text-lg shadow-sm border border-gray-100/20">
                                            ${iconSvg}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-gray-700 text-xs leading-relaxed">
                                                ${act.title}
                                            </p>
                                            <span class="text-[9px] text-gray-400 font-semibold mt-1 block">${act.time}</span>
                                        </div>
                                    </div>
                                `;
                            });
                            activityContainer.innerHTML = activityHtml;
                        }
                    }
                }
            })
            .catch(err => console.error('Realtime Sync error:', err))
            .finally(() => {
                if (syncIcon) {
                    setTimeout(() => syncIcon.classList.remove('animate-spin'), 600);
                }
            });
    }

    function syncParticipantTable(participants) {
        const tbody = document.getElementById('participantTableBody');
        if (!tbody) return;

        participants.forEach(p => {
            const tr = tbody.querySelector(`tr[data-id="${p.id}"]`);
            if (tr) {
                // Update checkin status cell
                const statusCell = tr.querySelector('.checkin-status-cell');
                if (statusCell) {
                    if (p.is_checked_in) {
                        const cleanTime = p.checked_in_at ? p.checked_in_at.split(', ')[1] : '';
                        statusCell.innerHTML = `
                            <span class="bg-green-50 text-green-600 border border-green-150 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap block text-center max-w-[110px]">
                                Hadir 🟢
                            </span>
                            <span class="text-[9px] text-gray-400 block font-semibold text-center max-w-[110px] mt-1">
                                ${cleanTime}
                            </span>
                        `;
                    } else {
                        statusCell.innerHTML = `
                            <span class="bg-slate-50 text-slate-400 border border-slate-200 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap block text-center max-w-[110px]">
                                Absent ⏳
                            </span>
                        `;
                    }
                }

                // Update checkin action cell
                const actionCell = tr.querySelector('.checkin-action-cell');
                if (actionCell) {
                    const btnClass = p.is_checked_in 
                        ? 'bg-red-50 hover:bg-red-100 text-red-650 border border-red-100 px-3.5 py-2 rounded-xl text-xs font-bold transition cursor-pointer select-none' 
                        : 'bg-green-50 hover:bg-green-100 text-green-600 border border-green-150 px-3.5 py-2 rounded-xl text-xs font-bold transition cursor-pointer select-none';
                    const btnText = p.is_checked_in ? 'Cancel' : 'Check-In';
                    
                    actionCell.innerHTML = `
                        <form action="${p.toggle_url}" method="POST" class="inline">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button type="submit" class="${btnClass}">
                                ${btnText}
                            </button>
                        </form>
                    `;
                }
            }
        });
    }

    function showCheckInToast(name) {
        const toast = document.getElementById('checkInToast');
        const msg = document.getElementById('toastMessage');
        if (toast && msg) {
            msg.innerText = `${name} baru saja check-in.`;
            toast.classList.remove('translate-y-[-100px]', 'opacity-0', 'pointer-events-none');
            toast.classList.add('translate-y-0', 'opacity-100');
            
            setTimeout(() => {
                toast.classList.add('translate-y-[-100px]', 'opacity-0', 'pointer-events-none');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 4000);
        }
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Auto-poll stats every 10 seconds
    setInterval(() => pollRealtimeStats(false), 10000);

    // Countdown Timer logic
    function updateCountdown() {
        const el = document.getElementById('countdownTimer');
        if (!el) return;

        const endTimeStr = el.getAttribute('data-end-time');
        const endDate = new Date(endTimeStr.replace(' ', 'T')).getTime();
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
    }

    setInterval(updateCountdown, 1000);
    updateCountdown();

    // Client-side search logic
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

        // Attach event listeners for QR Scanner Modal cancel and backdrop click
        try {
            const cancelBtn = document.getElementById('cancelQrBtn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', closeQrScannerModal);
            } else {
                console.error("Tombol BATAL (#cancelQrBtn) tidak ditemukan di DOM!");
            }
        } catch (err) {
            console.error("Gagal memasang event listener pada tombol BATAL:", err);
        }

        try {
            const backdrop = document.getElementById('qrScannerModal');
            if (backdrop) {
                backdrop.addEventListener('click', (e) => {
                    if (e.target === backdrop) {
                        closeQrScannerModal();
                    }
                });
            } else {
                console.error("Backdrop modal (#qrScannerModal) tidak ditemukan di DOM!");
            }
        } catch (err) {
            console.error("Gagal memasang event listener pada backdrop modal:", err);
        }
    });
</script>

@endsection
