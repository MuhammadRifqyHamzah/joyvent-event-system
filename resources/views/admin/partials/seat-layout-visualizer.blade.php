@php
    $hasCoordinates = false;
    $flatSeats = collect($seats);
    if ($flatSeats->isNotEmpty()) {
        $firstSeat = $flatSeats->first();
        if ($firstSeat && $firstSeat->x !== null && $firstSeat->y !== null) {
            $hasCoordinates = true;
        }
    }

    $layoutJson = json_decode($event->seat_layout, true);
    $desks = $layoutJson['desks'] ?? [];

    $maxX = 0;
    $maxY = 0;
    if ($hasCoordinates) {
        $maxX = $flatSeats->max('x') ?? 800;
        $maxY = $flatSeats->max('y') ?? 600;

        foreach ($desks as $desk) {
            $maxX = max($maxX, ($desk['x'] ?? 0) + ($desk['width'] ?? 0));
            $maxY = max($maxY, ($desk['y'] ?? 0) + ($desk['height'] ?? 0));
        }
    }

    $containerWidth = $maxX + 60;
    $containerHeight = $maxY + 60;

    // Group seats by row for fallback layout
    $groupedSeats = $flatSeats->groupBy(function($seat) {
        return is_numeric($seat->row) && $seat->row >= 1 && $seat->row <= 26 
            ? chr($seat->row + 64) 
            : $seat->row;
    });
@endphp

