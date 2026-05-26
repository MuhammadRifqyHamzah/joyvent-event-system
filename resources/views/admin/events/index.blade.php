@extends('admin.layouts.app')
 
@section('title', 'Events Management')
 
@section('content')
 
<div class="space-y-8">
 
    {{-- Session Success Notification --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4.5 rounded-2xl flex items-center gap-3 shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-green-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif
 
    <!-- White Card Container -->
    <div class="bg-white rounded-[32px] shadow-sm border border-gray-100/80 overflow-hidden p-10">
 
        <!-- Header: Title, Description, Search and Create Event -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 pb-8 border-b border-gray-100 mb-8">
            
            <div>
                <h2 class="text-4xl font-extrabold text-gray-800 tracking-tight">
                    List Events
                </h2>
                <p class="text-gray-400 text-sm mt-2 font-semibold">
                    Kelola semua event JoyVent dengan mudah dan cepat.
                </p>
            </div>
 
            <!-- Action Controls (Search + Create Event side-by-side) -->
            <div class="flex items-center gap-4 w-full md:w-auto">
                
                <!-- Search Input -->
                <div class="relative flex-1 md:flex-none">
                    <!-- Magnifying glass outline icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input 
                        type="text" 
                        id="eventSearch" 
                        placeholder="Search event..." 
                        class="border border-gray-200/80 bg-gray-50/50 rounded-2xl pl-12 pr-5 py-3.5 w-full md:w-80 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-sm font-semibold text-gray-700"
                    >
                </div>
 
                <!-- Create Event Button -->
                <a href="{{ route('admin.events.create') }}" class="px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-sm shadow-sm transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <!-- Plus outline icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Create Event</span>
                </a>
 
            </div>
 
        </div>
 
        <!-- Table Area -->
        <div class="overflow-x-auto">
            <table class="w-full">
                
                <!-- Table Head -->
                <thead class="text-gray-400 uppercase text-xs font-bold tracking-wider border-b border-gray-100 pb-4">
                    <tr>
                        <th class="text-left pb-5">EVENT</th>
                        <th class="text-left pb-5 pl-4">LOCATION</th>
                        <th class="text-left pb-5 pl-4">DATE</th>
                        <th class="text-left pb-5 pl-4">CAPACITY</th>
                        <th class="text-left pb-5 pl-4">STATUS</th>
                        <th class="text-left pb-5">ACTION</th>
                    </tr>
                </thead>
 
                <!-- Table Body -->
                <tbody class="divide-y divide-gray-50">
                    @forelse($events as $event)
                    <tr class="event-row hover:bg-gray-50/30 transition duration-150" 
                        data-name="{{ $event->name }}" 
                        data-desc="{{ $event->description }}" 
                        data-location="{{ $event->location }}">
                        
                        <!-- Event Info -->
                        <td class="py-5 pr-4">
                            <div class="flex items-center gap-4">
                                <!-- Ticket Icon -->
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0 border border-blue-100/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-blue-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-12v.75m0 3v.75m0 3v.75m0 3V18M3 8.25a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 8.25V15.75a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15.75V8.25Z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-gray-800 text-base leading-snug">
                                        {{ $event->name }}
                                    </h3>
                                    <p class="text-gray-400 text-xs mt-1.5 line-clamp-1 max-w-[280px] font-medium">
                                        {{ $event->description }}
                                    </p>
                                </div>
                            </div>
                        </td>
 
                        <!-- Location -->
                        <td class="py-5 pl-4 pr-4">
                            <span class="flex items-center gap-1.5">
                                <!-- Location Pin outline icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 flex-shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                <span class="truncate text-gray-500 font-semibold text-sm max-w-[160px]">{{ $event->location }}</span>
                            </span>
                        </td>
 
                        <!-- Date -->
                        <td class="py-5 pl-4 pr-4">
                            <span class="flex items-center gap-1.5">
                                <!-- Calendar outline icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 flex-shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                <span class="text-gray-500 font-semibold text-sm whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}
                                </span>
                            </span>
                        </td>
 
                        <!-- Capacity -->
                        <td class="py-5 pl-4 pr-4">
                            <span class="flex items-center gap-1.5">
                                <!-- User outline icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4 text-gray-400 flex-shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                <span class="text-gray-800 font-extrabold text-sm">
                                    {{ number_format($event->capacity) }}
                                </span>
                            </span>
                        </td>
 
                        <!-- Status Pill -->
                        <td class="py-5 pl-4 pr-4">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-amber-50 text-amber-600 border border-amber-100/50',
                                    'active' => 'bg-green-50 text-green-600 border border-green-100/50',
                                    'open' => 'bg-green-50 text-green-600 border border-green-100/50',
                                    'closed' => 'bg-red-50 text-red-600 border border-red-100/50',
                                    'finished' => 'bg-blue-50 text-blue-600 border border-blue-100/50',
                                ];
                                $statusLabels = [
                                    'draft' => 'Draft',
                                    'active' => 'Active',
                                    'open' => 'Active',
                                    'closed' => 'Closed',
                                    'finished' => 'Finished',
                                ];
                                $colorClass = $statusColors[$event->status] ?? 'bg-gray-50 text-gray-600 border border-gray-150';
                                $label = $statusLabels[$event->status] ?? ucfirst($event->status);
                            @endphp
                            <span class="{{ $colorClass }} px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide whitespace-nowrap">
                                {{ $label }}
                            </span>
                        </td>
 
                        <!-- Action Row -->
                        <td class="py-5">
                            <div class="flex items-center gap-3">
                                
                                <!-- Tickets Button -->
                                <a href="{{ route('admin.tickets.index', $event->id) }}" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-4 py-2 rounded-xl text-xs font-bold tracking-wide transition flex items-center gap-1.5 border border-indigo-100/50 cursor-pointer">
                                    <!-- Ticket outline icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-indigo-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-12v.75m0 3v.75m0 3v.75m0 3V18M3 8.25a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 8.25V15.75a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15.75V8.25Z" />
                                    </svg>
                                    <span>Tickets</span>
                                </a>
 
                                <!-- Edit Button -->
                                <a href="{{ route('admin.events.edit', $event->id) }}" class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-4 py-2 rounded-xl text-xs font-bold tracking-wide transition flex items-center gap-1.5 border border-blue-100/50 cursor-pointer">
                                    <!-- Pencil/Edit outline icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-blue-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    <span>Edit</span>
                                </a>
 
                                <!-- Delete Button -->
                                <button type="button" onclick="triggerDelete('{{ $event->id }}', '{{ addslashes($event->name) }}')" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-xl text-xs font-bold tracking-wide transition flex items-center gap-1.5 border border-red-100/50 cursor-pointer">
                                    <!-- Trash outline icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-red-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                    <span>Delete</span>
                                </button>
 
                                <!-- Laravel Delete Form -->
                                <form id="delete-form-{{ $event->id }}" action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
 
                            </div>
                        </td>
 
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-20 text-gray-400 font-bold text-sm">
                            Belum ada event terdaftar 😄
                        </td>
                    </tr>
                    @endforelse
 
                    <!-- No Results Template row (hidden by default) -->
                    <tr id="noResultsRow" class="hidden">
                        <td colspan="6" class="text-center py-20 text-gray-450 font-bold text-sm">
                            Event yang Anda cari tidak ditemukan.
                        </td>
                    </tr>
 
                </tbody>
 
            </table>
        </div>
 
    </div>
 
</div>
 
<!-- JavaScript: Client-Side Instant Search and Delete Confirm Dialogue -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Instant Client-Side Search
        const eventSearch = document.getElementById('eventSearch');
        
        if (eventSearch) {
            eventSearch.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                const rows = document.querySelectorAll('tbody tr.event-row');
                let hasResults = false;
                
                rows.forEach(row => {
                    const name = row.getAttribute('data-name').toLowerCase();
                    const desc = row.getAttribute('data-desc').toLowerCase();
                    const location = row.getAttribute('data-location').toLowerCase();
                    
                    if (name.includes(query) || desc.includes(query) || location.includes(query)) {
                        row.style.display = '';
                        hasResults = true;
                    } else {
                        row.style.display = 'none';
                    }
                });
 
                const noResultsRow = document.getElementById('noResultsRow');
                if (noResultsRow) {
                    if (!hasResults && query.length > 0) {
                        noResultsRow.classList.remove('hidden');
                    } else {
                        noResultsRow.classList.add('hidden');
                    }
                }
            });
        }
    });
 
    // 2. Interactive Confirm Delete
    function triggerDelete(eventId, eventName) {
        if (confirm(`Apakah Anda yakin ingin menghapus event "${eventName}"? Tindakan ini tidak dapat dibatalkan.`)) {
            const form = document.getElementById(`delete-form-${eventId}`);
            if (form) {
                form.submit();
            }
        }
    }
</script>
 
@endsection