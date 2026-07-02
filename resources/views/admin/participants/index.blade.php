@extends('admin.layouts.app')
 
@section('title', 'Participants')
 
@section('content')
 
<div class="space-y-4 md:space-y-8">
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
 
    <!-- Dashboard Top Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Total Registrations Card -->
        <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-gray-400 text-sm font-bold block mb-1 uppercase tracking-wider">Total Registrations</span>
                <span class="text-4xl font-extrabold text-gray-800 leading-tight">
                    {{ number_format($totalRegistrations) }}
                </span>
                <span class="text-gray-400 text-xs block mt-1.5 font-semibold">Total pendaftar terdaftar</span>
            </div>
            <div class="w-14 h-14 bg-blue-50 border border-blue-100/30 rounded-2xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0 1 10.089 20.8M15 19.128a11.386 11.386 0 0 0 4.911-1.562M7.5 19.128a9.38 9.38 0 0 1-2.625.372 9.337 9.337 0 0 1-4.121-.952 4.125 4.125 0 0 1 7.533-2.493M7.5 19.128v-.003c0-1.113.285-2.16.786-3.07M7.5 19.128v.109A11.386 11.386 0 0 0 12.41 20.8M7.5 19.128a11.386 11.386 0 0 1-4.911-1.562m4.911-7.453A3 3 0 1 1 8.5 8c0 .351-.06.688-.172 1M12.41 14.8a8.979 8.979 0 0 1-4.91 1.562 8.979 8.979 0 0 1-4.91-1.562m9.82 0a8.979 8.979 0 0 0-4.91-1.562 8.979 8.979 0 0 0-4.91 1.562m4.91-1.562V9.75M12 3a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z" />
                </svg>
            </div>
        </div>
 
        <!-- Total Attended Card -->
        <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-gray-400 text-sm font-bold block mb-1 uppercase tracking-wider">Total Attended</span>
                <span class="text-4xl font-extrabold text-gray-800 leading-tight">
                    {{ number_format($totalAttended) }}
                </span>
                <span class="text-gray-400 text-xs block mt-1.5 font-semibold">Sudah check-in di lokasi</span>
            </div>
            <div class="w-14 h-14 bg-green-50 border border-green-100/30 rounded-2xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-green-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                </svg>
            </div>
        </div>
 
        <!-- Attendance Rate Card -->
        <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="w-full">
                <span class="text-gray-400 text-sm font-bold block mb-1 uppercase tracking-wider">Attendance Rate</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-extrabold text-gray-800 leading-tight">
                        {{ $attendanceRate }}%
                    </span>
                </div>
                <!-- Progres Bar -->
                <div class="w-full bg-gray-100 rounded-full h-2 mt-3">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $attendanceRate }}%"></div>
                </div>
            </div>
        </div>
 
    </div>
 
    <!-- Header: Title and Controls (Search) -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pt-2">
 
        <div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-800 tracking-tight">
                Participants
            </h1>
            <p class="text-gray-400 text-sm mt-2 font-semibold">
                Kelola pendaftaran peserta dan presensi Check-In secara real-time.
            </p>
        </div>
 
        <!-- Action Controls -->
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
            
            <!-- Search Participant input -->
            <div class="relative w-full sm:w-80">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5 text-gray-400 absolute left-4.5 top-1/2 transform -translate-y-1/2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input 
                    type="text" 
                    id="participantSearch" 
                    placeholder="Search participant..." 
                    class="w-full border border-gray-200 bg-gray-50/50 rounded-2xl pl-12 pr-5 py-3.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-sm font-semibold text-gray-700"
                >
            </div>
 
        </div>
 
    </div>
 
    <!-- Table Card Container -->
    <div class="bg-white rounded-[32px] shadow-sm border border-gray-100/80 overflow-hidden p-4 sm:p-6 md:p-10">
 
        <div class="overflow-x-auto">
            <table class="w-full">
                
                <!-- Table Head -->
                <thead class="text-gray-400 uppercase text-xs font-bold tracking-wider border-b border-gray-100 pb-4">
                    <tr>
                        <th class="text-left pb-5">PARTICIPANT</th>
                        <th class="text-left pb-5 pl-4">EVENT</th>
                        <th class="text-left pb-5 pl-4">TICKET TYPE</th>
                        <th class="text-left pb-5 pl-4">SEAT</th>
                        <th class="text-left pb-5 pl-4">STATUS</th>
                        <th class="text-left pb-5">ACTION</th>
                    </tr>
                </thead>
 
                <!-- Table Body -->
                <tbody class="divide-y divide-gray-50">
                    @forelse($registrations as $reg)
                    <tr class="participant-row hover:bg-gray-50/30 transition duration-150"
                        data-name="{{ $reg->user ? $reg->user->name : '' }}"
                        data-email="{{ $reg->user ? $reg->user->email : '' }}"
                        data-event="{{ $reg->event ? $reg->event->name : '' }}"
                        data-ticket="{{ $reg->ticketCategory ? $reg->ticketCategory->name : '' }}"
                        data-seat="{{ $reg->seat_number ?? '-' }}">
                        
                        <!-- Participant Info -->
                        <td class="py-5 pr-4">
                            <div class="flex items-center gap-4">
                                <!-- Initials avatar icon -->
                                <div class="w-11 h-11 bg-slate-100 border border-slate-200/30 rounded-2xl flex items-center justify-center font-extrabold text-sm text-slate-600 uppercase flex-shrink-0">
                                    {{ $reg->user ? substr($reg->user->name, 0, 1) : '?' }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-gray-800 text-base leading-snug">
                                        {{ $reg->user ? $reg->user->name : 'Unknown User' }}
                                    </h3>
                                    <p class="text-gray-400 text-xs mt-1.5 font-medium line-clamp-1">
                                        {{ $reg->user ? $reg->user->email : '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>
 
                        <!-- Event -->
                        <td class="py-5 pl-4 pr-4">
                            <span class="text-gray-650 font-bold text-sm block max-w-[200px] truncate">
                                {{ $reg->event ? $reg->event->name : 'Unknown Event' }}
                            </span>
                        </td>
 
                        <!-- Ticket Category -->
                        <td class="py-5 pl-4 pr-4">
                            @php
                                $ticketName = $reg->ticketCategory ? $reg->ticketCategory->name : 'Regular';
                                $isVIP = str_contains(strtolower($ticketName), 'vip');
                                $ticketPill = $isVIP 
                                    ? 'bg-purple-50 text-purple-600 border border-purple-100/50' 
                                    : 'bg-blue-50 text-blue-600 border border-blue-100/50';
                            @endphp
                            <span class="{{ $ticketPill }} px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide whitespace-nowrap">
                                {{ $ticketName }}
                            </span>
                        </td>
 
                        <!-- Seat number -->
                        <td class="py-5 pl-4 pr-4">
                            <span class="text-gray-800 font-extrabold text-sm">
                                {{ $reg->seat_number ?? '-' }}
                            </span>
                        </td>
 
                        <!-- Check-in Status -->
                        <td class="py-5 pl-4 pr-4">
                            @if($reg->is_checked_in)
                                <div class="flex flex-col">
                                    <span class="bg-green-50 text-green-600 border border-green-100/50 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide w-fit flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                        </svg>
                                        Checked In
                                    </span>
                                    @if($reg->checked_in_at)
                                        <span class="text-gray-450 text-[10px] block mt-1 font-semibold pl-1.5">
                                            ⏱ {{ is_string($reg->checked_in_at) ? date('H:i', strtotime($reg->checked_in_at)) : $reg->checked_in_at->format('H:i') }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="bg-gray-50 text-gray-400 border border-gray-150 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide w-fit flex items-center gap-1.5">
                                    Pending
                                </span>
                            @endif
                        </td>
 
                        <!-- Check-in Action Toggle -->
                        <td class="py-5">
                            @if($eventStatus === 'ongoing')
                                <form action="{{ route('admin.participants.check_in', $reg->id) }}" method="POST">
                                    @csrf
                                    @if($reg->is_checked_in)
                                        <button type="submit" 
                                            class="bg-gray-100 hover:bg-gray-200 text-gray-650 px-4 py-2 rounded-xl text-xs font-bold tracking-wide transition border border-gray-200/50 cursor-pointer">
                                            Cancel Check-In
                                        </button>
                                    @else
                                        <button type="submit" 
                                            class="bg-green-50 hover:bg-green-100 text-green-600 px-4 py-2 rounded-xl text-xs font-bold tracking-wide transition border border-green-100/50 cursor-pointer">
                                            Check-In
                                        </button>
                                    @endif
                                </form>
                            @elseif($eventStatus === 'upcoming')
                                <button disabled class="bg-gray-100 text-gray-450 px-4 py-2 rounded-xl text-xs font-bold tracking-wide border border-gray-250/50 cursor-not-allowed select-none">
                                    Belum Mulai
                                </button>
                            @else
                                <button disabled class="bg-gray-150 text-gray-400 px-4 py-2 rounded-xl text-xs font-bold tracking-wide border border-gray-200/50 cursor-not-allowed select-none">
                                    Selesai
                                </button>
                            @endif
                        </td>
 
                    </tr>
                    @empty
                    <tr id="noResultsRow">
                        <td colspan="6" class="text-center py-20 text-gray-450 font-bold text-sm">
                            Belum ada peserta terdaftar 😄
                        </td>
                    </tr>
                    @endforelse
                    
                    {{-- Row for client side search zero hits --}}
                    <tr id="clientNoResultsRow" style="display: none;">
                        <td colspan="6" class="text-center py-20 text-gray-450 font-bold text-sm">
                            Peserta yang Anda cari tidak ditemukan 😕
                        </td>
                    </tr>
                </tbody>
 
            </table>
        </div>
 
    </div>
 
</div>
 
<!-- JavaScript: Real-time search and dropdown redirection -->
<script>
    // Client-Side Search
    const searchInput = document.getElementById('participantSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.participant-row');
            let foundAny = false;
            
            rows.forEach(row => {
                const name = row.getAttribute('data-name').toLowerCase();
                const email = row.getAttribute('data-email').toLowerCase();
                const event = row.getAttribute('data-event').toLowerCase();
                const ticket = row.getAttribute('data-ticket').toLowerCase();
                const seat = row.getAttribute('data-seat').toLowerCase();
                
                if (name.includes(query) || email.includes(query) || event.includes(query) || ticket.includes(query) || seat.includes(query)) {
                    row.style.display = '';
                    foundAny = true;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Toggle zero state
            const zeroState = document.getElementById('clientNoResultsRow');
            if (zeroState) {
                zeroState.style.display = foundAny ? 'none' : '';
            }
            
            // Hide the native empty row if query is running
            const nativeEmptyRow = document.getElementById('noResultsRow');
            if (nativeEmptyRow) {
                nativeEmptyRow.style.display = foundAny ? 'none' : (query === '' ? '' : 'none');
            }
        });
    }
</script>
 
@endsection
