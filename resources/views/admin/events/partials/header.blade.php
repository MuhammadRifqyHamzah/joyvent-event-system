@php
    $eventStatus = $event->calculated_status;

    if ($eventStatus === 'finished') {
        $statusText = 'Finished';
        $statusColor = 'bg-slate-100 text-slate-700 border border-slate-200/60';
        $gradientColor = 'from-slate-600 to-slate-800';
        $timeDetails = 'Ended on ' . \Carbon\Carbon::parse($event->end_date)->format('d M Y') . ' (' . \Carbon\Carbon::parse($event->end_time)->format('H:i') . ')';
    } elseif ($eventStatus === 'upcoming') {
        $statusText = 'Upcoming';
        $statusColor = 'bg-blue-50 text-blue-600 border border-blue-100/50';
        $gradientColor = 'from-blue-600 to-indigo-600';
        $timeDetails = 'Starts on ' . \Carbon\Carbon::parse($event->start_date)->format('d M Y') . ' (' . \Carbon\Carbon::parse($event->start_time)->format('H:i') . ')';
    } else {
        $statusText = 'Ongoing';
        $statusColor = 'bg-green-50 text-green-600 border border-green-150';
        $gradientColor = 'from-green-600 to-emerald-600';
        $timeDetails = 'Ends on ' . \Carbon\Carbon::parse($event->end_date)->format('d M Y') . ' (' . \Carbon\Carbon::parse($event->end_time)->format('H:i') . ')';
    }

    // Dynamic Gradient Category Cover
    switch($event->category) {
        case 'Entertainment':
            $coverGradient = 'from-purple-600 to-indigo-600';
            break;
        case 'Education':
            $coverGradient = 'from-blue-600 to-cyan-600';
            break;
        case 'Sports':
            $coverGradient = 'from-emerald-600 to-teal-600';
            break;
        case 'Business':
            $coverGradient = 'from-amber-600 to-orange-600';
            break;
        case 'Community':
            $coverGradient = 'from-rose-600 to-red-600';
            break;
        default:
            $coverGradient = $gradientColor;
    }
@endphp

<!-- Event Banner Cover Header -->
<div class="relative w-full h-64 md:h-72 bg-gradient-to-r {{ $coverGradient }} rounded-[32px] p-8 md:p-10 flex flex-col justify-end overflow-hidden shadow-sm border border-white/10"
     @if($event->banner_image) style="background-image: url('{{ $event->banner_image }}'); background-size: cover; background-position: center;" @endif>
    
    @if($event->banner_image)
        <div class="absolute inset-0 bg-black/50 pointer-events-none z-0"></div>
    @else
        <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute right-32 bottom-5 w-36 h-36 rounded-full bg-white/5 blur-xl pointer-events-none"></div>
    @endif

    <div class="relative z-10 space-y-4">
        <div class="flex items-center gap-3 flex-wrap">
            <span class="bg-white/20 backdrop-blur text-white px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider border border-white/20">
                {{ $event->category }}
            </span>
            <span class="{{ $statusColor }} bg-opacity-95 px-3.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide">
                {{ $statusText }}
            </span>
        </div>

        <h2 class="text-white text-3xl md:text-5xl font-black tracking-tight leading-tight drop-shadow-sm truncate max-w-4xl">
            {{ $event->name }}
        </h2>

        <div class="flex items-center gap-6 text-white/90 text-sm font-semibold flex-wrap">
            <span class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-white/70">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                <span>{{ $event->location }}</span>
            </span>
            @if($eventStatus === 'ongoing')
                <span class="flex items-center gap-2 text-rose-350">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-rose-450 flex-shrink-0 animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span class="font-extrabold uppercase tracking-wide text-xs">Ends In:</span>
                    <span id="countdownTimer" data-end-time="{{ $event->end_date }} {{ $event->end_time }}" class="font-black">Calculating...</span>
                </span>
            @else
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2050/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-white/70">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    <span>{{ $timeDetails }}</span>
                </span>
            @endif
        </div>
    </div>
</div>

@php
    $activeTab = request()->query('tab', 'overview');
