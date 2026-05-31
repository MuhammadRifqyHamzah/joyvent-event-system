@extends('admin.layouts.app')

@section('title', $event->name . ' - Event Details')

@section('content')

@php
    // Calculate Event Status
    $now = \Carbon\Carbon::now();
    $startDate = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time);
    $endDate = \Carbon\Carbon::parse($event->end_date . ' ' . $event->end_time);

    if ($now->between($startDate, $endDate)) {
        $statusLabel = 'On-going';
        $statusColor = 'bg-green-50 text-green-600 border border-green-100/50';
        $gradientColor = 'from-green-600 to-emerald-600';
    } elseif ($startDate->isFuture()) {
        $statusLabel = 'Upcoming';
        $statusColor = 'bg-blue-50 text-blue-600 border border-blue-100/50';
        $gradientColor = 'from-blue-600 to-indigo-600';
    } else {
        $statusLabel = 'Finished';
        $statusColor = 'bg-slate-100 text-slate-700 border border-slate-200/60';
        $gradientColor = 'from-slate-600 to-slate-800';
    }

    // Dynamic Gradient Category Cover (in case not overridden by status gradient)
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
            <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">Event Overview</h1>
            <p class="text-gray-400 text-sm mt-1.5 font-semibold">Monitor statistik penjualan tiket dan daftar peserta aktif.</p>
        </div>
        <a href="{{ route('admin.events') }}" 
            class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-5 py-3.5 rounded-2xl font-bold text-sm shadow-sm transition flex items-center gap-2 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-gray-550">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Event Banner Cover Header -->
    <div class="relative w-full h-64 md:h-72 bg-gradient-to-r {{ $coverGradient }} rounded-[32px] p-8 md:p-10 flex flex-col justify-end overflow-hidden shadow-sm border border-white/10"
         @if($event->banner_image) style="background-image: url('{{ $event->banner_image }}'); background-size: cover; background-position: center;" @endif>
        
        @if($event->banner_image)
            <!-- Dark Overlay (50%) -->
            <div class="absolute inset-0 bg-black/50 pointer-events-none z-0"></div>
        @else
            <!-- Glassmorphism ambient background circles -->
            <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute right-32 bottom-5 w-36 h-36 rounded-full bg-white/5 blur-xl pointer-events-none"></div>
        @endif

        <div class="relative z-10 space-y-4">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="bg-white/20 backdrop-blur text-white px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider border border-white/20">
                    {{ $event->category }}
                </span>
                <span class="{{ $statusColor }} bg-opacity-95 px-3.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide">
                    {{ $statusLabel }}
                </span>
            </div>

            <h2 class="text-white text-3xl md:text-5xl font-black tracking-tight leading-tight drop-shadow-sm truncate max-w-4xl">
                {{ $event->name }}
            </h2>

            <div class="flex items-center gap-6 text-white/90 text-sm font-semibold flex-wrap">
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-white/70">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <span>{{ $event->location }}</span>
                </span>
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-white/70">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008H14.25v-.008Zm0 2.25h.008v.008H14.25V15Zm0 2.25h.008v.008H14.25v-.008ZM16.5 15h.008v.008H16.5V15Zm0 2.25h.008v.008H16.5v-.008Z" />
                    </svg>
                    <span>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }} ({{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }})</span>
                </span>
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
                <span class="text-[10px] text-green-500 font-extrabold uppercase tracking-wider block">Sisa Kursi</span>
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
                <span class="text-[10px] text-amber-500 font-extrabold uppercase tracking-wider block">Total Pendapatan</span>
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
        <!-- Modern Rounded progress bar -->
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

    <!-- Registered Participants List -->
    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-10 overflow-hidden">
        <div class="pb-6 border-b border-gray-100 mb-6">
            <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Registered Participants</h3>
            <p class="text-xs text-gray-400 font-semibold mt-1.5">Daftar lengkap peserta yang telah terdaftar pada event ini.</p>
        </div>

        <div class="overflow-x-auto">
            @if($event->registrations->isEmpty())
                <div class="text-center py-16 text-gray-400 font-bold text-sm">
                    No ticket purchases yet.
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
                    <tbody class="divide-y divide-gray-50">
                        @foreach($event->registrations as $reg)
                            <tr class="hover:bg-gray-50/30 transition duration-150">
                                <!-- User Info -->
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

                                <!-- Email -->
                                <td class="py-4 pl-4 pr-4">
                                    <span class="text-gray-650 font-bold text-xs">
                                        {{ $reg->user->email ?? '-' }}
                                    </span>
                                </td>

                                <!-- Ticket Class -->
                                <td class="py-4 pl-4 pr-4">
                                    <span class="bg-indigo-50 text-indigo-600 border border-indigo-100/30 px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap">
                                        {{ $reg->ticketCategory->name ?? '-' }}
                                    </span>
                                </td>

                                <!-- Date -->
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

@endsection
