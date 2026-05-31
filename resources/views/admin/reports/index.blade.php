@extends('admin.layouts.app')

@section('title', 'Reports')

@section('content')
<!-- Include Client-Side Export Libraries -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<div class="space-y-8 relative">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 pb-2">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight flex items-center gap-3">
                <span>📊 Reports</span>
            </h1>
            <p class="text-gray-400 text-sm mt-2 font-semibold">
                Lihat laporan akhir dan performa event yang telah selesai.
            </p>
        </div>

        @if(!$finishedEvents->isEmpty())
        <!-- Export Buttons (Hidden if no event selected) -->
        <div id="exportButtons" class="hidden flex items-center gap-3 w-full md:w-auto">
            <button onclick="exportToExcel()" class="px-5 h-[46px] bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold text-xs shadow-md hover:shadow-lg transition flex items-center justify-center gap-2 cursor-pointer flex-1 md:flex-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12-3-3m0 0-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <span>Export Excel</span>
            </button>

            <button onclick="exportToPDF()" class="px-5 h-[46px] bg-red-600 hover:bg-red-700 text-white rounded-2xl font-bold text-xs shadow-md hover:shadow-lg transition flex items-center justify-center gap-2 cursor-pointer flex-1 md:flex-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <span>Export PDF</span>
            </button>
        </div>
        @endif
    </div>

    @if($finishedEvents->isEmpty())
        <!-- Empty State: No Finished Events -->
        <div class="bg-white rounded-[32px] border border-gray-100/80 p-20 text-center flex flex-col items-center justify-center shadow-sm w-full">
            <div class="w-20 h-20 rounded-3xl bg-amber-50 flex items-center justify-center text-amber-500 mb-6 border border-amber-100/50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
            </div>
            <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">Belum ada event selesai yang dapat dilaporkan.</h3>
        </div>
    @else
        <!-- Finished Event Selector dropdown -->
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <div class="space-y-2">
                <label for="eventSelector" class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Pilih Event Selesai</label>
                <select id="eventSelector" onchange="loadEventReport(this.value)" class="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    <option value="" disabled selected>-- Pilih Event Selesai --</option>
                    @foreach($finishedEvents as $e)
                        <option value="{{ $e->id }}">{{ $e->name }} ({{ \Carbon\Carbon::parse($e->start_date)->format('d M Y') }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Main Report Dashboard Section (Hidden until an event is selected) -->
        <div id="reportContent" class="hidden space-y-8 relative">
            
            <!-- Global spinner overlay when loading single event data -->
            <div id="reportLoadingOverlay" class="absolute inset-0 bg-white/70 backdrop-blur-xs z-50 flex items-center justify-center transition-opacity duration-300 opacity-0 pointer-events-none rounded-3xl">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm font-bold text-gray-500">Retrieving report details...</p>
                </div>
            </div>

            <!-- Top Grid Summaries -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Card 1: Event Information (Basic Info) -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex flex-col justify-between transition hover:shadow-md hover:border-blue-200/85 duration-300">
                    <div class="space-y-4">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest text-blue-500">
                            📋 Event Info
                        </p>
                        
                        <div class="space-y-3 pt-1">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase block">Nama Event</span>
                                <h3 id="infoEventName" class="text-sm font-extrabold text-gray-800 line-clamp-2 leading-tight">Name</h3>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase block">Kategori</span>
                                <span id="infoEventCategory" class="inline-block mt-0.5 bg-purple-50 text-purple-600 border border-purple-100 px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wide">Category</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase block">Tanggal Event</span>
                                <span id="infoEventDate" class="text-xs font-semibold text-gray-600">Date</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase block">Lokasi</span>
                                <span id="infoEventLocation" class="text-xs font-semibold text-gray-600 line-clamp-1">Location</span>
                            </div>
                            <div class="pt-1">
                                <span class="bg-slate-100 text-slate-700 border border-slate-200/60 px-3 py-1 rounded-full text-[10px] font-extrabold tracking-wider uppercase">
                                    Finished
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Participant Summary -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex flex-col justify-between transition hover:shadow-md hover:border-blue-200/85 duration-300">
                    <div class="space-y-4">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest text-blue-500">
                            👥 Participant Summary
                        </p>

                        <div class="space-y-3 pt-1 text-xs">
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-50">
                                <span class="font-semibold text-gray-500">Total Registrations</span>
                                <span id="partTotalRegistrations" class="font-bold text-gray-800">0</span>
                            </div>
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-50">
                                <span class="font-semibold text-gray-500">Total Check-In</span>
                                <span id="partTotalCheckIn" class="font-bold text-green-600">0</span>
                            </div>
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-50">
                                <span class="font-semibold text-gray-500">Total Not Check-In</span>
                                <span id="partTotalNotCheckIn" class="font-bold text-red-500">0</span>
                            </div>
                            <div class="flex justify-between items-center py-1.5">
                                <span class="font-semibold text-gray-500">Attendance Rate</span>
                                <span id="partAttendancePercentage" class="font-extrabold text-blue-600">0%</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden shadow-inner relative">
                            <div id="partProgressBar" class="bg-gradient-to-r from-blue-500 to-indigo-500 h-full rounded-full transition-all duration-700" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Ticket Summary -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex flex-col justify-between transition hover:shadow-md hover:border-blue-200/85 duration-300">
                    <div class="space-y-4 w-full">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest text-blue-500">
                            🎫 Ticket Summary
                        </p>

                        <!-- Dotted formatted ticket classes listing -->
                        <div id="ticketCategoriesList" class="space-y-2.5 max-h-[140px] overflow-y-auto pt-1 text-xs">
                            <!-- Injected programmatically, example: VIP ............... 20 Tiket -->
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-between items-center text-xs">
                        <span class="font-semibold text-gray-400">Total Terjual</span>
                        <span id="ticketTotalSold" class="font-bold text-gray-800">0 Tiket</span>
                    </div>
                </div>

                <!-- Card 4: Revenue Summary -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100/80 shadow-sm flex flex-col justify-between transition hover:shadow-md hover:border-blue-200/85 duration-300">
                    <div class="space-y-4">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest text-blue-500">
                            💰 Revenue Summary
                        </p>

                        <div class="space-y-3 pt-1 text-xs">
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-50">
                                <span class="font-semibold text-gray-500">Total Revenue</span>
                                <span id="revTotalRevenue" class="font-bold text-gray-800">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-50">
                                <span class="font-semibold text-gray-500">Average Ticket</span>
                                <span id="revAverageTicketPrice" class="font-bold text-blue-600">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center py-1.5">
                                <span class="font-semibold text-gray-500">Tickets Sold</span>
                                <span id="revTicketsSold" class="font-bold text-gray-800">0</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Financial report</span>
                        <span class="bg-emerald-50 text-emerald-600 border border-emerald-100 px-2 py-0.5 rounded text-[8px] font-bold">REALTIME</span>
                    </div>
                </div>

            </div>

            <!-- Participant Report Table Card -->
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden p-8 relative">
                
                <!-- Table Loading indicator inside Card -->
                <div id="tableLoadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-xs z-30 flex items-center justify-center transition-opacity duration-300 opacity-0 pointer-events-none">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </div>

                <!-- Table Header Controls -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 pb-6 border-b border-gray-100 mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Participant Report</h2>
                        <p class="text-gray-400 text-xs font-semibold mt-1">Daftar kehadiran peserta event</p>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-64 h-[42px] w-full md:w-64">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input 
                            type="text" 
                            id="tableSearch" 
                            oninput="handleSearch(this.value)" 
                            placeholder="Search Participant..." 
                            class="border border-gray-200/80 bg-gray-50/50 rounded-full pl-11 pr-4 w-full h-full focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-semibold text-gray-700 placeholder-gray-400"
                        >
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto min-h-[220px]">
                    <table class="w-full">
                        <thead class="text-gray-400 uppercase text-[10px] font-bold tracking-wider border-b border-gray-150">
                            <tr>
                                <th class="text-left pb-4">Nama Peserta</th>
                                <th class="text-left pb-4">Email</th>
                                <th class="text-left pb-4">Kategori Tiket</th>
                                <th class="text-left pb-4">Status Kehadiran</th>
                            </tr>
                        </thead>

                        <tbody id="tableBody" class="divide-y divide-gray-50 text-sm">
                            <!-- Injected dynamically -->
                        </tbody>
                    </table>

                    <!-- Table Empty State -->
                    <div id="tableEmptyState" class="hidden py-16 text-center text-gray-400">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                </svg>
                            </div>
                            <div class="font-bold text-sm text-gray-600">Peserta tidak ditemukan</div>
                            <div class="text-xs font-semibold text-gray-400">Kami tidak dapat menemukan nama peserta tersebut.</div>
                        </div>
                    </div>
                </div>

                <!-- Table Footer / Pagination -->
                <div id="tablePagination" class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-gray-50 mt-4">
                    <span id="paginationInfo" class="text-xs font-semibold text-gray-400">Showing 0 to 0 of 0 participants</span>
                    
                    <div class="flex items-center gap-2">
                        <button id="btnPrev" onclick="changePage(-1)" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-xs font-bold text-gray-500 transition disabled:opacity-50 disabled:pointer-events-none cursor-pointer">
                            Prev
                        </button>
                        <button id="btnNext" onclick="changePage(1)" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-xs font-bold text-gray-500 transition disabled:opacity-50 disabled:pointer-events-none cursor-pointer">
                            Next
                        </button>
                    </div>
                </div>

            </div>

        </div>
    @endif

</div>

<!-- JavaScript Logic -->
<script>
    // State variables
    let reportData = null;
    let filteredParticipants = [];
    let searchQuery = '';
    let currentPage = 1;
    const pageSize = 10;

    document.addEventListener('DOMContentLoaded', () => {
        // Automatically select the first event and trigger loading if any exist
        const selector = document.getElementById('eventSelector');
        if (selector && selector.options.length > 1) {
            selector.selectedIndex = 1;
            loadEventReport(selector.value);
        }
    });

    /**
     * Fetch report details for the selected finished event
     */
    function loadEventReport(eventId) {
        if (!eventId) return;

        // Show loading state
        toggleLoading(true);
        document.getElementById('reportContent').classList.add('hidden');
        document.getElementById('exportButtons').classList.add('hidden');

        fetch(`/admin/reports/data/${eventId}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    reportData = res;
                    applyFilters();
                    
                    // Unhide content panels
                    document.getElementById('reportContent').classList.remove('hidden');
                    document.getElementById('exportButtons').classList.remove('hidden');
                } else {
                    alert(res.message || 'Gagal memuat laporan event.');
                }
            })
            .catch(err => {
                console.error('Network error fetching finished event report:', err);
                alert('Gagal memuat data laporan dari server.');
            })
            .finally(() => {
                toggleLoading(false);
            });
    }

    /**
     * Toggle loader overlays
     */
    function toggleLoading(isLoading) {
        const overlay = document.getElementById('reportLoadingOverlay');
        if (!overlay) return;
        if (isLoading) {
            overlay.classList.remove('pointer-events-none');
            overlay.classList.add('opacity-100');
        } else {
            overlay.classList.add('pointer-events-none');
            overlay.classList.remove('opacity-100');
        }
    }

    /**
     * Apply filter constraints and triggers UI updates
     */
    function applyFilters() {
        if (!reportData) return;

        // Filter participants by search query
        filteredParticipants = reportData.participants.filter(p => {
            if (!searchQuery) return true;
            return p.name.toLowerCase().includes(searchQuery) || 
                   p.email.toLowerCase().includes(searchQuery) || 
                   p.ticket_category.toLowerCase().includes(searchQuery);
        });

        // Update card details
        updateCardMetrics();

        // Render table rows
        currentPage = 1;
        renderTable();
    }

    /**
     * Bind variables to HTML Cards
     */
    function updateCardMetrics() {
        if (!reportData) return;

        // Basic Info
        document.getElementById('infoEventName').textContent = reportData.event.name;
        document.getElementById('infoEventCategory').textContent = reportData.event.category;
        document.getElementById('infoEventDate').textContent = reportData.event.start_date;
        document.getElementById('infoEventLocation').textContent = reportData.event.location;

        // Participant Summary
        document.getElementById('partTotalRegistrations').textContent = reportData.participant_summary.total_registrations.toLocaleString('id-ID');
        document.getElementById('partTotalCheckIn').textContent = reportData.participant_summary.total_check_in.toLocaleString('id-ID');
        document.getElementById('partTotalNotCheckIn').textContent = reportData.participant_summary.total_not_check_in.toLocaleString('id-ID');
        
        const attendance = reportData.participant_summary.attendance_percentage;
        document.getElementById('partAttendancePercentage').textContent = `${attendance}%`;
        document.getElementById('partProgressBar').style.width = `${attendance}%`;

        // Ticket Summary (dotted lines)
        const ticketListContainer = document.getElementById('ticketCategoriesList');
        ticketListContainer.innerHTML = '';
        
        if (reportData.ticket_summary.categories.length === 0) {
            ticketListContainer.innerHTML = '<p class="text-gray-400 italic">No tickets configured</p>';
        } else {
            reportData.ticket_summary.categories.forEach(cat => {
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between text-xs font-semibold text-gray-500';
                div.innerHTML = `
                    <span>${escapeHtml(cat.name)}</span>
                    <span class="flex-grow border-b border-dotted border-gray-300 mx-2 mb-1.5"></span>
                    <span class="text-gray-800 font-bold">${cat.sold} Tiket</span>
                `;
                ticketListContainer.appendChild(div);
            });
        }
        document.getElementById('ticketTotalSold').textContent = `${reportData.ticket_summary.total_tickets_sold} Tiket`;

        // Revenue Summary
        document.getElementById('revTotalRevenue').textContent = formatRupiah(reportData.revenue_summary.total_revenue);
        document.getElementById('revAverageTicketPrice').textContent = formatRupiah(reportData.revenue_summary.average_ticket_price);
        document.getElementById('revTicketsSold').textContent = reportData.revenue_summary.tickets_sold.toLocaleString('id-ID');
    }

    /**
     * Render the paginated participant report inside the HTML Table body
     */
    function renderTable() {
        const tableBody = document.getElementById('tableBody');
        const emptyState = document.getElementById('tableEmptyState');
        const pagination = document.getElementById('tablePagination');
        
        tableBody.innerHTML = '';

        if (filteredParticipants.length === 0) {
            emptyState.classList.remove('hidden');
            pagination.classList.add('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        pagination.classList.remove('hidden');

        // Math pagination limits
        const totalItems = filteredParticipants.length;
        const totalPages = Math.ceil(totalItems / pageSize);
        if (currentPage > totalPages) currentPage = Math.max(1, totalPages);

        const startIdx = (currentPage - 1) * pageSize;
        const endIdx = Math.min(startIdx + pageSize, totalItems);
        const paginatedData = filteredParticipants.slice(startIdx, endIdx);

        // Update footer info
        document.getElementById('paginationInfo').textContent = `Showing ${startIdx + 1} to ${endIdx} of ${totalItems} participants`;
        document.getElementById('btnPrev').disabled = currentPage === 1;
        document.getElementById('btnNext').disabled = currentPage === totalPages;

        // Render rows
        paginatedData.forEach(p => {
            let statusPillClass = 'bg-slate-100 text-slate-700 border border-slate-200/60';
            if (p.is_checked_in.toLowerCase() === 'hadir') {
                statusPillClass = 'bg-green-50 text-green-600 border border-green-100/50';
            }

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/50 transition duration-150';
            tr.innerHTML = `
                <td class="py-4 font-bold text-gray-800">${escapeHtml(p.name)}</td>
                <td class="py-4 text-gray-500 font-semibold">${escapeHtml(p.email)}</td>
                <td class="py-4 text-gray-500 font-semibold">${escapeHtml(p.ticket_category)}</td>
                <td class="py-4">
                    <span class="${statusPillClass} px-3 py-1 rounded-full text-xs font-bold tracking-wide whitespace-nowrap">
                        ${p.is_checked_in}
                    </span>
                </td>
            `;
            tableBody.appendChild(tr);
        });
    }

    /**
     * Search trigger
     */
    function handleSearch(value) {
        searchQuery = value.toLowerCase().trim();
        applyFilters();
    }

    /**
     * Pagination navigation
     */
    function changePage(direction) {
        currentPage += direction;
        renderTable();
    }

    /**
     * Format name for file exports
     */
    function getEventFilename(name, ext) {
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
        return `joyvent-report-${slug}.${ext}`;
    }

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    /**
     * Export spreadsheet via SheetJS (XLSX format)
     */
    function exportToExcel() {
        if (!reportData) return;

        const wb = XLSX.utils.book_new();

        // Sheet 1: Summary Sheet
        const summaryRows = [
            ["JoyVent Finished Event Evaluation Report"],
            [],
            ["INFORMASI EVENT"],
            ["Nama Event", reportData.event.name],
            ["Kategori", reportData.event.category],
            ["Tanggal Event", reportData.event.start_date],
            ["Lokasi", reportData.event.location],
            ["Status", "Finished"],
            [],
            ["PARTICIPANT SUMMARY"],
            ["Total Registrations", reportData.participant_summary.total_registrations],
            ["Total Check-In", reportData.participant_summary.total_check_in],
            ["Total Not Check-In", reportData.participant_summary.total_not_check_in],
            ["Attendance Percentage", `${reportData.participant_summary.attendance_percentage}%`],
            [],
            ["TICKET SUMMARY"],
            ...reportData.ticket_summary.categories.map(c => [c.name, `${c.sold} Tiket`]),
            ["Total Tiket Terjual", reportData.ticket_summary.total_tickets_sold],
            [],
            ["REVENUE SUMMARY"],
            ["Total Revenue", reportData.revenue_summary.total_revenue],
            ["Average Ticket Price", reportData.revenue_summary.average_ticket_price],
            ["Tickets Sold", reportData.revenue_summary.tickets_sold]
        ];

        const wsSummary = XLSX.utils.aoa_to_sheet(summaryRows);
        XLSX.utils.book_append_sheet(wb, wsSummary, "Evaluation Summary");

        // Sheet 2: Participant List Sheet
        const participantHeaders = [["Nama Peserta", "Email", "Kategori Tiket", "Status Kehadiran"]];
        const participantRows = reportData.participants.map(p => [
            p.name,
            p.email,
            p.ticket_category,
            p.is_checked_in
        ]);

        const wsParticipants = XLSX.utils.aoa_to_sheet(participantHeaders.concat(participantRows));
        XLSX.utils.book_append_sheet(wb, wsParticipants, "Participant List");

        // Export
        XLSX.writeFile(wb, getEventFilename(reportData.event.name, 'xlsx'));
    }

    /**
     * Export reports PDF document with jsPDF and AutoTable styling
     */
    function exportToPDF() {
        if (!reportData) return;

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Title and Document Header
        doc.setFontSize(22);
        doc.setFont("helvetica", "bold");
        doc.text("JoyVent Finished Event Evaluation", 14, 20);

        doc.setFontSize(10);
        doc.setFont("helvetica", "normal");
        doc.setTextColor(100);
        doc.text(`Tanggal Cetak: ${new Date().toLocaleDateString('id-ID')}`, 14, 27);
        doc.setTextColor(0);

        // Section 1: Event Information
        doc.setFontSize(14);
        doc.setFont("helvetica", "bold");
        doc.text("1. Informasi Event", 14, 38);

        doc.autoTable({
            startY: 42,
            head: [['Detail', 'Keterangan']],
            body: [
                ['Nama Event', reportData.event.name],
                ['Kategori', reportData.event.category],
                ['Tanggal Event', reportData.event.start_date],
                ['Lokasi', reportData.event.location],
                ['Status', 'Finished']
            ],
            theme: 'striped',
            headStyles: { fillColor: [59, 130, 246] }
        });

        // Section 2: Participant & Ticket Summaries
        doc.setFontSize(14);
        doc.setFont("helvetica", "bold");
        doc.text("2. Ringkasan Peserta & Tiket", 14, doc.lastAutoTable.finalY + 12);

        const categoriesString = reportData.ticket_summary.categories.map(c => `${c.name}: ${c.sold} Tiket`).join(', ');

        doc.autoTable({
            startY: doc.lastAutoTable.finalY + 16,
            head: [['Metrik', 'Nilai']],
            body: [
                ['Total Registrations', reportData.participant_summary.total_registrations.toLocaleString('id-ID')],
                ['Total Check-In', reportData.participant_summary.total_check_in.toLocaleString('id-ID')],
                ['Total Not Check-In', reportData.participant_summary.total_not_check_in.toLocaleString('id-ID')],
                ['Attendance Percentage', `${reportData.participant_summary.attendance_percentage}%`],
                ['Tiket Per Kategori', categoriesString],
                ['Total Tiket Terjual', reportData.ticket_summary.total_tickets_sold]
            ],
            theme: 'striped',
            headStyles: { fillColor: [59, 130, 246] }
        });

        // Section 3: Revenue Summary
        doc.setFontSize(14);
        doc.setFont("helvetica", "bold");
        doc.text("3. Revenue Summary", 14, doc.lastAutoTable.finalY + 12);

        doc.autoTable({
            startY: doc.lastAutoTable.finalY + 16,
            head: [['Metrik', 'Nilai']],
            body: [
                ['Total Revenue', formatRupiah(reportData.revenue_summary.total_revenue)],
                ['Average Ticket Price', formatRupiah(reportData.revenue_summary.average_ticket_price)],
                ['Tickets Sold', reportData.revenue_summary.tickets_sold]
            ],
            theme: 'striped',
            headStyles: { fillColor: [59, 130, 246] }
        });

        // Section 4: Participant Report (Page 2)
        doc.addPage();
        doc.setFontSize(18);
        doc.setFont("helvetica", "bold");
        doc.text("4. Participant Report Table", 14, 20);

        const tableBodyData = reportData.participants.map(p => [
            p.name,
            p.email,
            p.ticket_category,
            p.is_checked_in
        ]);

        doc.autoTable({
            startY: 26,
            head: [['Nama Peserta', 'Email', 'Kategori Tiket', 'Status Kehadiran']],
            body: tableBodyData,
            theme: 'striped',
            headStyles: { fillColor: [59, 130, 246] }
        });

        // Save PDF
        doc.save(getEventFilename(reportData.event.name, 'pdf'));
    }
</script>
@endsection
