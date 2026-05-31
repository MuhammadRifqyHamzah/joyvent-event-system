@extends('admin.layouts.app')
 
@section('title', 'Seats')
 
@section('content')
 
<div class="space-y-8">
    @include('admin.events.partials.header')
    @php
        $eventStatus = $event->calculated_status;
    @endphp
 
    {{-- Success Alert Notification --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4.5 rounded-2xl flex items-center gap-3 shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-green-600 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif
 
    {{-- Errors Alert Notification --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-100 text-red-650 px-6 py-4.5 rounded-2xl flex items-center gap-3 shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-red-650 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            <span class="font-bold text-sm">{{ $errors->first() }}</span>
        </div>
    @endif
 
    <!-- Header: Title -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
 
        <div>
            <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">
                Seats Management
            </h1>
            <p class="text-gray-400 text-sm mt-2 font-semibold">
                Kelola layout tempat duduk dan blokir kursi secara visual interaktif.
            </p>
        </div>
 
    </div>
 
    <!-- Content Cards -->
    @if($event && !$event->has_seat_layout)
        <!-- State: Event is standing (No Seat Layout) -->
        <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-16 text-center">
            <div class="w-20 h-20 bg-amber-50 border border-amber-100/30 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10 text-amber-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 0L15.5 15m3.5-9L8 19.5" />
                </svg>
            </div>
            <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Event Berjenis Festival</h3>
            <p class="text-gray-400 text-sm mt-2 max-w-md mx-auto leading-relaxed font-semibold">
                Event <strong>"{{ $event->name }}"</strong> diatur sebagai Standing/Festival. Seluruh peserta berdiri bebas sehingga tidak memerlukan nomor denah kursi. 😄
            </p>
        </div>
    @else
        <!-- Seated Event Layout Editor -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
 
            <!-- Left Side: Information & Generator Form (Takes 1 Column) -->
            <div class="space-y-6 lg:col-span-1">
                
                <!-- Event Info Card -->
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8">
                    <span class="text-blue-600 font-extrabold text-xs tracking-wider uppercase">Active Event</span>
                    <h3 class="text-xl font-extrabold text-gray-800 tracking-tight mt-1">
                        {{ $event->name }}
                    </h3>
                    <div class="flex items-center gap-1.5 text-gray-400 text-xs font-semibold mt-3">
                        <span>📍 {{ $event->location }}</span>
                    </div>
                </div>
 
                <!-- Mass Generator / Layout Summary Card -->
                @if($eventStatus === 'finished')
                    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 text-center">
                        <div class="w-14 h-14 bg-slate-50 border border-slate-200 text-slate-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-extrabold text-gray-800">Konfigurasi Dikunci</h4>
                        <p class="text-xs text-gray-400 mt-2 font-semibold leading-relaxed">
                            Layout tempat duduk tidak dapat diubah karena event telah selesai.
                        </p>
                    </div>
                @elseif($groupedSeats->isEmpty())
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8">
                    <h4 class="text-base font-extrabold text-gray-800 tracking-tight flex items-center gap-2 mb-4">
                        <span>🪑 Mass Seat Generator</span>
                    </h4>
                    <form action="{{ route('admin.seats.generate') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
 
                        <!-- Row Letters -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Baris Kursi</label>
                            <input 
                                type="text" 
                                name="rows" 
                                value="A, B, C, D" 
                                placeholder="Pisahkan koma, contoh: A, B, C" 
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm font-semibold"
                                required
                            >
                            <span class="text-[10px] text-gray-400 block font-semibold leading-normal">Gunakan huruf dipisah koma untuk mendefinisikan baris.</span>
                        </div>
 
                        <!-- Seats Per Row -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Kursi per Baris</label>
                            <input 
                                type="number" 
                                name="seats_per_row" 
                                value="10" 
                                min="1" 
                                max="30"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm font-semibold"
                                required
                            >
                        </div>
 
                        <!-- Submit Button -->
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl text-xs tracking-wider transition cursor-pointer mt-2 shadow-sm">
                            Generate Seats 🪑
                        </button>
                    </form>
                </div>
                @else
                <!-- Layout Stats Card -->
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 space-y-4">
                    <h4 class="text-sm font-extrabold text-gray-400 uppercase tracking-wider">Layout Summary</h4>
                    
                    @php
                        $total = 0;
                        $available = 0;
                        $booked = 0;
                        $blocked = 0;
                        foreach($groupedSeats as $rowLabel => $seats) {
                            $total += $seats->count();
                            $available += $seats->where('status', 'available')->count();
                            $booked += $seats->where('status', 'booked')->count();
                            $blocked += $seats->where('status', 'blocked')->count();
                        }
                    @endphp
 
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 text-center">
                            <span class="text-[10px] text-gray-400 font-extrabold block uppercase tracking-wider">Total</span>
                            <span class="text-xl font-black text-gray-800 block mt-1">{{ $total }}</span>
                        </div>
                        <div class="bg-green-50/40 border border-green-100/30 rounded-2xl p-4 text-center">
                            <span class="text-[10px] text-green-500 font-extrabold block uppercase tracking-wider">Kosong</span>
                            <span class="text-xl font-black text-green-600 block mt-1">{{ $available }}</span>
                        </div>
                        <div class="bg-blue-50/40 border border-blue-100/30 rounded-2xl p-4 text-center">
                            <span class="text-[10px] text-blue-500 font-extrabold block uppercase tracking-wider">Dipesan</span>
                            <span class="text-xl font-black text-blue-600 block mt-1">{{ $booked }}</span>
                        </div>
                        <div class="bg-red-50/40 border border-red-100/30 rounded-2xl p-4 text-center">
                            <span class="text-[10px] text-red-500 font-extrabold block uppercase tracking-wider">Diblokir</span>
                            <span class="text-xl font-black text-red-600 block mt-1">{{ $blocked }}</span>
                        </div>
                    </div>
                </div>
                @endif
 
            </div>
 
            <!-- Right Side: Interactive 2D Seat Map (Takes 3 Columns) -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-10 space-y-8">
 
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Interactive Seat Map</h3>
                        <p class="text-gray-400 text-xs mt-1.5 font-semibold">
                            Klik kursi kosong (hijau) untuk memblokir, klik kursi diblokir (merah) untuk membuka blokir. Arahkan kursor ke kursi biru untuk detail pemesan.
                        </p>
                    </div>
 
                    @if($groupedSeats->isEmpty())
                        <!-- Empty Grid State -->
                        <div class="bg-slate-50/50 border-2 border-dashed border-slate-100 rounded-3xl p-16 text-center">
                            <div class="w-16 h-16 bg-slate-100 border border-slate-200/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 text-slate-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m15 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm12 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-bold text-gray-600">Peta Kursi Kosong</h4>
                            <p class="text-gray-400 text-xs mt-1 font-semibold max-w-xs mx-auto leading-normal">
                                Belum ada kursi yang di-generate. Silakan isi form generator di samping kiri untuk membuat peta kursi.
                            </p>
                        </div>
                    @else
                        <!-- Visual Seat Grid Map -->
                        <div class="flex flex-col gap-4 items-center justify-center p-8 bg-slate-50 border border-slate-100 rounded-[32px] overflow-auto select-none min-h-[300px]">
                            
                            <!-- Panggung Utama Label -->
                            <div class="w-full max-w-sm bg-slate-200 border border-slate-300/40 text-slate-500 text-[10px] font-bold py-2 rounded-lg text-center tracking-[4px] uppercase mb-12 shadow-sm">
                                PANGGUNG UTAMA / STAGE
                            </div>
 
                            <!-- Rows Loop -->
                            @foreach($groupedSeats as $rowLabel => $seats)
                                <div class="flex items-center gap-3">
                                    <!-- Row Label Left -->
                                    <span class="w-8 text-right font-extrabold text-xs text-slate-400 uppercase select-none mr-2">
                                        {{ $rowLabel }}
                                    </span>
                                    
                                    <!-- Seats Grid inside Row -->
                                    <div class="flex items-center gap-2">
                                        @foreach($seats as $seat)
                                            @php
                                                $status = $seat->status;
                                                $booking = $registrations->get($seat->seat_number);
                                                
                                                // Map colors
                                                if ($status === 'booked') {
                                                    $btnColor = 'bg-blue-50 border-blue-200 text-blue-600 hover:bg-blue-100/70 hover:border-blue-300';
                                                } elseif ($status === 'blocked') {
                                                    $btnColor = 'bg-red-50 border-red-200 text-red-650 hover:bg-red-100/70 hover:border-red-300';
                                                } else {
                                                    $btnColor = 'bg-green-50 border-green-200 text-green-600 hover:bg-green-100/70 hover:border-green-300';
                                                }
                                            @endphp
                                            
                                            <!-- Seat Item Container -->
                                            <div class="relative group">
                                                @if($eventStatus === 'finished')
                                                    <button type="button" 
                                                        disabled
                                                        class="w-10 h-10 rounded-xl border flex items-center justify-center font-bold text-xs tracking-tight transition shadow-sm disabled:cursor-not-allowed select-none {{ $btnColor }}">
                                                        {{ $seat->column }}
                                                    </button>
                                                @else
                                                    <form action="{{ route('admin.seats.toggle_status', $seat->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                            {{ $status === 'booked' ? 'disabled' : '' }}
                                                            class="w-10 h-10 rounded-xl border flex items-center justify-center font-bold text-xs tracking-tight transition shadow-sm cursor-pointer disabled:cursor-not-allowed select-none {{ $btnColor }}">
                                                            {{ $seat->column }}
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <!-- Tooltip overlay for Booked / Occupied Seat -->
                                                @if($status === 'booked' && $booking && $booking->user)
                                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3.5 hidden group-hover:block z-30 w-56 bg-slate-900/95 backdrop-blur text-white p-3.5 rounded-2xl shadow-xl text-center border border-slate-700/50">
                                                        <div class="font-extrabold text-sm truncate leading-snug">
                                                            {{ $booking->user->name }}
                                                        </div>
                                                        <div class="text-[10px] text-slate-400 mt-1 font-semibold truncate">
                                                            {{ $booking->user->email }}
                                                        </div>
                                                        <div class="inline-block mt-2 bg-blue-500/20 text-blue-400 border border-blue-400/30 text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider">
                                                            {{ $booking->ticketCategory ? $booking->ticketCategory->name : 'VIP' }}
                                                        </div>
                                                        <!-- Arrow shape -->
                                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-t-[6px] border-t-slate-900/95"></div>
                                                    </div>
                                                @endif
 
                                                <!-- Tooltip overlay for Blocked Seat -->
                                                @if($status === 'blocked')
                                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3.5 hidden group-hover:block z-30 w-44 bg-slate-900/95 backdrop-blur text-white p-2.5 rounded-2xl shadow-xl text-center border border-slate-700/50">
                                                        <div class="font-bold text-xs tracking-wide">
                                                            Kursi Diblokir 🔴
                                                        </div>
                                                        <p class="text-[9px] text-slate-400 mt-1 font-semibold leading-normal">
                                                            Klik untuk membuka blokir agar tersedia kembali
                                                        </p>
                                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-t-[6px] border-t-slate-900/95"></div>
                                                    </div>
                                                @endif
 
                                                <!-- Tooltip overlay for Available Seat -->
                                                @if($status === 'available')
                                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3.5 hidden group-hover:block z-30 w-40 bg-slate-900/95 backdrop-blur text-white p-2.5 rounded-2xl shadow-xl text-center border border-slate-700/50">
                                                        <div class="font-bold text-xs tracking-wide">
                                                            Kursi Kosong 🟢
                                                        </div>
                                                        <p class="text-[9px] text-slate-400 mt-1 font-semibold leading-normal">
                                                            Klik untuk memblokir kursi ini
                                                        </p>
                                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-t-[6px] border-t-slate-900/95"></div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
 
                            <!-- Bottom Stage Label / Pintu Masuk -->
                            <div class="text-[9px] text-slate-400 font-extrabold uppercase tracking-[2px] mt-8">
                                🚪 PINTU MASUK / ENTRANCE
                            </div>
                        </div>
 
                        <!-- Legenda Status Grid -->
                        <div class="flex items-center justify-center gap-8 pt-4 border-t border-gray-50 flex-wrap">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-md bg-green-50 border border-green-200"></div>
                                <span class="text-xs font-bold text-gray-500">🟢 Tersedia (Available)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-md bg-blue-50 border border-blue-200"></div>
                                <span class="text-xs font-bold text-gray-500">🔵 Terisi (Booked / Occupied)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-md bg-red-50 border border-red-200"></div>
                                <span class="text-xs font-bold text-gray-500">🔴 Diblokir (Blocked)</span>
                            </div>
                        </div>
                    @endif
 
                </div>
            </div>
 
        </div>
    @endif
 
</div>
 
@endsection
