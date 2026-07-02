@extends('admin.layouts.app')

@section('title', 'Seat Management Builder')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M19.5 3v11.25a2.25 2.25 0 0 1-2.25 2.25H15M3.75 11.25h16.5M3.75 16.5h16.5M12 3v18M12 21h-3.75m3.75 0H15" />
                    </svg>
                </span>
                Seat Management Builder
            </h2>
            <p class="text-gray-500 mt-1 text-sm">Define seating rules for event <span class="font-extrabold text-gray-700">{{ $event->name }}</span>. The system dynamically generates linear theater rows, classroom table layouts, or curved concert arenas.</p>
        </div>
        
        <div class="flex items-center gap-3 select-none">
            <!-- Mode Toggle Switch -->
            <div class="flex bg-gray-100 p-1 rounded-2xl border border-gray-200/50 mr-1">
                <button id="btn-admin-view" onclick="setViewMode('admin')" class="px-4 py-2 text-xs font-bold rounded-xl transition duration-200 bg-white text-blue-650 shadow-sm">
                    Admin View
                </button>
                <button id="btn-audience-view" onclick="setViewMode('audience')" class="px-4 py-2 text-xs font-bold rounded-xl transition duration-200 text-gray-500 hover:text-gray-800">
                    Audience View
                </button>
            </div>
        </div>
    </div>

    <!-- Main Builder Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        <!-- Canvas Area (Left 3 Columns) -->
        <div id="canvas-column" class="lg:col-span-3 flex flex-col space-y-4">
            <div id="canvas-container" class="bg-slate-50 border border-slate-200 rounded-3xl relative overflow-hidden h-[630px] shadow-inner flex flex-col">
                <!-- Grid background pattern -->
                <div class="absolute inset-0 pointer-events-none opacity-[0.4]" style="background-image: radial-gradient(circle, #cbd5e1 1.5px, transparent 1.5px); background-size: 20px 20px;"></div>

                <!-- Stage Indicator (Theater / Concert / Classroom) -->
                <div id="stage-banner" class="w-full flex justify-center py-4 bg-slate-200/50 border-b border-slate-200/80 relative z-10 select-none">
                    <div class="w-1/2 bg-white text-slate-500 font-extrabold text-xs py-2 px-6 rounded-full border border-slate-200 text-center tracking-[4px] uppercase select-none shadow-sm">
                        STAGE / SCREEN
                    </div>
                </div>

                <!-- Active Seats Canvas -->
                <div id="builder-canvas" class="flex-1 relative w-full h-full select-none overflow-auto p-6">
                    <!-- Seats and Tables will be dynamically added here -->
                </div>

                <!-- Guidelines -->
                <div id="canvas-guidelines" class="absolute bottom-4 left-4 right-4 flex justify-between text-[11px] text-slate-500 font-mono pointer-events-none z-10 select-none">
                    <span>Layout Mode: Dynamic Floor Preview</span>
                    <span>No manual coordinates editing needed</span>
                </div>
            </div>
        </div>

        <!-- Sidebar Control Panel (Right 1 Column) -->
        <div id="sidebar-column" class="space-y-6">

            <!-- Configuration Card -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b pb-3 select-none">
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Parameters</h3>
                </div>

                <!-- Form Inputs -->
                <div class="space-y-4">
                    <!-- Event Layout Type -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Layout Type</label>
                        <select id="layout-type" onchange="toggleFormSettings()" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 font-bold transition duration-200">
                            <option value="theater" selected>Theater</option>
                            <option value="classroom">Classroom</option>
                            <option value="concert">Concert</option>
                        </select>
                    </div>

                    <!-- Category Seating Pools (Theater / Concert) -->
                    <div id="seats-settings-group" class="space-y-4">
                        @foreach($tickets as $ticket)
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">{{ $ticket->name }} Seats</label>
                                <input type="number" id="ticket-seats-{{ $ticket->id }}" 
                                       data-ticket-id="{{ $ticket->id }}" 
                                       data-ticket-name="{{ $ticket->name }}" 
                                       value="{{ $ticket->quota }}" 
                                       min="0" max="{{ $ticket->quota }}" 
                                       oninput="triggerGenerate()" 
                                       class="ticket-seat-input w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 font-bold transition duration-200">
                                <p class="text-[10px] text-gray-450 font-bold px-1 mt-1">Quota limit: {{ $ticket->quota }}</p>
                            </div>
                        @endforeach

                        <!-- Seats Per Row (New) -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Seats Per Row</label>
                            <input type="number" id="seats-per-row" value="10" min="1" max="50" oninput="triggerGenerate()" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 font-bold transition duration-200">
                        </div>
                    </div>

                    <!-- Classroom Seating Layout Rules (Classroom only) -->
                    <div id="classroom-settings-group" class="space-y-4 hidden">
                        <!-- Classroom Rows -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Classroom Rows</label>
                            <input type="number" id="class-rows" value="5" min="1" max="20" oninput="triggerGenerate()" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 font-bold transition duration-200">
                        </div>

                        <!-- Classroom Seats Per Row -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Seats Per Row</label>
                            <input type="number" id="class-seats-per-row" value="8" min="1" max="24" oninput="triggerGenerate()" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 font-bold transition duration-200">
                        </div>
                    </div>

                    <!-- Middle Aisle Toggle (Theater / Concert only) -->
                    <div id="aisle-settings-group" class="flex items-center justify-between py-1 select-none">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block cursor-pointer" for="layout-aisle">Middle Aisle</label>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="layout-aisle" checked onchange="triggerGenerate()" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </div>
                    </div>

                    <button onclick="triggerGenerateButton()" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-2xl shadow-lg hover:shadow-blue-100 transition duration-200 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.656 48.656 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3M3.75 12l3 3m-3-3-3 3M20.25 12a19.458 19.458 0 0 1 .138 2.378c0 1.942-1.385 3.6-3.328 3.86a48.71 48.71 0 0 1-10.12 0 3.882 3.882 0 0 1-3.328-3.86c0-.8.046-1.594.138-2.378" />
                        </svg>
                        Generate Floor
                    </button>

                    <button onclick="saveLayoutToBackend()" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-2xl shadow-lg hover:shadow-emerald-100 transition duration-200 flex items-center justify-center gap-2 mt-4 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                        </svg>
                        Save & Complete Setup
                    </button>
                </div>
            </div>

            <!-- Total Capacity Statistics Card -->
            <div id="stats-card" class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b pb-3 select-none">
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider font-semibold">Total Capacity</h3>
                </div>
                <div class="grid grid-cols-2 gap-4" id="stats-card-container">
                    <!-- Dynamic statistics will render here -->
                </div>
            </div>

        </div>
    </div>

    <!-- JSON Preview Output Section -->
    <div id="json-card" class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-3">
        <div class="flex items-center justify-between select-none">
            <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider font-semibold">JSON Realtime Output</h3>
            <button onclick="copyJsonOutput()" class="text-xs text-blue-600 hover:text-blue-700 font-bold flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H5.25m11.9-3.662a9 9 0 0 1 1.242 7.244M11.362 6.302a9 9 0 0 1 3.291 3.291m-8.822 5.285a9 9 0 0 1-1.242-7.244m6.285 7.244a9 9 0 0 1-3.291-3.291M12 12m-9 0a9 9 0 1 1 18 0a9 9 0 1 1 -18 0" />
                </svg>
                Copy JSON
            </button>
        </div>
        <pre id="json-preview" class="bg-slate-900 text-emerald-400 p-6 rounded-2xl overflow-x-auto text-xs font-mono shadow-inner max-h-[200px] border border-slate-800"></pre>
    </div>

