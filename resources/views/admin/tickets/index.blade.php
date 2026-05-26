@extends('admin.layouts.app')
 
@section('title', 'Ticket Categories')
 
@section('content')
 
<div class="space-y-8">
 
    {{-- Success Notification Alert --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4.5 rounded-2xl flex items-center gap-3 shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-green-600 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif
 
    <!-- Header: Title, Event context, and Add Ticket Button -->
    <div class="flex justify-between items-center pb-2">
 
        <div>
            <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">
                Ticket Categories
            </h1>
            <p class="text-gray-400 text-sm mt-2 font-semibold flex items-center gap-1">
                Event:
                <span class="text-gray-700 font-extrabold">
                    {{ $event->name }}
                </span>
            </p>
        </div>
 
        <!-- Add Ticket trigger button -->
        <button type="button" onclick="openAddTicketModal()" 
            class="px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-sm shadow-sm transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
            <!-- Plus outline icon -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-white">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Add Ticket</span>
        </button>
 
    </div>
 
    <!-- Main Card Container -->
    <div class="bg-white rounded-[32px] shadow-sm border border-gray-100/80 overflow-hidden p-10">
 
        <!-- Section Header -->
        <div class="pb-8 border-b border-gray-100 mb-8">
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">
                List Ticket Categories
            </h2>
            <p class="text-gray-400 text-sm mt-1.5 font-semibold">
                Kelola semua kategori tiket 😄
            </p>
        </div>
 
        <!-- Table Area -->
        <div class="overflow-x-auto">
            <table class="w-full">
 
                <!-- Table Head -->
                <thead class="text-gray-400 uppercase text-xs font-bold tracking-wider border-b border-gray-100 pb-4">
                    <tr>
                        <th class="text-left pb-5">TICKET</th>
                        <th class="text-left pb-5 pl-4">PRICE</th>
                        <th class="text-left pb-5 pl-4">QUOTA</th>
                        <th class="text-left pb-5 pl-4">STATUS</th>
                        <th class="text-left pb-5">ACTION</th>
                    </tr>
                </thead>
 
                <!-- Table Body -->
                <tbody class="divide-y divide-gray-50">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50/30 transition duration-150">
 
                        <!-- Ticket Info -->
                        <td class="py-5 pr-4">
                            <div class="flex items-center gap-4">
                                <!-- Ticket Icon in blue rounded container -->
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0 border border-blue-100/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-blue-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-12v.75m0 3v.75m0 3v.75m0 3V18M3 8.25a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 8.25V15.75a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15.75V8.25Z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-gray-800 text-base leading-snug">
                                        {{ $ticket->name }}
                                    </h3>
                                    @if($ticket->description)
                                        <p class="text-gray-400 text-xs mt-1.5 line-clamp-1 max-w-[280px] font-medium">
                                            {{ $ticket->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>
 
                        <!-- Price -->
                        <td class="py-5 pl-4 pr-4">
                            <span class="text-gray-650 font-bold text-sm">
                                Rp {{ number_format($ticket->price) }}
                            </span>
                        </td>
 
                        <!-- Quota -->
                        <td class="py-5 pl-4 pr-4">
                            <span class="text-gray-800 font-extrabold text-sm">
                                {{ number_format($ticket->quota) }}
                            </span>
                        </td>
 
                        <!-- Status -->
                        <td class="py-5 pl-4 pr-4">
                            @if($ticket->is_active)
                                <span class="bg-green-50 text-green-600 border border-green-100/50 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide">
                                    Active
                                </span>
                            @else
                                <span class="bg-gray-50 text-gray-400 border border-gray-150 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide">
                                    Inactive
                                </span>
                            @endif
                        </td>
 
                        <!-- Action -->
                        <td class="py-5">
                            <div class="flex items-center gap-3">
                                
                                <!-- Delete Button -->
                                <button type="button" onclick="triggerDelete('{{ $ticket->id }}', '{{ addslashes($ticket->name) }}')" 
                                    class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-xl text-xs font-bold tracking-wide transition flex items-center gap-1.5 border border-red-100/50 cursor-pointer">
                                    <!-- Trash outline icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-red-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                    <span>Delete</span>
                                </button>
 
                                <!-- Laravel Delete Ticket Form -->
                                <form id="delete-form-{{ $ticket->id }}" action="{{ route('admin.tickets.destroy', $ticket->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
 
                            </div>
                        </td>
 
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-20 text-gray-400 font-bold text-sm">
                            Belum ada ticket category 😄
                        </td>
                    </tr>
                    @endforelse
                </tbody>
 
            </table>
        </div>
 
    </div>
 
</div>
 
<!-- Add Ticket Overlay Popup Modal -->
<div id="addTicketModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <!-- Dark blurred overlay backdrop -->
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition duration-300" onclick="closeAddTicketModal()"></div>
    
    <!-- Modal Dialog White Card Content -->
    <div class="relative bg-white rounded-[32px] shadow-2xl border border-gray-150/40 w-full max-w-xl p-8 z-10 transform scale-95 transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-5 border-b border-gray-100">
            <h3 class="text-2xl font-extrabold text-gray-800 flex items-center gap-2">
                <!-- Plus outline icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Add Ticket</span>
            </h3>
            <!-- Close trigger X icon button -->
            <button type="button" onclick="closeAddTicketModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-full p-2 transition cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
 
        <!-- Modal Form Input Fields -->
        <form action="{{ route('admin.tickets.store', $event->id) }}" method="POST" class="mt-6 space-y-6">
            @csrf
            
            <!-- Nama Tiket -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2.5">
                    Nama Tiket
                </label>
                <input type="text" name="name" required placeholder="Contoh: VIP, Reguler" 
                    class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700 placeholder-gray-300">
            </div>
 
            <!-- Harga & Kuota side-by-side -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2.5">
                        Harga (Rupiah)
                    </label>
                    <input type="number" name="price" required placeholder="Contoh: 150000" 
                        class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700 placeholder-gray-300">
                </div>
 
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2.5">
                        Kuota Tiket
                    </label>
                    <input type="number" name="quota" required placeholder="Contoh: 100" 
                        class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700 placeholder-gray-300">
                </div>
            </div>
 
            <!-- Deskripsi Tiket -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2.5">
                    Deskripsi Tiket
                </label>
                <textarea name="description" rows="4" placeholder="Benefit tiket..." 
                    class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700 placeholder-gray-300"></textarea>
            </div>
 
            <!-- Aktifkan Tiket Langsung Checkbox -->
            <div class="flex items-center">
                <label class="flex items-center gap-3 text-gray-700 font-bold text-sm cursor-pointer select-none">
                    <input type="checkbox" name="is_active" value="1" checked 
                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span>Aktifkan Tiket Langsung</span>
                </label>
            </div>
 
            <!-- Action buttons -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <button type="button" onclick="closeAddTicketModal()" 
                    class="bg-gray-100 hover:bg-gray-200 text-gray-650 px-6 py-3 rounded-2xl font-bold text-sm transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold text-sm transition cursor-pointer">
                    Simpan
                </button>
            </div>
 
        </form>
 
    </div>
</div>
 
<!-- JavaScript: Open/Close popup and confirm delete actions -->
<script>
    function openAddTicketModal() {
        const modal = document.getElementById('addTicketModal');
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                const card = modal.querySelector('.relative');
                if (card) {
                    card.classList.remove('scale-95');
                    card.classList.add('scale-100');
                }
            }, 50);
        }
    }
 
    function closeAddTicketModal() {
        const modal = document.getElementById('addTicketModal');
        if (modal) {
            const card = modal.querySelector('.relative');
            if (card) {
                card.classList.remove('scale-100');
                card.classList.add('scale-95');
            }
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }
    }
 
    function triggerDelete(ticketId, ticketName) {
        if (confirm(`Apakah Anda yakin ingin menghapus kategori tiket "${ticketName}"? Tindakan ini tidak dapat dibatalkan.`)) {
            const form = document.getElementById(`delete-form-${ticketId}`);
            if (form) {
                form.submit();
            }
        }
    }
</script>
 
@endsection