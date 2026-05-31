<!-- Summary Cards Section -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <!-- Card 1: Pending Refunds -->
    <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center border border-amber-100/50 flex-shrink-0 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-7 h-7">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
        <div>
            <span class="text-[10px] text-gray-400 font-extrabold uppercase tracking-wider block">Pending Refunds</span>
            <span id="stat-refunds-pending" class="text-2xl font-black text-amber-600 block mt-1">{{ number_format($pendingRefundsCount) }}</span>
        </div>
    </div>

    <!-- Card 2: Approved Refunds -->
    <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center border border-green-100/50 flex-shrink-0 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-7 h-7">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
        <div>
            <span class="text-[10px] text-green-550 font-extrabold uppercase tracking-wider block">Approved Refunds</span>
            <span id="stat-refunds-approved" class="text-2xl font-black text-green-600 block mt-1">{{ number_format($approvedRefundsCount) }}</span>
        </div>
    </div>

    <!-- Card 3: Rejected Refunds -->
    <div class="bg-white rounded-3xl p-6 border border-gray-100/80 shadow-sm flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100/50 flex-shrink-0 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-7 h-7">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </div>
        <div>
            <span class="text-[10px] text-rose-500 font-extrabold uppercase tracking-wider block">Rejected Refunds</span>
            <span id="stat-refunds-rejected" class="text-2xl font-black text-rose-650 block mt-1">{{ number_format($rejectedRefundsCount) }}</span>
        </div>
    </div>
</div>

