@extends('admin.layouts.app')
 
@section('title', 'Ticket Categories')
 
@section('content')
 
<div class="space-y-8">
 
    <div id="dynamicAlertContainer" class="space-y-4"></div>
    <div id="sessionAlertContainer">
        {{-- Success Notification Alert --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4.5 rounded-2xl flex items-center gap-3 shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-green-600 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif
    </div>

    <!-- Progress Steps Indicator -->
    @php
        $hasFeatures = $event->has_certificate || $event->has_seat_layout || $event->has_lucky_draw;
        $eventCapacityLimit = (int) ($event->getAttributes()['capacity'] ?? 0);
        $currentTotalQuota = $tickets->sum('quota');
        $remainingQuotaLimit = max(0, $eventCapacityLimit - $currentTotalQuota);
    @endphp
    <div class="bg-white rounded-2xl p-6 border border-gray-100/80 shadow-sm flex items-center justify-between max-w-4xl mx-auto w-full">
        <!-- Step 1: Create Event -->
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                ✓
            </div>
            <div class="hidden sm:block">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Step 1</p>
                <h4 class="text-xs font-bold text-gray-700">Create Event</h4>
            </div>
        </div>

        <!-- Line -->
        <div class="flex-1 h-0.5 bg-blue-100 mx-4 sm:mx-6"></div>

        <!-- Step 2: Tickets -->
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-sm ring-4 ring-blue-100">
                2
            </div>
            <div>
                <p class="text-[10px] text-blue-500 font-bold uppercase tracking-wider">Step 2</p>
                <h4 class="text-xs font-bold text-blue-655">Tickets</h4>
            </div>
        </div>

        <!-- Line -->
        <div class="flex-1 h-0.5 bg-gray-100 mx-4 sm:mx-6"></div>

        @if($hasFeatures)
            <!-- Step 3: Features -->
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 font-bold text-sm">
                    3
                </div>
                <div class="hidden sm:block">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Step 3</p>
                    <h4 class="text-xs font-bold text-gray-400">Features</h4>
                </div>
            </div>

            <!-- Line -->
            <div class="flex-1 h-0.5 bg-gray-100 mx-4 sm:mx-6"></div>
        @endif

        <!-- Step 4: Complete -->
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 font-bold text-sm">
                ★
            </div>
            <div class="hidden sm:block">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Step 4</p>
                <h4 class="text-xs font-bold text-gray-400">Complete</h4>
            </div>
        </div>
    </div>
 
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
                <tbody id="ticketTableBody" class="divide-y divide-gray-50">
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

        <!-- Next / Finish Action Buttons -->
        <div class="mt-10 pt-8 border-t border-gray-100">
            <div class="flex justify-end flex-wrap gap-4">
                @if($hasFeatures)
                    <a href="{{ route('admin.events.features', $event->id) }}" id="nextStepButton"
                       class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-sm shadow-md transition duration-200 flex items-center gap-2 {{ $tickets->isEmpty() ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:-translate-y-0.5' }}">
                        <span>Next: Feature Setup</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('admin.events.finish', $event->id) }}" id="nextStepButton"
                       class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-sm shadow-md transition duration-200 flex items-center gap-2 {{ $tickets->isEmpty() ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:-translate-y-0.5' }}">
                        <span>Finish Setup</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </a>
                @endif
            </div>
            <p id="ticketRequirementHint" class="text-xs text-red-500 font-semibold mt-2.5 text-right transition duration-200 {{ $tickets->isEmpty() ? '' : 'hidden' }}">
                * Tambahkan minimal satu kategori tiket untuk melanjutkan.
            </p>
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
 
        <!-- Quota Information Panel -->
        <div class="mt-5 bg-blue-50/50 rounded-2xl p-4.5 border border-blue-100/50 grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Kapasitas Event</p>
                <p class="text-lg font-extrabold text-gray-700 mt-1" id="uiCapacityVal">{{ number_format($eventCapacityLimit) }}</p>
            </div>
            <div class="border-x border-blue-100/30">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Kuota Terpakai</p>
                <p class="text-lg font-extrabold text-blue-600 mt-1" id="uiUsedQuotaVal">{{ number_format($currentTotalQuota) }}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Sisa Kuota</p>
                <p class="text-lg font-extrabold text-emerald-600 mt-1" id="uiRemainingQuotaVal">{{ number_format($remainingQuotaLimit) }}</p>
            </div>
        </div>

        <!-- Modal Form Input Fields -->
        <form id="addTicketForm" action="{{ route('admin.tickets.store', $event->id) }}" method="POST" onsubmit="submitTicketForm(event)" class="mt-6 space-y-6">
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
                    <input type="number" name="quota" id="ticketQuotaInput" required placeholder="Contoh: 100" 
                        class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700 placeholder-gray-300">
                    <p class="text-xs text-gray-400 font-semibold mt-1.5" id="quotaMaxLimitHint">
                        Maksimal kuota yang dapat ditambahkan: {{ number_format($remainingQuotaLimit) }}
                    </p>
                    <p id="quotaFrontendError" class="text-red-500 text-xs font-semibold mt-1 hidden"></p>
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
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 flex-wrap">
                <button type="button" onclick="closeAddTicketModal()" 
                    class="bg-gray-100 hover:bg-gray-200 text-gray-650 px-6 py-3 rounded-2xl font-bold text-sm transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" onclick="keepModalOpen = true"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-bold text-sm transition cursor-pointer">
                    Simpan & Tambah Lagi
                </button>
                <button type="submit" onclick="keepModalOpen = false"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold text-sm transition cursor-pointer">
                    Simpan
                </button>
            </div>
 
        </form>
 
    </div>
</div>
 
<!-- JavaScript: Open/Close popup and confirm delete actions -->
<script>
    let keepModalOpen = false;
    const csrfToken = "{{ csrf_token() }}";
    const destroyRouteUrlTemplate = "{{ route('admin.tickets.destroy', 'TICKET_ID') }}";
    let eventCapacity = {{ $eventCapacityLimit }};
    let usedQuota = {{ $currentTotalQuota }};
    let ticketCount = {{ $tickets->count() }};

    function openAddTicketModal() {
        const modal = document.getElementById('addTicketModal');
        if (modal) {
            // Reset form
            const form = document.getElementById('addTicketForm');
            if (form) {
                form.reset();
                form.querySelectorAll('.error-message').forEach(el => el.remove());
                const quotaInput = document.getElementById('ticketQuotaInput');
                if (quotaInput) {
                    quotaInput.classList.remove('border-red-500');
                    quotaInput.classList.add('border-gray-200');
                }
                const errorEl = document.getElementById('quotaFrontendError');
                if (errorEl) {
                    errorEl.classList.add('hidden');
                }
                const submitButtons = form.querySelectorAll('button[type="submit"]');
                submitButtons.forEach(btn => {
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                });
            }

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

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function formatNumber(value) {
        return new Intl.NumberFormat('en-US').format(value);
    }

    function generateTicketRowHtml(ticket) {
        const destroyUrl = destroyRouteUrlTemplate.replace('TICKET_ID', ticket.id);
        const playerEscapedName = escapeHtml(ticket.name);
        const escapedNameForJs = playerEscapedName.replace(/'/g, "\\'");
        
        let descHtml = '';
        if (ticket.description) {
            descHtml = `
                <p class="text-gray-400 text-xs mt-1.5 line-clamp-1 max-w-[280px] font-medium">
                    ${escapeHtml(ticket.description)}
                </p>
            `;
        }

        let statusHtml = '';
        if (ticket.is_active == 1 || ticket.is_active === true) {
            statusHtml = `
                <span class="bg-green-50 text-green-600 border border-green-100/50 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide">
                    Active
                </span>
            `;
        } else {
            statusHtml = `
                <span class="bg-gray-50 text-gray-400 border border-gray-150 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide">
                    Inactive
                </span>
            `;
        }

        return `
            <tr class="hover:bg-gray-50/30 transition duration-150">
                <!-- Ticket Info -->
                <td class="py-5 pr-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0 border border-blue-100/30">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-blue-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-12v.75m0 3v.75m0 3v.75m0 3V18M3 8.25a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 8.25V15.75a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15.75V8.25Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-gray-800 text-base leading-snug">
                                ${playerEscapedName}
                            </h3>
                            ${descHtml}
                        </div>
                    </div>
                </td>

                <!-- Price -->
                <td class="py-5 pl-4 pr-4">
                    <span class="text-gray-650 font-bold text-sm">
                        Rp ${formatNumber(ticket.price)}
                    </span>
                </td>

                <!-- Quota -->
                <td class="py-5 pl-4 pr-4">
                    <span class="text-gray-800 font-extrabold text-sm">
                        ${formatNumber(ticket.quota)}
                    </span>
                </td>

                <!-- Status -->
                <td class="py-5 pl-4 pr-4">
                    ${statusHtml}
                </td>

                <!-- Action -->
                <td class="py-5">
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="triggerDelete('${ticket.id}', '${escapedNameForJs}')" 
                            class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-xl text-xs font-bold tracking-wide transition flex items-center gap-1.5 border border-red-100/50 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-red-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            <span>Delete</span>
                        </button>

                        <form id="delete-form-${ticket.id}" action="${destroyUrl}" method="POST" class="hidden">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                        </form>
                    </div>
                </td>
            </tr>
        `;
    }

    function showSuccessAlert(message) {
        const container = document.getElementById('dynamicAlertContainer');
        if (!container) return;
        
        container.innerHTML = '';
        
        const alertHtml = `
            <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4.5 rounded-2xl flex items-center gap-3 shadow-sm transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-green-600 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span class="font-bold text-sm">${escapeHtml(message)}</span>
            </div>
        `;
        container.innerHTML = alertHtml;
        
        const sessionAlert = document.getElementById('sessionAlertContainer');
        if (sessionAlert) {
            sessionAlert.innerHTML = '';
        }
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function submitTicketForm(event) {
        event.preventDefault();
        
        const form = event.target;
        const quotaInput = form.querySelector('[name="quota"]');
        if (quotaInput) {
            const remaining = Math.max(0, eventCapacity - usedQuota);
            const value = parseInt(quotaInput.value) || 0;
            if (value > remaining) {
                validateQuotaInput();
                return;
            }
        }

        const formData = new FormData(form);
        
        const submitButtons = form.querySelectorAll('button[type="submit"]');
        submitButtons.forEach(btn => {
            btn.disabled = true;
            btn.style.opacity = '0.7';
        });
        
        form.querySelectorAll('.error-message').forEach(el => el.remove());
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok && response.status !== 422) {
                throw new Error('Terjadi kesalahan. Silakan coba lagi.');
            }
            return response.json();
        })
        .then(data => {
            submitButtons.forEach(btn => {
                btn.disabled = false;
                btn.style.opacity = '1';
            });
            
            if (data.errors) {
                Object.keys(data.errors).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        const errorEl = document.createElement('p');
                        errorEl.className = 'text-red-500 text-xs font-semibold mt-1 error-message';
                        errorEl.innerText = data.errors[key][0];
                        input.parentNode.appendChild(errorEl);
                    }
                });
                return;
            }
            
            if (data.success) {
                // Update used quota and UI
                const addedQuota = parseInt(data.ticket.quota) || 0;
                usedQuota += addedQuota;
                ticketCount++;
                updateQuotaInfoUI();
                updateNextButtonState();

                const tableBody = document.getElementById('ticketTableBody');
                if (tableBody) {
                    const emptyRow = tableBody.querySelector('td[colspan="5"]');
                    if (emptyRow) {
                        emptyRow.closest('tr').remove();
                    }
                    
                    const newRowHtml = generateTicketRowHtml(data.ticket);
                    tableBody.insertAdjacentHTML('beforeend', newRowHtml);
                }
                
                showSuccessAlert(data.message);
                
                if (keepModalOpen) {
                    form.reset();
                    updateQuotaInfoUI();
                    const firstInput = form.querySelector('input[name="name"]');
                    if (firstInput) firstInput.focus();
                } else {
                    closeAddTicketModal();
                    form.reset();
                }
            }
        })
        .catch(error => {
            submitButtons.forEach(btn => {
                btn.disabled = false;
                btn.style.opacity = '1';
            });
            alert(error.message || 'Gagal menyimpan ticket category.');
        });
    }

    function updateQuotaInfoUI() {
        const capacityEl = document.getElementById('uiCapacityVal');
        const usedQuotaEl = document.getElementById('uiUsedQuotaVal');
        const remainingQuotaEl = document.getElementById('uiRemainingQuotaVal');
        const limitHintEl = document.getElementById('quotaMaxLimitHint');
        const quotaInput = document.getElementById('ticketQuotaInput');

        const remaining = Math.max(0, eventCapacity - usedQuota);

        if (capacityEl) capacityEl.innerText = formatNumber(eventCapacity);
        if (usedQuotaEl) usedQuotaEl.innerText = formatNumber(usedQuota);
        
        if (remainingQuotaEl) {
            remainingQuotaEl.innerText = formatNumber(remaining);
            if (remaining <= 0) {
                remainingQuotaEl.className = "text-lg font-extrabold text-red-650 mt-1";
            } else {
                remainingQuotaEl.className = "text-lg font-extrabold text-emerald-600 mt-1";
            }
        }
        
        if (limitHintEl) {
            limitHintEl.innerText = `Maksimal kuota yang dapat ditambahkan: ${formatNumber(remaining)}`;
        }
        
        // Trigger verification on the input to toggle errors immediately
        if (quotaInput) {
            validateQuotaInput();
        }
    }

    function validateQuotaInput() {
        const quotaInput = document.getElementById('ticketQuotaInput');
        const errorEl = document.getElementById('quotaFrontendError');
        const submitButtons = document.querySelectorAll('#addTicketForm button[type="submit"]');
        
        if (!quotaInput || !errorEl) return;
        
        const remaining = Math.max(0, eventCapacity - usedQuota);
        const value = parseInt(quotaInput.value) || 0;
        
        if (value > remaining) {
            errorEl.innerText = `Total kuota tiket tidak boleh melebihi kapasitas event (${formatNumber(eventCapacity)} peserta). Sisa kuota tersedia: ${formatNumber(remaining)}.`;
            errorEl.classList.remove('hidden');
            quotaInput.classList.add('border-red-500');
            quotaInput.classList.remove('border-gray-200');
            
            // Disable submit buttons and add visual disabled classes
            submitButtons.forEach(btn => {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            });
        } else {
            errorEl.classList.add('hidden');
            quotaInput.classList.remove('border-red-500');
            quotaInput.classList.add('border-gray-200');
            
            // Enable submit buttons and remove visual disabled classes
            submitButtons.forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        }
    }

    function updateNextButtonState() {
        const nextBtn = document.getElementById('nextStepButton');
        const hintEl = document.getElementById('ticketRequirementHint');
        if (!nextBtn) return;
        if (ticketCount === 0) {
            nextBtn.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
            nextBtn.classList.remove('hover:-translate-y-0.5');
            if (hintEl) hintEl.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
            nextBtn.classList.add('hover:-translate-y-0.5');
            if (hintEl) hintEl.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const quotaInput = document.getElementById('ticketQuotaInput');
        if (quotaInput) {
            quotaInput.addEventListener('input', validateQuotaInput);
            quotaInput.addEventListener('change', validateQuotaInput);
        }
        updateNextButtonState();
    });
</script>
 
@endsection