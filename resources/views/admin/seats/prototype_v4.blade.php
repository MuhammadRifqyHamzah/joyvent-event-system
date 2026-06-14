@extends('admin.layouts.app')

@section('title', 'Seat Configuration Builder V4')

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
                Seat Configuration Builder (V4 Parameterized Prototype)
            </h2>
            <p class="text-gray-500 mt-1 text-sm">Define seat counts per category. The system automatically structures and generates optimized floor plan layouts.</p>
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
            <div id="canvas-container" class="bg-slate-900 border-2 border-slate-800 rounded-3xl relative overflow-hidden h-[600px] shadow-2xl flex flex-col">
                <!-- Grid background pattern -->
                <div class="absolute inset-0 pointer-events-none opacity-[0.03]" style="background-image: radial-gradient(circle, white 2px, transparent 2px); background-size: 20px 20px;"></div>

                <!-- Stage Indicator (Theater / Classroom) -->
                <div id="stage-banner" class="w-full flex justify-center py-4 bg-slate-950/40 border-b border-slate-800/60 relative z-10 select-none">
                    <div class="w-1/2 bg-slate-800/80 text-slate-400 font-extrabold text-xs py-2 px-6 rounded-full border border-slate-700 text-center tracking-[4px] uppercase select-none">
                        STAGE / SCREEN
                    </div>
                </div>

                <!-- Active Seats Canvas -->
                <div id="builder-canvas" class="flex-1 relative w-full h-full select-none overflow-auto p-6">
                    <!-- Seats and Tables will be dynamically added here -->
                </div>

                <!-- Guidelines -->
                <div id="canvas-guidelines" class="absolute bottom-4 left-4 right-4 flex justify-between text-[11px] text-slate-500 font-mono pointer-events-none z-10 select-none">
                    <span>Layout Type: Parameterized Configuration</span>
                    <span>No manual positioning required</span>
                </div>
            </div>
        </div>

        <!-- Sidebar Control Panel (Right 1 Column) -->
        <div id="sidebar-column" class="space-y-6">

            <!-- Configuration Card -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b pb-3 select-none">
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Configuration</h3>
                </div>

                <!-- Form Inputs -->
                <div class="space-y-4">
                    <!-- Event Layout Type -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Event Layout Type</label>
                        <select id="layout-type" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 font-bold transition duration-200">
                            <option value="theater" selected>Theater</option>
                            <option value="classroom">Classroom</option>
                            <option value="roundtable">Round Table</option>
                        </select>
                    </div>

                    <!-- VIP Seats Count -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">VIP Seats</label>
                        <input type="number" id="vip-seats" value="20" min="0" max="100" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 font-bold transition duration-200">
                    </div>

                    <!-- Platinum Seats Count -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Platinum Seats</label>
                        <input type="number" id="plat-seats" value="50" min="0" max="200" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 font-bold transition duration-200">
                    </div>

                    <!-- Regular Seats Count -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Regular Seats</label>
                        <input type="number" id="reg-seats" value="200" min="0" max="500" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 font-bold transition duration-200">
                    </div>

                    <!-- Middle Aisle -->
                    <div class="flex items-center justify-between py-1 select-none">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block cursor-pointer" for="layout-aisle">Middle Aisle</label>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="layout-aisle" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </div>
                    </div>

                    <button onclick="triggerGenerate()" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-2xl shadow-lg hover:shadow-blue-100 transition duration-200 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.656 48.656 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3M3.75 12l3 3m-3-3-3 3M20.25 12a19.458 19.458 0 0 1 .138 2.378c0 1.942-1.385 3.6-3.328 3.86a48.71 48.71 0 0 1-10.12 0 3.882 3.882 0 0 1-3.328-3.86c0-.8.046-1.594.138-2.378" />
                        </svg>
                        Generate Layout
                    </button>
                </div>
            </div>

            <!-- Realtime Statistics Card -->
            <div id="stats-card" class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b pb-3 select-none">
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider font-semibold">Realtime Statistics</h3>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 border border-gray-100 rounded-2xl p-4 text-center">
                        <span class="text-[10px] text-gray-400 font-extrabold block uppercase tracking-wider">Total</span>
                        <span id="stat-total" class="text-xl font-black text-gray-800 block mt-1">0</span>
                    </div>
                    <div class="bg-amber-50 border border-amber-100/30 rounded-2xl p-4 text-center">
                        <span class="text-[10px] text-amber-500 font-extrabold block uppercase tracking-wider">VIP</span>
                        <span id="stat-vip" class="text-xl font-black text-amber-600 block mt-1">0</span>
                    </div>
                    <div class="bg-purple-50 border border-purple-100/30 rounded-2xl p-4 text-center">
                        <span class="text-[10px] text-purple-500 font-extrabold block uppercase tracking-wider">Platinum</span>
                        <span id="stat-plat" class="text-xl font-black text-purple-600 block mt-1">0</span>
                    </div>
                    <div class="bg-blue-50 border border-blue-100/30 rounded-2xl p-4 text-center">
                        <span class="text-[10px] text-blue-500 font-extrabold block uppercase tracking-wider">Regular</span>
                        <span id="stat-reg" class="text-xl font-black text-blue-600 block mt-1">0</span>
                    </div>
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
    
    /* Category styles */
    .seat-vip {
        background-color: rgba(245, 158, 11, 0.15);
        border-color: rgba(245, 158, 11, 0.6);
        color: rgb(251, 191, 36);
    }
    .seat-vip:hover {
        border-color: rgb(245, 158, 11);
        box-shadow: 0 0 8px rgba(245, 158, 11, 0.4);
    }
    
    .seat-platinum {
        background-color: rgba(168, 85, 247, 0.15);
        border-color: rgba(168, 85, 247, 0.6);
        color: rgb(192, 132, 252);
    }
    .seat-platinum:hover {
        border-color: rgb(168, 85, 247);
        box-shadow: 0 0 8px rgba(168, 85, 247, 0.4);
    }

    .seat-regular {
        background-color: rgba(59, 130, 246, 0.15);
        border-color: rgba(59, 130, 246, 0.6);
        color: rgb(147, 197, 253);
    }
    .seat-regular:hover {
        border-color: rgb(59, 130, 246);
        box-shadow: 0 0 8px rgba(59, 130, 246, 0.4);
    }

    /* Desk Overlay styling for classrooms */
    .classroom-desk {
        position: absolute;
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 4px;
        pointer-events: none;
        z-index: 10;
    }

    /* Round Table graphical overlay */
    .round-table-overlay {
        position: absolute;
        background-color: rgba(255, 255, 255, 0.05);
        border: 2px dashed rgba(255, 255, 255, 0.15);
        border-radius: 9999px;
        z-index: 10;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.4), inset 0 0 10px rgba(0, 0, 0, 0.5);
    }

    .audience-mode .seat-element {
        cursor: default !important;
    }
