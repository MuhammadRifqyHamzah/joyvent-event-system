@extends('admin.layouts.app')

@section('title', $event->name . ' - Upcoming Details')

@section('content')

@php
    $statusColor = 'bg-blue-50 text-blue-600 border border-blue-100/50';
    $gradientColor = 'from-blue-600 to-indigo-600';

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

    {{-- Success Alert Notification --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4.5 rounded-2xl flex items-center gap-3 shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-green-600 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Top Action Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">Event Overview (Upcoming)</h1>
            <p class="text-gray-400 text-sm mt-1.5 font-semibold">Kelola dan monitor persiapan event JoyVent Anda.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('admin.events.edit', $event->id) }}" 
                class="bg-white border border-gray-250 hover:bg-gray-50 text-gray-700 px-5 py-3 rounded-2xl font-bold text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                <span>Edit Event</span>
            </a>
            <a href="{{ route('admin.tickets.index', $event->id) }}" 
                class="bg-blue-650 hover:bg-blue-700 text-white px-5 py-3 rounded-2xl font-bold text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                <span>Manage Tickets</span>
            </a>
            <a href="{{ route('admin.events.features', $event->id) }}" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-2xl font-bold text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                <span>Setup Features</span>
            </a>
            <a href="{{ route('admin.events') }}" 
                class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-5 py-3 rounded-2xl font-bold text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-550">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

@php
    $prepSteps = [];
    
    // Step 1: Ticket categories
    $ticketCategoriesCount = $event->ticketCategories->count();
    $prepSteps[] = [
        'name' => 'Kategori Tiket Dibuat',
        'desc' => 'Menentukan kategori dan harga tiket masuk.',
        'status' => $ticketCategoriesCount > 0
    ];

    // Step 2: Seat layout configuration
    if($event->has_seat_layout) {
        $seatsCount = \App\Models\Seat::where('event_id', $event->id)->count();
        $prepSteps[] = [
            'name' => 'Denah Tempat Duduk',
            'desc' => 'Men-generate layout peta kursi penonton.',
            'status' => $seatsCount > 0
        ];
    }

    // Step 3: Certificate template upload
    if($event->has_certificate) {
        $hasTemplate = false;
        $templateDirectory = public_path('storage/certificates/templates');
        if (\Illuminate\Support\Facades\File::exists($templateDirectory)) {
            $files = \Illuminate\Support\Facades\File::files($templateDirectory);
            foreach ($files as $file) {
                if (str_starts_with($file->getFilename(), 'template_' . $event->id . '.')) {
                    $hasTemplate = true;
                    break;
                }
            }
        }
        $prepSteps[] = [
            'name' => 'Desain Template Sertifikat',
            'desc' => 'Mengunggah template sertifikat digital peserta.',
            'status' => $hasTemplate
        ];
    }

    // Calculate percentage
    $completedSteps = collect($prepSteps)->where('status', true)->count();
    $totalSteps = count($prepSteps);
    $prepProgress = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 100;