<!-- Main Table and Filter container -->
<div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-10 overflow-hidden space-y-6">
    <div class="pb-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Refund Request Manager</h3>
            <p class="text-xs text-gray-400 font-semibold mt-1">Kelola dan review permintaan pengembalian dana tiket peserta secara terpusat.</p>
        </div>

        <!-- Filter Status Tab Options -->
        <div class="flex items-center gap-1 bg-slate-50 border border-slate-100 p-1 rounded-2xl select-none">
            <button onclick="filterRefunds('all')" id="refund-filter-all" class="refund-filter-btn px-4 py-2 rounded-xl text-xs font-bold transition duration-200 cursor-pointer bg-white text-blue-600 shadow-xs border border-gray-100/30">
                All
            </button>
            <button onclick="filterRefunds('pending')" id="refund-filter-pending" class="refund-filter-btn px-4 py-2 rounded-xl text-xs font-bold transition duration-200 cursor-pointer text-gray-500 hover:text-gray-800">
                Pending
            </button>
            <button onclick="filterRefunds('approved')" id="refund-filter-approved" class="refund-filter-btn px-4 py-2 rounded-xl text-xs font-bold transition duration-200 cursor-pointer text-gray-500 hover:text-gray-800">
                Approved
            </button>
            <button onclick="filterRefunds('rejected')" id="refund-filter-rejected" class="refund-filter-btn px-4 py-2 rounded-xl text-xs font-bold transition duration-200 cursor-pointer text-gray-500 hover:text-gray-800">
                Rejected
            </button>
        </div>
    </div>

    <!-- Refunds List Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="text-gray-400 uppercase text-xs font-bold tracking-wider border-b border-gray-100 pb-3">
                <tr>
                    <th class="text-left pb-3">Participant</th>
                    <th class="text-left pb-3 pl-4">Ticket Type</th>
                    <th class="text-left pb-3 pl-4">Refund Reason</th>
                    <th class="text-left pb-3 pl-4">Request Date</th>
                    <th class="text-left pb-3 pl-4">Status</th>
                    <th class="text-right pb-3 pr-2">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="refundTableBody">
                @forelse($refunds as $refund)
                    <tr class="hover:bg-gray-50/30 transition duration-150 refund-row" data-status="{{ $refund->status }}" data-id="{{ $refund->id }}">
                        <td class="py-4 pr-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-2xl bg-slate-50 border border-slate-100/50 flex items-center justify-center font-black text-xs text-slate-600">
                                    {{ strtoupper(substr($refund->registration->user->name ?? 'G', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="font-bold text-gray-800 text-sm block truncate max-w-[150px]">
                                        {{ $refund->registration->user->name ?? 'Guest' }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-medium block truncate max-w-[150px] mt-0.5">
                                        {{ $refund->registration->user->email ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 pl-4 pr-4">
                            <span class="bg-indigo-50 text-indigo-600 border border-indigo-100/30 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap">
                                {{ $refund->registration->ticketCategory->name ?? '-' }}
                            </span>
                        </td>

                        <td class="py-4 pl-4 pr-4">
                            <span class="text-gray-700 text-xs font-semibold block max-w-[200px] truncate">
                                {{ $refund->reason }}
                            </span>
                        </td>

                        <td class="py-4 pl-4 pr-4">
                            <span class="text-gray-400 text-xs font-semibold whitespace-nowrap">
                                {{ $refund->created_at->format('d M Y') }}
                            </span>
                        </td>

                        <td class="py-4 pl-4 pr-4 refund-status-badge-cell">
                            @if($refund->status === 'pending')
                                <span class="bg-amber-50 text-amber-600 border border-amber-100 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap">
                                    Pending ⏳
                                </span>
                            @elseif($refund->status === 'approved')
                                <span class="bg-green-50 text-green-600 border border-green-150 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap">
                                    Approved 🟢
                                </span>
                            @else
                                <span class="bg-rose-50 text-rose-600 border border-rose-100 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap">
                                    Rejected 🔴
                                </span>
                            @endif
                        </td>

                        <td class="py-4 text-right pr-2">
                            <button onclick="viewRefundDetails({{ json_encode([
                                'id' => $refund->id,
                                'participant_name' => $refund->registration->user->name ?? 'Guest',
                                'participant_email' => $refund->registration->user->email ?? '-',
                                'ticket_type' => $refund->registration->ticketCategory->name ?? '-',
                                'ticket_price' => 'Rp ' . number_format($refund->registration->ticketCategory->price ?? 0, 0, ',', '.'),
                                'reason' => $refund->reason,
                                'additional_notes' => $refund->additional_notes ?? '-',
                                'request_date' => $refund->created_at->format('d M Y, H:i'),
                                'purchase_date' => $refund->registration->created_at->format('d M Y, H:i'),
                                'status' => $refund->status,
                                'approve_url' => route('admin.refunds.approve', $refund->id),
                                'reject_url' => route('admin.refunds.reject', $refund->id),
                            ]) }})" class="bg-blue-50 hover:bg-blue-100/80 text-blue-600 border border-blue-100 px-3.5 py-2 rounded-xl text-xs font-bold transition cursor-pointer select-none">
                                View Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-16 text-gray-400 font-bold text-sm">
                            No refund requests found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail Refund -->
<div id="refundDetailModal" class="fixed inset-0 z-55 hidden items-center justify-center px-4 bg-slate-950/70 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-[32px] p-8 md:p-10 max-w-lg w-full border border-gray-150 shadow-2xl relative overflow-hidden transform scale-95 transition-all duration-300">
        <!-- Close Button -->
        <button onclick="closeRefundModal()" class="absolute top-6 right-6 text-gray-400 hover:text-gray-650 bg-slate-100 hover:bg-slate-200 p-2 rounded-full transition cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-50 border border-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl shadow-sm">
                💵
            </div>
            <h3 class="text-2xl font-black text-gray-800 tracking-tight">Detail Pengajuan Refund</h3>
            <p class="text-xs text-gray-400 font-semibold mt-1">Review detail data transaksi dan alasan pembatalan.</p>
        </div>

        <div class="space-y-5">
            <!-- Section: Participant -->
            <div class="bg-slate-50 border border-slate-100 p-4.5 rounded-2xl">
                <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider block mb-2">Data Peserta</span>
                <div class="font-bold text-gray-800 text-sm" id="modalParticipantName">-</div>
                <div class="text-[10px] text-gray-450 mt-0.5" id="modalParticipantEmail">-</div>
            </div>

            <!-- Grid: Ticket Category & Purchase date -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-slate-50 border border-slate-100 p-4.5 rounded-2xl">
                    <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider block mb-1">Jenis Tiket</span>
                    <span class="font-black text-gray-800 text-xs" id="modalTicketType">-</span>
                </div>
                <div class="bg-slate-50 border border-slate-100 p-4.5 rounded-2xl">
                    <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider block mb-1">Harga Tiket</span>
                    <span class="font-black text-blue-600 text-xs" id="modalTicketPrice">-</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-slate-50 border border-slate-100 p-4.5 rounded-2xl">
                    <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider block mb-1">Tanggal Pembelian</span>
                    <span class="font-semibold text-gray-700 text-xs" id="modalPurchaseDate">-</span>
                </div>
                <div class="bg-slate-50 border border-slate-100 p-4.5 rounded-2xl">
                    <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider block mb-1">Tanggal Pengajuan</span>
                    <span class="font-semibold text-gray-700 text-xs" id="modalRequestDate">-</span>
                </div>
            </div>

            <!-- Section: Reason and Notes -->
            <div class="bg-slate-50 border border-slate-100 p-4.5 rounded-2xl space-y-2">
                <div>
                    <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider block">Alasan Refund</span>
                    <span class="font-semibold text-gray-700 text-xs block mt-1 leading-relaxed" id="modalRefundReason">-</span>
                </div>
                <div class="pt-2 border-t border-slate-200/40">
                    <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider block">Catatan Tambahan</span>
                    <span class="font-semibold text-gray-500 text-xs block mt-1 leading-relaxed" id="modalAdditionalNotes">-</span>
                </div>
            </div>
        </div>

        <!-- Footer Action buttons -->
        <div class="flex gap-3 mt-8 w-full" id="modalActionButtons">
            <!-- Buttons dynamically generated -->
        </div>
    </div>
</div>

<script>
    // Status Filter JS
    function filterRefunds(status) {
        // Toggle Active Filter Tab Style
        document.querySelectorAll('.refund-filter-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-blue-600', 'shadow-xs', 'border', 'border-gray-100/30');
            btn.classList.add('text-gray-500', 'hover:text-gray-800');
        });

        const activeBtn = document.getElementById(`refund-filter-${status}`);
        if (activeBtn) {
            activeBtn.classList.remove('text-gray-500', 'hover:text-gray-800');
            activeBtn.classList.add('bg-white', 'text-blue-600', 'shadow-xs', 'border', 'border-gray-100/30');
        }

        // Show/Hide table rows
        const rows = document.querySelectorAll('.refund-row');
        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            if (status === 'all' || rowStatus === status) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    // Modal Control
    function viewRefundDetails(refund) {
        document.getElementById('modalParticipantName').innerText = refund.participant_name;
        document.getElementById('modalParticipantEmail').innerText = refund.participant_email;
        document.getElementById('modalTicketType').innerText = refund.ticket_type;
        document.getElementById('modalTicketPrice').innerText = refund.ticket_price;
        document.getElementById('modalPurchaseDate').innerText = refund.purchase_date;
        document.getElementById('modalRequestDate').innerText = refund.request_date;
        document.getElementById('modalRefundReason').innerText = refund.reason;
        document.getElementById('modalAdditionalNotes').innerText = refund.additional_notes;

        const actionButtons = document.getElementById('modalActionButtons');
        if (refund.status === 'pending') {
            actionButtons.innerHTML = `
                <form action="${refund.approve_url}" method="POST" class="flex-1">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'}">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-extrabold py-3.5 px-6 rounded-2xl text-xs tracking-wider uppercase transition cursor-pointer shadow-sm select-none">
                        Approve
                    </button>
                </form>
                <form action="${refund.reject_url}" method="POST" class="flex-1">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'}">
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-650 text-white font-extrabold py-3.5 px-6 rounded-2xl text-xs tracking-wider uppercase transition cursor-pointer shadow-sm select-none">
                        Reject
                    </button>
                </form>
            `;
        } else {
            actionButtons.innerHTML = `
                <button onclick="closeRefundModal()" class="w-full bg-slate-100 hover:bg-slate-200 text-gray-700 font-extrabold py-3.5 px-6 rounded-2xl text-xs tracking-wider uppercase transition cursor-pointer shadow-sm">
                    Tutup
                </button>
            `;
        }

        const modal = document.getElementById('refundDetailModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden'; // block scrolling
        }
    }

    function closeRefundModal() {
        const modal = document.getElementById('refundDetailModal');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // allow scrolling
        }
    }

    // Close modal when clicked outside
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('refundDetailModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeRefundModal();
                }
            });
        }
    });
</script>