@if ($hasCoordinates)
    <!-- CSS Styles for Layout Visualizer -->
    <style>
        .classroom-desk-visualizer {
            position: absolute;
            background-color: rgba(139, 92, 26, 0.15);
            border: 1.5px solid rgba(139, 92, 26, 0.4);
            border-radius: 4px;
            pointer-events: none;
            z-index: 10;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>

    <div class="relative bg-slate-50 border border-slate-200 rounded-[32px] overflow-auto p-6 shadow-inner select-none mx-auto w-full" 
         style="height: {{ $containerHeight }}px; min-height: 400px;" id="seatCanvasVisualizer">
         
        <!-- Grid background pattern -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.4]" style="background-image: radial-gradient(circle, #cbd5e1 1.5px, transparent 1.5px); background-size: 20px 20px;"></div>

        <!-- Centered Seat and Desk Container -->
        <div class="relative mx-auto h-full" style="width: 730px;">
            <!-- Render Classroom Desks -->
            @foreach($desks as $desk)
                <div class="classroom-desk-visualizer" 
                     style="left: {{ $desk['x'] }}px; top: {{ $desk['y'] }}px; width: {{ $desk['width'] }}px; height: {{ $desk['height'] }}px;">
                </div>
            @endforeach

            <!-- Render Seats -->
            @foreach($flatSeats as $seat)
                @php
                    $status = $seat->status;
                    $booking = isset($seatBookings) ? ($seatBookings instanceof \Illuminate\Support\Collection ? $seatBookings->get($seat->seat_number) : ($seatBookings[$seat->seat_number] ?? null)) : null;
                    
                    $categoryName = $seat->ticketCategory->name ?? '';
                    $slugCat = strtolower($categoryName);
                    $emoji = str_contains($slugCat, 'vip') ? '👑' : (str_contains($slugCat, 'platinum') || str_contains($slugCat, 'silver') ? '⭐' : '🪑');

                    // Determine base color classes based on category (for available seats)
                    if (str_contains($slugCat, 'vip') || str_contains($slugCat, 'vvip')) {
                        $categoryColor = 'bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100/70 hover:border-amber-300';
                    } elseif (str_contains($slugCat, 'platinum') || str_contains($slugCat, 'silver')) {
                        $categoryColor = 'bg-purple-50 border-purple-200 text-purple-700 hover:bg-purple-100/70 hover:border-purple-300';
                    } elseif (str_contains($slugCat, 'regular') || str_contains($slugCat, 'reguler')) {
                        $categoryColor = 'bg-blue-50 border-blue-200 text-blue-650 hover:bg-blue-100/70 hover:border-blue-300';
                    } else {
                        $categoryColor = 'bg-green-50 border-green-200 text-green-600 hover:bg-green-100/70 hover:border-green-300';
                    }

                    // If booked or blocked, override
                    if ($status === 'booked') {
                        $btnColor = 'bg-slate-100 border-slate-250 text-slate-400 opacity-60';
                    } elseif ($status === 'blocked') {
                        $btnColor = 'bg-red-50 border-red-200 text-red-650 hover:bg-red-100/70 hover:border-red-300';
                    } else {
                        $btnColor = $categoryColor;
                    }
                @endphp

                <div class="absolute group" 
                     style="left: {{ $seat->x }}px; top: {{ $seat->y }}px; transform: rotate({{ $seat->rotation }}deg); width: 32px; height: 32px; z-index: 20;">
                    
                    @if(isset($interactive) && $interactive)
                        <form action="{{ route('admin.seats.toggle_status', $seat->id) }}" method="POST" class="w-full h-full">
                            @csrf
                            <button type="submit" 
                                {{ $status === 'booked' ? 'disabled' : '' }}
                                class="w-full h-full rounded-lg border flex flex-col items-center justify-center font-bold text-[9px] tracking-tight transition shadow-sm cursor-pointer disabled:cursor-not-allowed select-none {{ $btnColor }}"
                                title="Seat {{ $seat->seat_number }}">
                                <span class="text-[8px] leading-none mb-0.5">{{ $emoji }}</span>
                                <span class="leading-none font-extrabold">{{ $seat->seat_number }}</span>
                            </button>
                        </form>
                    @else
                        <div class="w-full h-full rounded-lg border flex flex-col items-center justify-center font-bold text-[9px] tracking-tight select-none {{ $btnColor }}">
                            <span class="text-[8px] leading-none mb-0.5">{{ $emoji }}</span>
                            <span class="leading-none font-extrabold">{{ $seat->seat_number }}</span>
                        </div>
                    @endif

                    <!-- Tooltips -->
                    @if($status === 'booked' && $booking && $booking->user)
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3.5 hidden group-hover:block z-[9999] w-52 bg-slate-950/95 backdrop-blur text-white p-3 rounded-2xl shadow-xl border border-slate-700/50 text-center">
                            <div class="font-extrabold text-xs truncate leading-snug">{{ $booking->user->name }}</div>
                            <div class="text-[9px] text-slate-400 mt-1 truncate">{{ $booking->user->email }}</div>
                            <div class="inline-block mt-2 bg-blue-500/20 text-blue-400 border border-blue-400/30 text-[8px] px-2.5 py-0.5 rounded-full font-bold uppercase">
                                {{ $booking->ticketCategory->name ?? 'Ticket' }}
                            </div>
                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-t-[6px] border-t-slate-950/95"></div>
                        </div>
                    @else
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3.5 hidden group-hover:block z-[9999] w-36 bg-slate-950/95 backdrop-blur text-white p-2.5 rounded-2xl shadow-xl border border-slate-700/50 text-center pointer-events-none">
                            <div class="font-extrabold text-[11px] leading-snug">{{ $seat->seat_number }}</div>
                            <div class="text-[9px] text-slate-400 mt-0.5">{{ $seat->ticketCategory->name ?? 'Regular' }}</div>
                            <div class="inline-block mt-1.5 text-[8px] px-2 py-0.5 rounded-full font-bold uppercase
                                {{ $status === 'blocked' ? 'bg-red-500/20 text-red-400 border border-red-400/30' : 'bg-green-500/20 text-green-450 border border-green-450/30' }}">
                                {{ $status === 'blocked' ? 'Blocked 🔴' : 'Available 🟢' }}
                            </div>
                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-t-[6px] border-t-slate-950/95"></div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Legend for coordinates map -->
    <div class="pt-6 border-t border-gray-100 flex flex-col items-center gap-3.5 w-full text-[10px] font-bold uppercase tracking-wider text-gray-500">
        <!-- Ticket Categories Row -->
        <div class="flex items-center justify-center gap-6 flex-wrap">
            <span class="text-[9px] text-gray-400">Kelas Tiket:</span>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-md bg-amber-50 border border-amber-200"></div>
                <span>VIP/VVIP 👑</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-md bg-purple-50 border border-purple-200"></div>
                <span>Silver/Platinum ⭐</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-md bg-blue-50 border border-blue-200"></div>
                <span>Regular 🪑</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-md bg-green-50 border border-green-200"></div>
                <span>Lainnya</span>
            </div>
        </div>
        <!-- Occupancy Status Row -->
        <div class="flex items-center justify-center gap-6 flex-wrap">
            <span class="text-[9px] text-gray-400">Status Kursi:</span>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-md bg-slate-100 border border-slate-250 opacity-60"></div>
                <span>Terisi (Booked)</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-md bg-red-50 border border-red-200"></div>
                <span>Diblokir (Blocked)</span>
            </div>
        </div>
    </div>
@else
    <!-- Fallback Grid Layout -->
    <div class="flex flex-col gap-4 items-center justify-center p-8 bg-slate-50 border border-slate-100 rounded-[32px] overflow-auto select-none min-h-[300px]" id="seatMapContainer">
        <!-- Panggung -->
        <div class="w-full max-w-sm bg-slate-200 border border-slate-300/40 text-slate-500 text-[10px] font-bold py-2 rounded-lg text-center tracking-[4px] uppercase mb-12 shadow-sm">
            STAGE / PANGGUNG UTAMA
        </div>

        @foreach($groupedSeats as $rowLabel => $rowSeats)
            <div class="flex items-center gap-3">
                <span class="w-8 text-right font-extrabold text-xs text-slate-450 uppercase select-none mr-2">
                    {{ $rowLabel }}
                </span>
                
                <div class="flex items-center gap-2">
                    @foreach($rowSeats as $seat)
                        @php
                            $status = $seat->status;
                            $booking = isset($seatBookings) ? ($seatBookings instanceof \Illuminate\Support\Collection ? $seatBookings->get($seat->seat_number) : ($seatBookings[$seat->seat_number] ?? null)) : null;
                            
                            $categoryName = $seat->ticketCategory->name ?? '';
                            $slugCat = strtolower($categoryName);

                            // Determine base color classes based on category (for available seats)
                            if (str_contains($slugCat, 'vip') || str_contains($slugCat, 'vvip')) {
                                $categoryColor = 'bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100/70 hover:border-amber-300';
                            } elseif (str_contains($slugCat, 'platinum') || str_contains($slugCat, 'silver')) {
                                $categoryColor = 'bg-purple-50 border-purple-200 text-purple-700 hover:bg-purple-100/70 hover:border-purple-300';
                            } elseif (str_contains($slugCat, 'regular') || str_contains($slugCat, 'reguler')) {
                                $categoryColor = 'bg-blue-50 border-blue-200 text-blue-650 hover:bg-blue-100/70 hover:border-blue-300';
                            } else {
                                $categoryColor = 'bg-green-50 border-green-200 text-green-600 hover:bg-green-100/70 hover:border-green-300';
                            }

                            // If booked or blocked, override
                            if ($status === 'booked') {
                                $btnColor = 'bg-slate-100 border-slate-250 text-slate-400 opacity-60';
                            } elseif ($status === 'blocked') {
                                $btnColor = 'bg-red-50 border-red-200 text-red-655 hover:bg-red-100/70 hover:border-red-300';
                            } else {
                                $btnColor = $categoryColor;
                            }
                        @endphp
                        <div class="relative group">
                            @if(isset($interactive) && $interactive)
                                <form action="{{ route('admin.seats.toggle_status', $seat->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                        {{ $status === 'booked' ? 'disabled' : '' }}
                                        class="w-9 h-9 rounded-xl border flex items-center justify-center font-bold text-xs tracking-tight transition shadow-sm cursor-pointer disabled:cursor-not-allowed select-none {{ $btnColor }}">
                                        {{ $seat->column }}
                                    </button>
                                </form>
                            @else
                                <div class="w-9 h-9 rounded-xl border flex items-center justify-center font-bold text-xs tracking-tight select-none {{ $btnColor }}">
                                    {{ $seat->column }}
                                </div>
                            @endif
                            
                            @if($status === 'booked' && $booking && $booking->user)
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 hidden group-hover:block z-30 w-52 bg-slate-900/95 backdrop-blur text-white p-3 rounded-2xl shadow-xl border border-slate-700/50 text-center">
                                    <div class="font-extrabold text-xs truncate leading-snug">{{ $booking->user->name }}</div>
                                    <div class="text-[9px] text-slate-400 mt-1 truncate">{{ $booking->user->email }}</div>
                                    <div class="inline-block mt-2 bg-blue-500/20 text-blue-400 border border-blue-400/30 text-[8px] px-2.5 py-0.5 rounded-full font-bold uppercase">
                                        {{ $booking->ticketCategory->name ?? 'Ticket' }}
                                    </div>
                                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-t-[6px] border-t-slate-900/95"></div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Pintu masuk -->
        <div class="text-[9px] text-slate-400 font-extrabold uppercase tracking-[2px] mt-8">
            🚪 ENTRANCE
        </div>
    </div>

    <!-- Legend for fallback map -->
    <div class="pt-6 border-t border-gray-100 flex flex-col items-center gap-3.5 w-full text-[10px] font-bold uppercase tracking-wider text-gray-500">
        <!-- Ticket Categories Row -->
        <div class="flex items-center justify-center gap-6 flex-wrap">
            <span class="text-[9px] text-gray-400">Kelas Tiket:</span>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-md bg-amber-50 border border-amber-200"></div>
                <span>VIP/VVIP</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-md bg-purple-50 border border-purple-200"></div>
                <span>Silver/Platinum</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-md bg-blue-50 border border-blue-200"></div>
                <span>Regular</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-md bg-green-50 border border-green-200"></div>
                <span>Lainnya</span>
            </div>
        </div>
        <!-- Occupancy Status Row -->
        <div class="flex items-center justify-center gap-6 flex-wrap">
            <span class="text-[9px] text-gray-400">Status Kursi:</span>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-md bg-slate-100 border border-slate-250 opacity-60"></div>
                <span>Terisi (Booked)</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-md bg-red-50 border border-red-200"></div>
                <span>Diblokir (Blocked)</span>
            </div>
        </div>
    </div>
@endif