@endphp

<!-- Tab Navigation Bar -->
<div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden p-2 mt-6">
    <div class="flex flex-wrap items-center gap-1">
        <button onclick="switchTab('overview')" id="tab-btn-overview" 
            class="tab-btn flex items-center gap-2 px-6 py-3.5 rounded-2xl text-sm transition duration-200 cursor-pointer 
            {{ $activeTab === 'overview' ? 'bg-blue-50 text-blue-600 font-extrabold shadow-xs' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-bold' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            <span>Overview</span>
        </button>

        <button onclick="switchTab('participants')" id="tab-btn-participants" 
            class="tab-btn flex items-center gap-2 px-6 py-3.5 rounded-2xl text-sm transition duration-200 cursor-pointer 
            {{ $activeTab === 'participants' ? 'bg-blue-50 text-blue-600 font-extrabold shadow-xs' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-bold' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0010.089 20.8M15 19.128a11.386 11.386 0 004.911-1.562M7.5 19.128a9.38 9.38 0 01-2.625.372 9.337 9.337 0 01-4.121-.952 4.125 4.125 0 017.533-2.493M7.5 19.128v-.003c0-1.113.285-2.16.786-3.07M7.5 19.128v.109A11.386 11.386 0 0012.41 20.8M7.5 19.128a11.386 11.386 0 01-4.911-1.562m4.911-7.453A3 3 0 118.5 8c0 .351-.06.688-.172 1M12.41 14.8a8.979 8.979 0 01-4.91 1.562 8.979 8.979 0 01-4.91-1.562m9.82 0a8.979 8.979 0 00-4.91-1.562 8.979 8.979 0 00-4.91 1.562m4.91-1.562V9.75M12 3a3 3 0 110 6 3 3 0 010-6z" />
            </svg>
            <span>Participants</span>
        </button>

        @if($event->has_seat_layout)
        <button onclick="switchTab('seats')" id="tab-btn-seats" 
            class="tab-btn flex items-center gap-2 px-6 py-3.5 rounded-2xl text-sm transition duration-200 cursor-pointer 
            {{ $activeTab === 'seats' ? 'bg-blue-50 text-blue-600 font-extrabold shadow-xs' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-bold' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6ZM18 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6c0-1.243 1.007-2.25 2.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5Z" />
            </svg>
            <span>Seats</span>
        </button>
        @endif

        @if($event->has_lucky_draw)
        <button onclick="switchTab('lucky_draw')" id="tab-btn-lucky_draw" 
            class="tab-btn flex items-center gap-2 px-6 py-3.5 rounded-2xl text-sm transition duration-200 cursor-pointer 
            {{ $activeTab === 'lucky_draw' ? 'bg-blue-50 text-blue-600 font-extrabold shadow-xs' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-bold' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625A2.625 2.625 0 1114.625 7.5H12m0-2.625V7.5m0 12.75V7.5m-9 0h18" />
            </svg>
            <span>Lucky Draw</span>
        </button>
        @endif

        @if($event->has_certificate)
        <button onclick="switchTab('certificates')" id="tab-btn-certificates" 
            class="tab-btn flex items-center gap-2 px-6 py-3.5 rounded-2xl text-sm transition duration-200 cursor-pointer 
            {{ $activeTab === 'certificates' ? 'bg-blue-50 text-blue-600 font-extrabold shadow-xs' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-bold' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12z" />
            </svg>
            <span>Certificates</span>
        </button>
        @endif

        <button onclick="switchTab('refunds')" id="tab-btn-refunds" 
            class="tab-btn flex items-center gap-2 px-6 py-3.5 rounded-2xl text-sm transition duration-200 cursor-pointer 
            {{ $activeTab === 'refunds' ? 'bg-blue-50 text-blue-600 font-extrabold shadow-xs' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-bold' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-1.971-.618-1.172-.879-1.172-2.303 0-3.182 1.172-.879 3.07-.879 4.242 0L15 8.818M12 3v18" />
            </svg>
            <span>Refunds</span>
        </button>
    </div>
</div>