</style>

<!-- Prototype Builder Script -->
<script>
    let seats = [];
    let tables = [];
    let viewMode = 'admin';

    const canvas = document.getElementById('builder-canvas');
    const jsonPreview = document.getElementById('json-preview');

    document.addEventListener('DOMContentLoaded', () => {
        loadPrototype();
    });

    // Toggle Preview Mode
    function setViewMode(mode) {
        viewMode = mode;
        
        const btnAdmin = document.getElementById('btn-admin-view');
        const btnAudience = document.getElementById('btn-audience-view');
        const canvasColumn = document.getElementById('canvas-column');
        const sidebarColumn = document.getElementById('sidebar-column');
        const jsonCard = document.getElementById('json-card');
        const stageBanner = document.getElementById('stage-banner');
        
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

    // Load layout from localStorage
    function loadPrototype() {
        const stored = localStorage.getItem('joyvent_seat_configuration_v4_prototype');
        if (stored) {
            try {
                const loaded = JSON.parse(stored);
                if (loaded && loaded.config) {
                    document.getElementById('layout-type').value = loaded.config.layoutType || 'theater';
                    document.getElementById('vip-seats').value = loaded.config.vipSeats !== undefined ? loaded.config.vipSeats : 20;
                    document.getElementById('plat-seats').value = loaded.config.platSeats !== undefined ? loaded.config.platSeats : 50;
                    document.getElementById('reg-seats').value = loaded.config.regSeats !== undefined ? loaded.config.regSeats : 200;
                    document.getElementById('layout-aisle').checked = loaded.config.hasAisle !== undefined ? loaded.config.hasAisle : true;
                    
                    seats = loaded.seats || [];
                    tables = loaded.tables || [];
                }
            } catch (e) {
                console.error('Failed to parse localStorage prototype V4, loading defaults', e);
                generateDefaultLayout();
            }
        } else {
            generateDefaultLayout();
        }
        renderCanvas();
        updateRealtimeStats();
        updateJsonPreview();
    }

    // Default configuration
    function generateDefaultLayout() {
        document.getElementById('layout-type').value = 'theater';
        document.getElementById('vip-seats').value = 20;
        document.getElementById('plat-seats').value = 50;
        document.getElementById('reg-seats').value = 200;
        document.getElementById('layout-aisle').checked = true;

        generateLayout('theater', 20, 50, 200, true);
    }

    // Callback on form generate button
    function triggerGenerate() {
        const layoutType = document.getElementById('layout-type').value;
        const vipSeats = parseInt(document.getElementById('vip-seats').value, 10) || 0;
        const platSeats = parseInt(document.getElementById('plat-seats').value, 10) || 0;
        const regSeats = parseInt(document.getElementById('reg-seats').value, 10) || 0;
        const hasAisle = document.getElementById('layout-aisle').checked;

        generateLayout(layoutType, vipSeats, platSeats, regSeats, hasAisle);
        savePrototype();
        renderCanvas();
        updateRealtimeStats();
        updateJsonPreview();
    }

    // Layout generators
    function generateLayout(layoutType, vipCount, platCount, regCount, hasAisle) {
        seats = [];
        tables = [];
        
        const canvasWidth = 760;
        const spacingX = 40;
        const spacingY = 44;
        const aisleGap = hasAisle ? 36 : 0;
        const columns = 12;

        const stageBanner = document.getElementById('stage-banner');
        if (layoutType === 'roundtable') {
            stageBanner.classList.add('hidden');
        } else {
            stageBanner.classList.remove('hidden');
        }

        if (layoutType === 'theater') {
            // Layout Theater:VIP, Platinum, and Regular in sequence
            let currentGlobalRowIndex = 0;
            const categories = [
                { type: 'VIP', count: vipCount, prefix: 'VIP' },
                { type: 'Platinum', count: platCount, prefix: 'PLAT' },
                { type: 'Regular', count: regCount, prefix: 'REG' }
            ];

            const totalWidth = (columns - 1) * spacingX + aisleGap;
            const startX = Math.max(30, (canvasWidth - totalWidth - 32) / 2);
            const startY = 90;

            categories.forEach(cat => {
                if (cat.count <= 0) return;
                
                const neededRows = Math.ceil(cat.count / columns);
                let seatCounter = 1;

                for (let r = 0; r < neededRows; r++) {
                    const rowY = startY + currentGlobalRowIndex * spacingY;
                    
                    for (let c = 0; c < columns; c++) {
                        if (seatCounter > cat.count) break;

                        let posX = startX + c * spacingX;
                        if (hasAisle && c >= Math.floor(columns / 2)) {
                            posX += aisleGap;
                        }

                        const seatName = `${cat.prefix}-${String(seatCounter).padStart(2, '0')}`;
                        seats.push({
                            id: `st-${cat.type}-${seatCounter}`,
                            seat_number: seatName,
                            category: cat.type,
                            status: 'available',
                            x: Math.round(posX),
                            y: Math.round(rowY),
                            rotation: 0
                        });
                        seatCounter++;
                    }
                    currentGlobalRowIndex++;
                }
                // Add vertical spacing between categories
                currentGlobalRowIndex += 0.5;
            });
        } 
        else if (layoutType === 'classroom') {
            const classSpacingY = 70;
            const deskGap = 20;
            const centerAisle = hasAisle ? 32 : 0;

            let totalWidth = 0;
            for (let c = 0; c < columns; c++) {
                if (c > 0) {
                    if (c % 2 === 0) {
                        totalWidth += deskGap;
                    } else {
                        totalWidth += spacingX;
                    }
                    if (hasAisle && c === Math.floor(columns / 2)) {
                        totalWidth += centerAisle;
                    }
                }
            }
            const startX = Math.max(30, (canvasWidth - totalWidth - 32) / 2);
            const startY = 100;

            let currentGlobalRowIndex = 0;
            const categories = [
                { type: 'VIP', count: vipCount, prefix: 'VIP' },
                { type: 'Platinum', count: platCount, prefix: 'PLAT' },
                { type: 'Regular', count: regCount, prefix: 'REG' }
            ];

            categories.forEach(cat => {
                if (cat.count <= 0) return;
                
                const neededRows = Math.ceil(cat.count / columns);
                let seatCounter = 1;

                for (let r = 0; r < neededRows; r++) {
                    const rowY = startY + currentGlobalRowIndex * classSpacingY;
                    let currentX = startX;

                    for (let c = 0; c < columns; c++) {
                        if (seatCounter > cat.count) break;

                        if (c > 0) {
                            if (c % 2 === 0) {
                                currentX += deskGap;
                            } else {
                                currentX += spacingX;
                            }
                            if (hasAisle && c === Math.floor(columns / 2)) {
                                currentX += centerAisle;
                            }
                        }

                        const seatName = `${cat.prefix}-${String(seatCounter).padStart(2, '0')}`;
                        seats.push({
                            id: `st-${cat.type}-${seatCounter}`,
                            seat_number: seatName,
                            category: cat.type,
                            status: 'available',
                            x: Math.round(currentX),
                            y: Math.round(rowY),
                            rotation: 0,
                            isClassroomDesk: (c % 2 === 0)
                        });
                        seatCounter++;
                    }
                    currentGlobalRowIndex++;
                }
                currentGlobalRowIndex += 0.5;
            });
        }
        else if (layoutType === 'roundtable') {
            const tableCapacity = 8;
            const categories = [
                { type: 'VIP', count: vipCount, prefix: 'VIP' },
                { type: 'Platinum', count: platCount, prefix: 'PLAT' },
                { type: 'Regular', count: regCount, prefix: 'REG' }
            ];

            // Calculate total tables per category
            let vipTables = Math.ceil(vipCount / tableCapacity);
            let platTables = Math.ceil(platCount / tableCapacity);
            let regTables = Math.ceil(regCount / tableCapacity);

            // Set coordinates for tables
            let currentY = 80;
            const maxTablesPerRow = 4;
            const rowHeight = 140;
            const colSpacing = 160;

            function arrangeTablesForCategory(catTables, prefixName) {
                const list = [];
                for (let i = 0; i < catTables; i++) {
                    list.push({
                        name: `${prefixName}-T${i+1}`,
                        x: 0,
                        y: 0
                    });
                }

                for (let i = 0; i < list.length; i += maxTablesPerRow) {
                    const chunk = list.slice(i, i + maxTablesPerRow);
                    const count = chunk.length;
                    const rowWidth = (count - 1) * colSpacing;
                    const startX = (canvasWidth - rowWidth) / 2;

                    for (let j = 0; j < count; j++) {
                        chunk[j].x = Math.round(startX + j * colSpacing);
                        chunk[j].y = Math.round(currentY);
                    }
                    currentY += rowHeight;
                }
                tables.push(...list);
            }

            // Lay out tables vertically by category
            if (vipTables > 0) {
                arrangeTablesForCategory(vipTables, 'VIP');
            }
            if (platTables > 0) {
                currentY += 20; // extra spacing
                arrangeTablesForCategory(platTables, 'PLAT');
            }
            if (regTables > 0) {
                currentY += 20; // extra spacing
                arrangeTablesForCategory(regTables, 'REG');
            }

            // Distribute seats around tables
            categories.forEach(cat => {
                if (cat.count <= 0) return;
                
                let seatCounter = 1;
                const catTables = tables.filter(t => t.name.startsWith(cat.prefix));

                catTables.forEach((table, tIdx) => {
                    // Maximum of tableCapacity (8) seats per table
                    for (let i = 0; i < tableCapacity; i++) {
                        if (seatCounter > cat.count) break;

                        // Calculate polar coordinates
                        const theta = (i * (2 * Math.PI)) / tableCapacity;
                        const radius = 42; 
                        
                        const posX = table.x + radius * Math.cos(theta) - 16;
                        const posY = table.y + radius * Math.sin(theta) - 16;
                        const rotation = (theta * 180) / Math.PI + 90;

                        const seatName = `${cat.prefix}-${String(seatCounter).padStart(2, '0')}`;
                        seats.push({
                            id: `st-${cat.type}-${seatCounter}`,
                            seat_number: seatName,
                            category: cat.type,
                            status: 'available',
                            x: Math.round(posX),
                            y: Math.round(posY),
                            rotation: Math.round(rotation)
                        });
                        seatCounter++;
                    }
                });
            });
        }
    }

    // Save configuration payload to localStorage
    function savePrototype() {
        const layoutType = document.getElementById('layout-type').value;
        const vipSeats = parseInt(document.getElementById('vip-seats').value, 10) || 0;
        const platSeats = parseInt(document.getElementById('plat-seats').value, 10) || 0;
        const regSeats = parseInt(document.getElementById('reg-seats').value, 10) || 0;
        const hasAisle = document.getElementById('layout-aisle').checked;

        const payload = {
            config: {
                layoutType,
                vipSeats,
                platSeats,
                regSeats,
                hasAisle
            },
            seats: seats.map(s => ({
                seat_number: s.seat_number,
                category: s.category,
                status: s.status
            })),
            tables: tables
        };

        localStorage.setItem('joyvent_seat_configuration_v4_prototype', JSON.stringify(payload));
    }

    // Render configuration UI elements
    function renderCanvas() {
        canvas.innerHTML = '';
        const layoutType = document.getElementById('layout-type').value;

        // Draw roundtable graphics
        if (layoutType === 'roundtable') {
            tables.forEach(t => {
                const tEl = document.createElement('div');
                tEl.className = 'round-table-overlay flex items-center justify-center text-[9px] font-black text-slate-500/80';
                tEl.style.left = `${t.x - 26}px`;
                tEl.style.top = `${t.y - 26}px`;
                tEl.style.width = `52px`;
                tEl.style.height = `52px`;
                tEl.textContent = t.name;
                canvas.appendChild(tEl);
            });
        }

        seats.forEach(seat => {
            if (seat.isClassroomDesk && viewMode === 'admin') {
                const desk = document.createElement('div');
                desk.className = 'classroom-desk';
                desk.style.left = `${seat.x - 6}px`;
                desk.style.top = `${seat.y - 12}px`;
                desk.style.width = '84px';
                desk.style.height = '8px';
                canvas.appendChild(desk);
            }

            const el = document.createElement('div');
            el.id = seat.id;
            el.className = `seat-element seat-${seat.category.toLowerCase()}`;
            el.style.left = `${seat.x}px`;
            el.style.top = `${seat.y}px`;

            if (seat.rotation !== 0) {
                el.style.transform = `rotate(${seat.rotation}deg)`;
            }

            const emoji = seat.category === 'VIP' ? '👑' : (seat.category === 'Platinum' ? '⭐' : '🪑');
            el.innerHTML = `
                <span class="text-[9px]">${emoji}</span>
                <span class="text-[7px] font-extrabold tracking-tight leading-none mt-0.5">${seat.seat_number}</span>
            `;

            // Tooltips
            if (viewMode === 'audience') {
                el.classList.add('group');
                const tooltip = document.createElement('div');
                tooltip.className = 'absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3.5 hidden group-hover:block z-[9999] w-36 bg-slate-950/95 backdrop-blur text-white p-3 rounded-2xl shadow-xl text-center border border-slate-700/50 pointer-events-none transition duration-150';
                
                if (seat.rotation !== 0) {
                    tooltip.style.transform = `translate(-50%) rotate(${-seat.rotation}deg)`;
                }

                tooltip.innerHTML = `
                    <div class="font-extrabold text-sm leading-snug">${seat.seat_number}</div>
                    <div class="text-[10px] text-slate-400 mt-1 font-semibold">${seat.category}</div>
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

    // Refresh realtime stats panel
    function updateRealtimeStats() {
        const vipCount = seats.filter(s => s.category === 'VIP').length;
        const platCount = seats.filter(s => s.category === 'Platinum').length;
        const regCount = seats.filter(s => s.category === 'Regular').length;
        const total = seats.length;

        document.getElementById('stat-total').textContent = total;
        document.getElementById('stat-vip').textContent = vipCount;
        document.getElementById('stat-plat').textContent = platCount;
        document.getElementById('stat-reg').textContent = regCount;
    }

    // Refresh JSON output panel
    function updateJsonPreview() {
        // Output format: seat_number, category, status
        const output = seats.map(s => ({
            seat_number: s.seat_number,
            category: s.category,
            status: s.status
        }));
        jsonPreview.textContent = JSON.stringify(output, null, 2);
    }

    // Copy JSON to clipboard
    function copyJsonOutput() {
        const json = jsonPreview.textContent;
        navigator.clipboard.writeText(json).then(() => {
            alert('V4 JSON output copied to clipboard successfully!');
        }).catch(err => {
            console.error('Failed to copy JSON:', err);
        });
    }
</script>
@endsection