@endphp

    @include('admin.events.partials.header')

    <!-- Tab 1 Panel: Overview -->
    <div id="tab-panel-overview" class="tab-panel space-y-8 mt-6">
        <!-- Countdown to Event Day -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-[32px] p-8 text-white shadow-lg flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="space-y-2 text-center md:text-left">
                <span class="inline-block bg-white/20 backdrop-blur px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border border-white/20">Preparation Phase</span>
                <h3 class="text-2xl font-black">Event Dimulai Dalam:</h3>
                <p class="text-white/80 text-xs font-semibold">Harap pastikan semua logistik dan kelengkapan data peserta siap sebelum acara dimulai.</p>
            </div>
            <div class="flex items-center gap-4 text-center" id="eventStartCountdown" data-start-time="{{ $event->start_date }} {{ $event->start_time }}">
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 min-w-[70px]">
                    <span class="block text-2xl font-black" id="countdown-days">00</span>
                    <span class="block text-[10px] uppercase font-bold text-white/70">Hari</span>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 min-w-[70px]">
                    <span class="block text-2xl font-black" id="countdown-hours">00</span>
                    <span class="block text-[10px] uppercase font-bold text-white/70">Jam</span>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 min-w-[70px]">
                    <span class="block text-2xl font-black" id="countdown-minutes">00</span>
                    <span class="block text-[10px] uppercase font-bold text-white/70">Menit</span>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 min-w-[70px]">
                    <span class="block text-2xl font-black" id="countdown-seconds">00</span>
                    <span class="block text-[10px] uppercase font-bold text-white/70">Detik</span>
                </div>
            </div>
        </div>

        <!-- Quick Stats Cards Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Capacity -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-600 flex items-center justify-center border border-slate-100/50 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.079-13.123c0-2.293 1.726-4.143 3.978-4.143 2.252 0 3.978 1.85 3.978 4.143M8.962 10.743c.153-.027.31-.043.47-.043h4.137c.16 0 .317.016.47.043m-9.71-4.82c-.53.47-1.012 1.028-1.428 1.662m0 0a10.79 10.79 0 0 0 3.76 2.304m-3.76-2.304c.15-.226.31-.447.48-.667m9.94 4.632c.53.47 1.012 1.028 1.428 1.662m0 0a10.79 10.79 0 0 1-3.76 2.304m3.76-2.304c-.15-.226-.31-.447-.48-.667" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-extrabold uppercase tracking-wider block">Total Kapasitas</span>
                    <span class="text-2xl font-black text-gray-800 block mt-1">{{ number_format($capacity) }}</span>
                </div>
            </div>

            <!-- Card 2: Sold -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100/50 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-12v.75m0 3v.75m0 3v.75m0 3V18M3 8.25a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 8.25V15.75a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15.75V8.25Z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-blue-500 font-extrabold uppercase tracking-wider block">Tiket Terjual</span>
                    <span class="text-2xl font-black text-gray-800 block mt-1">{{ number_format($ticketsSold) }}</span>
                </div>
            </div>

            <!-- Card 3: Remaining -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100/50 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-green-550 font-extrabold uppercase tracking-wider block">Sisa Kursi</span>
                    <span class="text-2xl font-black text-gray-800 block mt-1">{{ number_format($remainingSeats) }}</span>
                </div>
            </div>

            <!-- Card 4: Revenue -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100/50 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5M3 18.75h18M3.75 4.5v14.25m16.5-14.25v14.25" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] text-amber-550 font-extrabold uppercase tracking-wider block">Total Pendapatan</span>
                    <span class="text-2xl font-black text-gray-800 block mt-1">Rp {{ number_format($totalRevenue) }}</span>
                </div>
            </div>
        </div>

        <!-- Ticket Sales Progress Card -->
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">
                <div>
                    <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Ticket Sales Progress</h3>
                    <p class="text-xs text-gray-400 font-semibold mt-1">Status pemenuhan kuota tiket terjual dari total kapasitas event.</p>
                </div>
                <div class="text-right">
                    <span class="text-xl font-black text-blue-600">
                        {{ $ticketsSold }} <span class="text-gray-400 text-sm font-semibold">/ {{ $capacity }} Terjual</span>
                    </span>
                    <span class="bg-blue-50 text-blue-600 px-2.5 py-1 rounded-lg text-xs font-black block mt-1 text-center w-full">
                        {{ $soldPercentage }}%
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-100 h-4 rounded-full overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-500 h-full rounded-full transition-all duration-700" style="width: {{ min(100, $soldPercentage) }}%"></div>
            </div>
        </div>

        <!-- Ticket Categories List -->
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 space-y-6">
            <div class="pb-5 border-b border-gray-100">
                <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Ticket Categories</h3>
                <p class="text-xs text-gray-400 font-semibold mt-1">Daftar kategori tiket beserta rincian status penjualan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($event->ticketCategories as $category)
                    @php
                        $catSold = $soldCounts[$category->id] ?? 0;
                        $catRemaining = max(0, $category->quota - $catSold);
                    @endphp
                    <div class="bg-slate-50/50 border border-slate-100 rounded-3xl p-6 flex flex-col justify-between hover:border-blue-100 hover:bg-white hover:shadow-md transition duration-300">
                        <div class="space-y-4">
                            <div class="flex justify-between items-start gap-2">
                                <h4 class="font-extrabold text-gray-800 text-lg leading-snug truncate">
                                    {{ $category->name }}
                                </h4>
                                <span class="bg-blue-50 text-blue-600 border border-blue-100/30 px-3 py-1 rounded-xl text-xs font-black whitespace-nowrap">
                                    Rp {{ number_format($category->price) }}
                                </span>
                            </div>
                            @if($category->description)
                                <p class="text-xs text-gray-400 font-medium line-clamp-2 leading-relaxed">
                                    {{ $category->description }}
                                </p>
                            @endif
                        </div>
                        <div class="mt-6 pt-5 border-t border-slate-100 grid grid-cols-3 gap-2 text-center">
                            <div>
                                <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider block">Quota</span>
                                <span class="text-sm font-bold text-gray-700 block mt-1">{{ number_format($category->quota) }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] text-blue-500 font-extrabold uppercase tracking-wider block">Sold</span>
                                <span class="text-sm font-bold text-blue-600 block mt-1">{{ number_format($catSold) }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] {{ $catRemaining > 0 ? 'text-green-500' : 'text-rose-500' }} font-extrabold uppercase tracking-wider block">Left</span>
                                <span class="text-sm font-bold {{ $catRemaining > 0 ? 'text-green-600' : 'text-rose-500' }} block mt-1">{{ number_format($catRemaining) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-10 bg-slate-50 border border-dashed border-slate-200 rounded-3xl text-gray-400 font-bold text-sm">
                        Belum ada kategori tiket terdaftar untuk event ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Event Preparation Progress -->
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 space-y-6">
            <div>
                <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Progress Persiapan Event</h3>
                <p class="text-xs text-gray-400 font-semibold mt-1">Daftar kesiapan konfigurasi fitur JoyVent sebelum pelaksanaan event.</p>
            </div>

            <div class="flex flex-col md:flex-row gap-8 items-center">
                <!-- Circular Progress SVG -->
                <div class="w-full md:w-1/3 flex flex-col items-center text-center space-y-3">
                    <div class="relative w-36 h-36 flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="72" cy="72" r="60" stroke="#f1f5f9" stroke-width="12" fill="transparent"/>
                            <circle cx="72" cy="72" r="60" stroke="#2563eb" stroke-width="12" fill="transparent"
                                    stroke-dasharray="377"
                                    stroke-dashoffset="{{ 377 - (377 * $prepProgress / 100) }}"
                                    class="transition-all duration-1000"/>
                        </svg>
                        <div class="absolute text-center">
                            <span class="block text-3xl font-black text-slate-800">{{ $prepProgress }}%</span>
                            <span class="text-[9px] uppercase font-bold text-gray-400">Siap</span>
                        </div>
                    </div>
                </div>

                <!-- Steps list -->
                <div class="w-full md:w-2/3 space-y-4">
                    @foreach($prepSteps as $step)
                        <div class="flex items-start gap-4 p-4 rounded-2xl border {{ $step['status'] ? 'bg-green-50/20 border-green-100/50' : 'bg-slate-50/50 border-slate-100' }} transition">
                            <span class="text-xl leading-none">{{ $step['status'] ? '✅' : '⏳' }}</span>
                            <div>
                                <h4 class="font-extrabold text-sm {{ $step['status'] ? 'text-green-800' : 'text-slate-700' }}">{{ $step['name'] }}</h4>
                                <p class="text-xs text-gray-400 font-semibold mt-0.5">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2 Panel: Participants -->
    <div id="tab-panel-participants" class="tab-panel hidden space-y-8 mt-6">
        <!-- Registered Participants List -->
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-10 overflow-hidden">
            <div class="pb-6 border-b border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Registered Participants</h3>
                    <p class="text-xs text-gray-400 font-semibold mt-1.5">Daftar lengkap peserta yang telah terdaftar pada event ini.</p>
                </div>
                
                <div class="relative w-64 h-[38px] flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input 
                        type="text" 
                        id="participantSearch" 
                        placeholder="Cari nama/email..." 
                        class="border border-gray-255 bg-gray-50/50 rounded-full pl-11 pr-4 w-full h-full focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-semibold text-gray-700 placeholder-gray-400"
                    >
                </div>
            </div>

            <div class="overflow-x-auto">
                @if($event->registrations->isEmpty())
                    <div class="text-center py-16 text-gray-400 font-bold text-sm">
                        Belum ada pembelian tiket.
                    </div>
                @else
                    <table class="w-full">
                        <thead class="text-gray-400 uppercase text-xs font-bold tracking-wider border-b border-gray-100 pb-4">
                            <tr>
                                <th class="text-left pb-4">Peserta</th>
                                <th class="text-left pb-4 pl-4">Email</th>
                                <th class="text-left pb-4 pl-4">Kelas Tiket</th>
                                <th class="text-left pb-4">Tanggal Pembelian</th>
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
                                    <td class="py-4">
                                        <span class="text-gray-400 font-bold text-xs">
                                            {{ $reg->created_at->format('d M Y, H:i') }}
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
                    <span class="text-[10px] text-green-550 font-extrabold block uppercase tracking-wider font-semibold">Kursi Kosong</span>
                    <span class="text-2xl font-black text-green-600 block mt-1" id="seat-stat-available">{{ $availableSeatsCount }}</span>
                </div>
                <div class="bg-red-50/60 border border-red-100/40 rounded-3xl p-6 text-center">
                    <span class="text-[10px] text-red-500 font-extrabold block uppercase tracking-wider font-semibold">Kursi Diblok</span>
                    <span class="text-2xl font-black text-red-650 block mt-1" id="seat-stat-blocked">{{ $blockedSeatsCount }}</span>
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

    <!-- Tab 4 Panel: Lucky Draw -->
    @if($event->has_lucky_draw)
        <div id="tab-panel-lucky_draw" class="tab-panel hidden space-y-8 mt-6">
            <!-- Lucky Draw Placeholder for Upcoming Event -->
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-10 text-center space-y-6">
                <div class="w-20 h-20 bg-indigo-50 border border-indigo-100 text-indigo-500 rounded-full flex items-center justify-center text-3xl mx-auto shadow-sm">
                    🎰
                </div>
                <div class="max-w-md mx-auto space-y-2">
                    <h3 class="text-2xl font-black text-gray-800">Undian Berhadiah (Lucky Draw)</h3>
                    <p class="text-sm text-gray-455 font-medium leading-relaxed">
                        Fitur Lucky Draw belum dimulai. Fitur ini akan aktif dan dapat diundi secara realtime setelah event berstatus **Ongoing** dan peserta mulai melakukan check-in di lokasi.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 bg-slate-50 border border-slate-100 text-slate-600 px-4 py-2 rounded-2xl text-xs font-bold">
                    <span>Hadiah Utama: <strong>{{ $event->prize_name ?? 'Mystery Gift' }}</strong></span>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab 5 Panel: Certificates -->
    @if($event->has_certificate)
        <div id="tab-panel-certificates" class="tab-panel hidden space-y-8 mt-6">
            <!-- Certificate Preview & Generation section -->
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 space-y-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-5 border-b border-gray-100">
                    <div>
                        <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Auto Certificate Dispatch</h3>
                        <p class="text-xs text-gray-400 font-semibold mt-1">Terbitkan sertifikat keikutsertakaan secara massal bagi seluruh peserta yang hadir di lokasi.</p>
                    </div>
                    <button disabled
                        class="px-5 py-3 bg-slate-100 text-slate-400 rounded-2xl font-bold text-xs shadow-sm cursor-not-allowed select-none flex items-center gap-2">
                        <span>Mass Issue Certificates 🚀</span>
                    </button>
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
                                    <span class="text-2xl font-black text-gray-800 block mt-1">0</span>
                                </div>
                                <div class="bg-amber-50/40 border border-amber-100/30 rounded-2xl p-4 text-center">
                                    <span class="text-[10px] text-amber-500 font-extrabold uppercase tracking-wider font-semibold">Pending Generation</span>
                                    <span class="text-2xl font-black text-amber-600 block mt-1">0</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-5 border-t border-slate-100 flex items-center justify-between text-xs text-gray-400">
                            <span>Title: <strong>{{ $event->certificate_title ?? '-' }}</strong></span>
                            <span>Organizer: <strong>{{ $event->organizer_name ?? '-' }}</strong></span>
                        </div>
                    </div>
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

        // Countdown script
        const countdownEl = document.getElementById('eventStartCountdown');
        if (countdownEl) {
            const startTimeStr = countdownEl.getAttribute('data-start-time');
            const startTime = new Date(startTimeStr).getTime();

            const updateCountdown = () => {
                const now = new Date().getTime();
                const distance = startTime - now;

                if (distance < 0) {
                    document.getElementById('countdown-days').innerText = '00';
                    document.getElementById('countdown-hours').innerText = '00';
                    document.getElementById('countdown-minutes').innerText = '00';
                    document.getElementById('countdown-seconds').innerText = '00';
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('countdown-days').innerText = days.toString().padStart(2, '0');
                document.getElementById('countdown-hours').innerText = hours.toString().padStart(2, '0');
                document.getElementById('countdown-minutes').innerText = minutes.toString().padStart(2, '0');
                document.getElementById('countdown-seconds').innerText = seconds.toString().padStart(2, '0');
            };

            updateCountdown();
            setInterval(updateCountdown, 1000);
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