</div>

<!-- CSS Styles for Layout Visualizer -->
<style>
    .seat-element {
        position: absolute;
        width: 32px;
        height: 32px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1.5px solid;
        z-index: 20;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    /* Dynamic styling matches category colors */
    .seat-vip, .seat-vvip {
        background-color: rgba(245, 158, 11, 0.08);
        border-color: rgba(245, 158, 11, 0.45);
        color: rgb(180, 83, 9);
    }
    .seat-vip:hover, .seat-vvip:hover {
        border-color: rgb(217, 119, 6);
        box-shadow: 0 0 8px rgba(245, 158, 11, 0.25);
    }
    
    .seat-platinum, .seat-silver {
        background-color: rgba(168, 85, 247, 0.08);
        border-color: rgba(168, 85, 247, 0.45);
        color: rgb(109, 40, 217);
    }
    .seat-platinum:hover, .seat-silver:hover {
        border-color: rgb(139, 92, 246);
        box-shadow: 0 0 8px rgba(168, 85, 247, 0.25);
    }

    .seat-regular, .seat-reguler {
        background-color: rgba(59, 130, 246, 0.08);
        border-color: rgba(59, 130, 246, 0.45);
        color: rgb(29, 78, 216);
    }
    .seat-regular:hover, .seat-reguler:hover {
        border-color: rgb(37, 99, 235);
        box-shadow: 0 0 8px rgba(59, 130, 246, 0.25);
    }

    /* Fallback generic style for custom categories */
    .seat-element:not(.seat-vip):not(.seat-vvip):not(.seat-platinum):not(.seat-silver):not(.seat-regular):not(.seat-reguler) {
        background-color: rgba(16, 185, 129, 0.08);
        border-color: rgba(16, 185, 129, 0.45);
        color: rgb(4, 120, 87);
    }

    .classroom-desk {
        position: absolute;
        background-color: rgba(139, 92, 26, 0.15);
        border: 1.5px solid rgba(139, 92, 26, 0.4);
        border-radius: 4px;
        pointer-events: none;
        z-index: 10;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .audience-mode .seat-element {
        cursor: default !important;
    }
</style>

<!-- Prototype Builder Script -->
<script>
    let seats = [];
    let desks = [];
    let viewMode = 'admin';

    const canvas = document.getElementById('builder-canvas');
    const jsonPreview = document.getElementById('json-preview');

    // Initial data loaded from database config
    const dbLayout = @json(json_decode($event->seat_layout, true));

    document.addEventListener('DOMContentLoaded', () => {
        loadPrototype();
    });

    // Toggle forms display depending on layout selected
    function toggleFormSettings() {
        const type = document.getElementById('layout-type').value;
        const seatsSettings = document.getElementById('seats-settings-group');
        const classroomSettings = document.getElementById('classroom-settings-group');
        const aisleSettings = document.getElementById('aisle-settings-group');

        if (type === 'classroom') {
            seatsSettings.classList.add('hidden');
            classroomSettings.classList.remove('hidden');
            aisleSettings.classList.add('hidden');
        } else {
            seatsSettings.classList.remove('hidden');
            classroomSettings.classList.add('hidden');
            aisleSettings.classList.remove('hidden');
        }
        triggerGenerate();
    }

    // Toggle Preview Mode
    function setViewMode(mode) {
        viewMode = mode;
        
        const btnAdmin = document.getElementById('btn-admin-view');
        const btnAudience = document.getElementById('btn-audience-view');
        const canvasColumn = document.getElementById('canvas-column');
        const sidebarColumn = document.getElementById('sidebar-column');
        const jsonCard = document.getElementById('json-card');
        
        if (mode === 'audience') {
            btnAdmin.className = "px-4 py-2 text-xs font-bold rounded-xl transition duration-200 text-gray-500 hover:text-gray-800";
            btnAudience.className = "px-4 py-2 text-xs font-bold rounded-xl transition duration-200 bg-white text-blue-650 shadow-sm";
            
            sidebarColumn.classList.add('hidden');
            canvasColumn.className = "lg:col-span-4 flex flex-col space-y-4";
            jsonCard.classList.add('hidden');
            canvas.classList.add('audience-mode');
        } else {
            btnAdmin.className = "px-4 py-2 text-xs font-bold rounded-xl transition duration-200 bg-white text-blue-600 shadow-sm";
            btnAudience.className = "px-4 py-2 text-xs font-bold rounded-xl transition duration-200 text-gray-500 hover:text-gray-800";
            
            sidebarColumn.classList.remove('hidden');
            canvasColumn.className = "lg:col-span-3 flex flex-col space-y-4";
            jsonCard.classList.remove('hidden');
            canvas.classList.remove('audience-mode');
        }
        
        renderCanvas();
    }

    // Dynamic Row label indexing (e.g. 0 -> A, 27 -> AB)
    function getRowLabel(index) {
        let label = '';
        let temp = index;
        while (temp >= 0) {
            label = String.fromCharCode(65 + (temp % 26)) + label;
            temp = Math.floor(temp / 26) - 1;
        }
        return label;
    }

    // Load layout configuration
    function loadPrototype() {
        if (dbLayout && dbLayout.config) {
            try {
                document.getElementById('layout-type').value = dbLayout.config.layoutType || 'theater';
                document.getElementById('layout-aisle').checked = dbLayout.config.hasAisle !== undefined ? dbLayout.config.hasAisle : true;
                document.getElementById('class-rows').value = dbLayout.config.classRows !== undefined ? dbLayout.config.classRows : 5;
                document.getElementById('class-seats-per-row').value = dbLayout.config.classSeatsPerRow !== undefined ? dbLayout.config.classSeatsPerRow : 8;
                if (dbLayout.config.seatsPerRow !== undefined) {
                    document.getElementById('seats-per-row').value = dbLayout.config.seatsPerRow;
                }

                if (dbLayout.config.categoriesData) {
                    for (const catId in dbLayout.config.categoriesData) {
                        const input = document.getElementById('ticket-seats-' + catId);
                        if (input) {
                            input.value = dbLayout.config.categoriesData[catId];
                        }
                    }
                }

                seats = dbLayout.seats || [];
                desks = dbLayout.desks || [];

                toggleFormSettings();
                renderCanvas();
                updateRealtimeStats();
                updateJsonPreview();
                return;
            } catch (err) {
                console.error('Failed to load database layout config, fall backing to default', err);
            }
        }
        
        generateDefaultLayout();
    }

    // Default configuration
    function generateDefaultLayout() {
        document.getElementById('layout-type').value = 'theater';
        const aisleCheck = document.getElementById('layout-aisle');
        if (aisleCheck) aisleCheck.checked = true;
        
        const rowInput = document.getElementById('seats-per-row');
        if (rowInput) rowInput.value = 10;
        
        document.getElementById('class-rows').value = 5;
        document.getElementById('class-seats-per-row').value = 8;

        toggleFormSettings();
    }

    // Triggered on inputs change
    function triggerGenerate() {
        const layoutType = document.getElementById('layout-type').value;
        const hasAisle = document.getElementById('layout-aisle') ? document.getElementById('layout-aisle').checked : false;
        const classRows = parseInt(document.getElementById('class-rows').value, 10) || 1;
        const classSeatsPerRow = parseInt(document.getElementById('class-seats-per-row').value, 10) || 1;

        // Read dynamic ticket seat inputs
        const categoryInputs = document.querySelectorAll('.ticket-seat-input');
        const categoriesData = [];
        categoryInputs.forEach(input => {
            categoriesData.push({
                id: input.getAttribute('data-ticket-id'),
                name: input.getAttribute('data-ticket-name'),
                count: parseInt(input.value, 10) || 0
            });
        });

        generateLayout(layoutType, categoriesData, hasAisle, classRows, classSeatsPerRow);
        renderCanvas();
        updateRealtimeStats();
        updateJsonPreview();
    }

    // Triggered explicitly by button
    function triggerGenerateButton() {
        triggerGenerate();
        alert('Seating layout generated successfully! Click "Save & Complete Setup" to store it.');
    }

    // Core layout generator algorithm (dynamic categories version)
    function generateLayout(layoutType, categoriesData, hasAisle, classRows, classSeatsPerRow) {
        seats = [];
        desks = [];
        
        const canvasWidth = 730;
        const spacingX = 40;
        const spacingY = 44;
        const aisleGap = hasAisle ? 30 : 0;

        if (layoutType === 'theater') {
            const seatsPerRow = parseInt(document.getElementById('seats-per-row').value, 10) || 10;
            let currentGlobalRowIndex = 0;

            const columns = seatsPerRow;
            const totalWidth = (columns - 1) * spacingX + aisleGap;
            const startX = Math.max(25, (canvasWidth - totalWidth - 32) / 2);
            const startY = 85;

            categoriesData.forEach((cat, catIdx) => {
                if (cat.count <= 0) return;
                
                const neededRows = Math.ceil(cat.count / columns);
                let seatCounter = 1;

                for (let r = 0; r < neededRows; r++) {
                    const rowY = startY + currentGlobalRowIndex * spacingY;
                    const rowLabel = getRowLabel(currentGlobalRowIndex);
                     
                    for (let c = 0; c < columns; c++) {
                        if (seatCounter > cat.count) break;

                        let posX = startX + c * spacingX;
                        if (hasAisle && c >= Math.floor(columns / 2)) {
                            posX += aisleGap;
                        }

                        const seatName = `${cat.name}-${String(seatCounter).padStart(2, '0')}`;
                        seats.push({
                            seat_number: seatName,
                            category: cat.name,
                            layout_type: 'theater',
                            row: rowLabel,
                            position: c + 1,
                            x: Math.round(posX),
                            y: Math.round(rowY),
                            rotation: 0
                        });
                        seatCounter++;
                    }
                    currentGlobalRowIndex++;
                }

                // Add visual gap if there are more categories to generate
                const hasMore = categoriesData.slice(catIdx + 1).some(c => c.count > 0);
                if (hasMore) {
                    currentGlobalRowIndex += 1.2;
                }
            });
        } 
        else if (layoutType === 'concert') {
            // Concert Layout Generator using Row-First Curvature
            const seatsPerRow = parseInt(document.getElementById('seats-per-row').value, 10) || 10;
            const rowSpacing = 50;
            const categoryGap = 55;
            const centerY = -2400;
            const centerX = canvasWidth / 2;
            const concertAisleGap = hasAisle ? 20 : 0;

            let activeCatIndex = 0;
            const categories = categoriesData.map(cat => {
                let scale = 1.00;
                if (cat.count > 0) {
                    scale = 1.00 + activeCatIndex * 0.20;
                    activeCatIndex++;
                }
                return {
                    type: cat.name,
                    count: cat.count,
                    prefix: cat.name,
                    widthScale: scale
                };
            });

            // Scan active categories and rows to calculate max row width multiplier
            let maxRowMultiplier = 1;
            categories.forEach(cat => {
                if (cat.count > 0) {
                    const totalRows = Math.ceil(cat.count / seatsPerRow);
                    const r = totalRows - 1;
                    const multiplier = cat.widthScale * (1 + r * 0.04);
                    if (multiplier > maxRowMultiplier) {
                        maxRowMultiplier = multiplier;
                    }
                }
            });

            // Scale base seat spacing
            let baseSeatSpacing = (670 - concertAisleGap) / ((seatsPerRow - 1) * maxRowMultiplier);
            baseSeatSpacing = Math.min(60, Math.max(34, baseSeatSpacing));

            let currentY = 45;
            let currentGlobalRowIndex = 0;

            categories.forEach((cat, catIdx) => {
                if (cat.count <= 0) return;

                const totalRows = Math.ceil(cat.count / seatsPerRow);
                let seatsPlaced = 0;
                const catSpacing = baseSeatSpacing * cat.widthScale;

                for (let r = 0; r < totalRows; r++) {
                    const rowLabel = getRowLabel(currentGlobalRowIndex);
                    const Math_min = Math.min;
                    const seatsInRow = Math_min(seatsPerRow, cat.count - seatsPlaced);
                    const seatSpacing = catSpacing * (1 + r * 0.04);

                    let rowWidth = (seatsInRow - 1) * seatSpacing;
                    if (hasAisle && seatsInRow >= 2) {
                        rowWidth += concertAisleGap;
                    }

                    const radius = currentY - centerY;

                    for (let i = 0; i < seatsInRow; i++) {
                        let relativeX = i * seatSpacing;
                        if (hasAisle && seatsInRow >= 2) {
                            const half = Math.floor(seatsInRow / 2);
                            if (i >= half) {
                                relativeX += concertAisleGap;
                            }
                        }
                        const dx = relativeX - (rowWidth / 2);

                        let compressedDx = dx;
                        if (rowWidth > 0) {
                            compressedDx = dx * (0.88 + 0.12 * Math.abs(dx / (rowWidth / 2)));
                        }

                        const theta = compressedDx / radius;
                        const posX = centerX + radius * Math.sin(theta) - 16;
                        const posY = centerY + radius * Math.cos(theta) - 16;
                        const degreesRotation = -theta * 180 / Math.PI;

                        const seatName = `${cat.prefix}-${String(seatsPlaced + i + 1).padStart(2, '0')}`;
                        seats.push({
                            seat_number: seatName,
                            category: cat.type,
                            layout_type: 'concert',
                            row: rowLabel,
                            position: i + 1,
                            x: Math.round(posX),
                            y: Math.round(posY),
                            rotation: Math.round(degreesRotation)
                        });
                    }

                    seatsPlaced += seatsInRow;
                    currentGlobalRowIndex++;

                    if (seatsPlaced >= cat.count) {
                        const hasMore = categories.slice(catIdx + 1).some(c => c.count > 0);
                        if (hasMore) {
                            currentY += categoryGap;
                        } else {
                            currentY += rowSpacing;
                        }
                    } else {
                        currentY += rowSpacing;
                    }
                }
            });
        }
        else if (layoutType === 'classroom') {
            // Lecture training style layout
            const classSpacingX = 42;
            const classSpacingY = 75;
            const centerAisle = classSeatsPerRow > 4 ? 35 : 0;
            const deskSpacing = 16;

            let totalWidth = 0;
            for (let c = 0; c < classSeatsPerRow; c++) {
                totalWidth += classSpacingX;
                if (c > 0 && c % 2 === 0) {
                    totalWidth += deskSpacing;
                }
                if (c === Math.floor(classSeatsPerRow / 2)) {
                    totalWidth += centerAisle;
                }
            }
            const startX = Math.max(30, (canvasWidth - totalWidth) / 2);
            const startY = 90;

            for (let r = 0; r < classRows; r++) {
                const rowY = startY + r * classSpacingY;
                const rowLabel = getRowLabel(r);
                
                const activeCategories = categoriesData.filter(c => c.count > 0);
                let catType = 'Regular';
                if (activeCategories.length > 0) {
                    const catIndex = Math.min(r, activeCategories.length - 1);
                    catType = activeCategories[catIndex].name;
                }
                
                let currentX = startX;

                for (let c = 0; c < classSeatsPerRow; c++) {
                    if (c > 0) {
                        currentX += classSpacingX;
                        if (c % 2 === 0) {
                            currentX += deskSpacing;
                        }
                        if (c === Math.floor(classSeatsPerRow / 2)) {
                            currentX += centerAisle;
                        }
                    }

                    if (c % 2 === 0 && c + 1 < classSeatsPerRow) {
                        desks.push({
                            x: Math.round(currentX - 5),
                            y: Math.round(rowY - 14),
                            width: Math.round(classSpacingX + 42),
                            height: 8
                        });
                    } else if (c % 2 === 0 && c + 1 === classSeatsPerRow) {
                        desks.push({
                            x: Math.round(currentX - 5),
                            y: Math.round(rowY - 14),
                            width: 42,
                            height: 8
                        });
                    }

                    const seatName = `${rowLabel}${c + 1}`;
                    seats.push({
                        seat_number: seatName,
                        category: catType,
                        layout_type: 'classroom',
                        row: rowLabel,
                        position: c + 1,
                        x: Math.round(currentX),
                        y: Math.round(rowY),
                        rotation: 0
                    });
                }
            }
        }

        // Horizontal post-processing centering
        if (seats.length > 0) {
            let minX = Infinity;
            let maxX = -Infinity;

            seats.forEach(s => {
                minX = Math.min(minX, s.x);
                maxX = Math.max(maxX, s.x + 32);
            });

            desks.forEach(d => {
                minX = Math.min(minX, d.x);
                maxX = Math.max(maxX, d.x + d.width);
            });

            if (minX !== Infinity && maxX !== -Infinity) {
                const totalWidth = maxX - minX;
                const targetStartX = (canvasWidth - totalWidth) / 2;
                const shiftX = targetStartX - minX;

                seats.forEach(s => {
                    s.x = Math.round(s.x + shiftX);
                });
                desks.forEach(d => {
                    d.x = Math.round(d.x + shiftX);
                });
            }
        }
    }

    // Save payload to Backend via HTTP POST
    function saveLayoutToBackend() {
        if (seats.length === 0) {
            alert('Please generate the layout first before saving! 🪑');
            return;
        }

        const layoutType = document.getElementById('layout-type').value;
        const hasAisle = document.getElementById('layout-aisle') ? document.getElementById('layout-aisle').checked : false;
        const classRows = parseInt(document.getElementById('class-rows').value, 10) || 1;
        const classSeatsPerRow = parseInt(document.getElementById('class-seats-per-row').value, 10) || 1;

        const categoryInputs = document.querySelectorAll('.ticket-seat-input');
        const categoriesData = {};
        categoryInputs.forEach(input => {
            categoriesData[input.getAttribute('data-ticket-id')] = parseInt(input.value, 10) || 0;
        });

        const saveUrl = "{{ route('admin.seats.builder.save', $event->id) }}";
        const csrfToken = "{{ csrf_token() }}";

        const payload = {
            config: {
                layoutType,
                hasAisle,
                classRows,
                classSeatsPerRow,
                seatsPerRow: parseInt(document.getElementById('seats-per-row').value, 10) || 10,
                categoriesData
            },
            seats: seats,
            desks: desks
        };

        fetch(saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            return response.json().then(data => {
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to save layout.');
                }
                return data;
            });
        })
        .then(data => {
            alert(data.message || 'Layout saved successfully! 🎉');
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
            console.error(err);
        });
    }

    // Draw elements on canvas
    function renderCanvas() {
        canvas.innerHTML = '';
        const layoutType = document.getElementById('layout-type').value;

        // Draw Classroom Desks
        if (layoutType === 'classroom') {
            desks.forEach(desk => {
                const deskEl = document.createElement('div');
                deskEl.className = 'classroom-desk';
                deskEl.style.left = `${desk.x}px`;
                deskEl.style.top = `${desk.y}px`;
                deskEl.style.width = `${desk.width}px`;
                deskEl.style.height = `${desk.height}px`;
                canvas.appendChild(deskEl);
            });
        }

        seats.forEach(seat => {
            const el = document.createElement('div');
            const slugCat = seat.category.toLowerCase().replace(/[^a-z0-9]/g, '');
            el.className = `seat-element seat-${slugCat}`;
            el.style.left = `${seat.x}px`;
            el.style.top = `${seat.y}px`;

            if (seat.rotation !== 0) {
                el.style.transform = `rotate(${seat.rotation}deg)`;
            }

            const emoji = seat.category.toLowerCase().includes('vip') ? '👑' : (seat.category.toLowerCase().includes('platinum') || seat.category.toLowerCase().includes('silver') ? '⭐' : '🪑');
            el.innerHTML = `
                <span class="text-[9px]">${emoji}</span>
                <span class="text-[7px] font-extrabold tracking-tight leading-none mt-0.5">${seat.seat_number}</span>
            `;

            // Tooltips for Audience View
            if (viewMode === 'audience') {
                el.classList.add('group');
                const tooltip = document.createElement('div');
                tooltip.className = 'absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3.5 hidden group-hover:block z-[9999] w-36 bg-slate-950/95 backdrop-blur text-white p-3 rounded-2xl shadow-xl text-center border border-slate-700/50 pointer-events-none transition duration-150';
                
                if (seat.rotation !== 0) {
                    tooltip.style.transform = `translate(-50%) rotate(${-seat.rotation}deg)`;
                }

                tooltip.innerHTML = `
                    <div class="font-extrabold text-sm leading-snug">${seat.seat_number}</div>
                    <div class="text-[10px] text-slate-400 mt-1 font-semibold">${seat.category} Seat</div>
                    <div class="inline-block mt-2 bg-green-500/20 text-green-450 border border-green-450/30 text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider">
                        Available 🟢
                    </div>
                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-t-[6px] border-t-slate-950/95"></div>
                `;
                el.appendChild(tooltip);
            }

            canvas.appendChild(el);
        });
    }

    // Refresh realtime capacity counters in statistics box
    function updateRealtimeStats() {
        const statsContainer = document.getElementById('stats-card-container');
        if (!statsContainer) return;

        statsContainer.innerHTML = '';
        const total = seats.length;

        // Total capacity block
        const totalBlock = document.createElement('div');
        totalBlock.className = 'bg-emerald-50 border border-emerald-100 rounded-2xl p-4 text-center col-span-2';
        totalBlock.innerHTML = `
            <span class="text-[10px] text-emerald-650 font-extrabold block uppercase tracking-wider">Total Seating Capacity</span>
            <span class="text-3xl font-black text-emerald-600 block mt-1">${total}</span>
        `;
        statsContainer.appendChild(totalBlock);

        // Count per category
        const categories = [...new Set(seats.map(s => s.category))];
        categories.forEach(catName => {
            const count = seats.filter(s => s.category === catName).length;
            const catBlock = document.createElement('div');
            catBlock.className = 'bg-slate-50 border border-slate-200/60 rounded-2xl p-3 text-center';
            catBlock.innerHTML = `
                <span class="text-[9px] text-slate-400 font-extrabold block uppercase tracking-wider">${catName}</span>
                <span class="text-lg font-black text-slate-700 block mt-0.5">${count}</span>
            `;
            statsContainer.appendChild(catBlock);
        });
    }

    // Refresh realtime JSON output panel
    function updateJsonPreview() {
        const output = seats.map(s => ({
            seat: s.seat_number,
            category: s.category,
            layout_type: s.layout_type,
            row: s.row,
            position: s.position
        }));
        jsonPreview.textContent = JSON.stringify(output, null, 2);
    }

    // Copy JSON to clipboard
    function copyJsonOutput() {
        const json = jsonPreview.textContent;
        navigator.clipboard.writeText(json).then(() => {
            alert('JSON output copied to clipboard successfully!');
        }).catch(err => {
            console.error('Failed to copy JSON:', err);
        });
    }
</script>
@endsection
