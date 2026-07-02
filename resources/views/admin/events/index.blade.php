@extends('admin.layouts.app')
 
@section('title', 'Events Management')
 
@section('content')
 
<div class="space-y-3 md:space-y-8">

    {{-- Session Success Notification --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4.5 rounded-2xl flex items-center gap-3 shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-green-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if(request('status') === 'open')
        <!-- ========================================================================== -->
        <!-- ON-GOING EVENTS MONITORING CARDS LAYOUT -->
        <!-- ========================================================================== -->
        
        <!-- Header: Title, Description, Search and Create Event -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2 md:gap-6 pb-1.5 border-b border-gray-150/40 md:border-b-0 mb-1.5 md:mb-0">
            <div>
                <h1 class="text-xl sm:text-3xl md:text-4xl font-extrabold text-gray-800 tracking-tight flex items-center gap-2 md:gap-3">
                    <span>Ongoing Events</span>
                    <!-- Pulsing Green Dot -->
                    <span class="relative flex h-3 w-3 md:h-3.5 md:w-3.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 md:h-3.5 md:w-3.5 bg-green-500"></span>
                    </span>
                    <span class="bg-green-50 text-green-600 border border-green-100/50 px-2.5 py-1 md:px-3.5 md:py-1.5 rounded-full text-[10px] md:text-xs font-bold tracking-wide uppercase">
                        Monitoring
                    </span>
                </h1>
                <p class="text-gray-400 text-[11px] md:text-sm mt-0.5 md:mt-2 font-semibold">
                    Realtime event monitoring and attendance tracking dashboard.
                </p>
            </div>

            <!-- Action Controls (Filter + Search + Create Event) -->
            <div class="flex flex-col md:flex-row items-stretch md:items-center gap-2 md:gap-3.5 w-full md:w-auto mt-1 md:mt-0">
                
                <!-- Tab Filter Buttons -->
                <div class="flex items-center bg-gray-100/60 p-0.5 md:p-1 rounded-full border border-gray-200/40 gap-1 self-start md:self-auto h-[36px] md:h-[42px]">
                    <button type="button" data-filter="all" class="h-[30px] md:h-[34px] px-3 md:px-4 rounded-full text-[11px] md:text-xs font-semibold transition duration-200 select-none cursor-pointer filter-btn active bg-slate-900 text-white shadow-sm flex items-center justify-center whitespace-nowrap">
                        All Events
                    </button>
                    <button type="button" data-filter="ongoing" class="h-[30px] md:h-[34px] px-3 md:px-4 rounded-full text-[11px] md:text-xs font-semibold transition duration-200 select-none cursor-pointer filter-btn text-gray-500 hover:text-gray-955 hover:bg-gray-150/50 flex items-center justify-center whitespace-nowrap">
                        On-going
                    </button>
                    <button type="button" data-filter="upcoming" class="h-[30px] md:h-[34px] px-3 md:px-4 rounded-full text-[11px] md:text-xs font-semibold transition duration-200 select-none cursor-pointer filter-btn text-gray-500 hover:text-gray-955 hover:bg-gray-150/50 flex items-center justify-center whitespace-nowrap">
                        Upcoming
                    </button>
                    <button type="button" data-filter="finished" class="h-[30px] md:h-[34px] px-3 md:px-4 rounded-full text-[11px] md:text-xs font-semibold transition duration-200 select-none cursor-pointer filter-btn text-gray-500 hover:text-gray-955 hover:bg-gray-150/50 flex items-center justify-center whitespace-nowrap">
                        Finished
                    </button>
                </div>

                <div class="flex items-center gap-2 md:gap-3.5 flex-1 md:flex-none">
                    <!-- Search Input -->
                    <div class="relative w-60 h-[36px] md:h-[42px] flex-1 md:flex-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input 
                            type="text" 
                            id="eventSearch" 
                            placeholder="Search ongoing event..." 
                            class="border border-gray-200/80 bg-gray-50/50 rounded-full pl-11 pr-4 w-full h-full focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-semibold text-gray-700 placeholder-gray-400"
                        >
                    </div>

                    <!-- Create Button -->
                    <a href="{{ route('admin.events.create') }}" class="px-4 md:px-5 h-[36px] md:h-[42px] bg-blue-600 hover:bg-blue-700 text-white rounded-full font-bold text-[11px] md:text-xs shadow-sm transition flex items-center justify-center gap-1.5 whitespace-nowrap cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Create Event</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Monitoring Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mt-4 md:mt-8">
            @foreach($events as $event)
                @php
                    $attendanceProgress = $event->registrations_count > 0 
                        ? round(($event->attended_count / $event->registrations_count) * 100) 
                        : 0;
                @endphp
                <div class="bg-white rounded-3xl p-4 md:p-6 border border-gray-100/80 shadow-sm flex flex-col justify-between transition hover:-translate-y-1 hover:shadow-md duration-300 relative group overflow-hidden event-card"
                     data-name="{{ $event->name }}"
                     data-location="{{ $event->location }}"
                     data-desc="{{ $event->description }}"
                     data-start-date="{{ $event->start_date }}"
                     data-end-date="{{ $event->end_date }}"
                     data-start-time="{{ $event->start_time }}"
                     data-end-time="{{ $event->end_time }}">
                     
                     <!-- Top Glowing Accent -->
                     <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-green-400 to-emerald-500"></div>
                     
                     <div>
                          <!-- Card Top Header -->
                          <div class="flex justify-between items-center mb-3 md:mb-4">
                             <span class="live-badge flex items-center gap-1.5 bg-green-50 text-green-600 border border-green-100/50 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider">
                                 <span class="relative flex h-1.5 w-1.5">
                                     <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                     <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-green-500"></span>
                                 </span>
                                 LIVE
                             </span>
                         </div>
                         
                         <!-- Title -->
                         <h3 class="text-lg font-extrabold text-gray-800 tracking-tight leading-snug group-hover:text-blue-600 transition-colors">
                             <a href="{{ route('admin.events.ongoing', $event->id) }}">
                                 {{ $event->name }}
                             </a>
                         </h3>
                         
                         <!-- Category Badge -->
                         @php
                             $categoryColors = [
                                 'Entertainment' => 'bg-purple-50 text-purple-600 border border-purple-100/50',
                                 'Education' => 'bg-indigo-50 text-indigo-600 border border-indigo-100/50',
                                 'Sports' => 'bg-emerald-50 text-emerald-600 border border-emerald-100/50',
                                 'Business' => 'bg-blue-50 text-blue-600 border border-blue-100/50',
                                 'Community' => 'bg-amber-50 text-amber-600 border border-amber-100/50',
                             ];
                             $catColor = $categoryColors[$event->category] ?? 'bg-gray-50 text-gray-600 border border-gray-150';
                         @endphp
                         <div class="mt-2 flex">
                             <span class="{{ $catColor }} px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                 {{ $event->category }}
                             </span>
                         </div>
                         
                         <!-- Metadata: Location, Date & Countdown -->
                         <div class="space-y-1.5 md:space-y-2 mt-3.5 md:mt-4 pb-3 md:pb-4 border-b border-gray-100">
                             <div class="flex items-center gap-2 text-gray-500 font-semibold text-xs">
                                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0">
                                     <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                     <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                 </svg>
                                 <span class="truncate">{{ $event->location }}</span>
                             </div>
                             
                             <div class="flex items-center gap-2 text-gray-500 font-semibold text-xs">
                                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0">
                                     <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                 </svg>
                                 <span>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}</span>
                             </div>

                             <div class="flex items-center gap-2 text-rose-500 font-extrabold text-xs">
                                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-rose-500 flex-shrink-0">
                                     <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                 </svg>
                                 <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mr-0.5">Ends In:</span>
                                 <span data-countdown="{{ $event->end_date }} {{ $event->end_time }}">Calculating...</span>
                             </div>
                         </div>
                         
                         <!-- Attendance Rate Progress Bar -->
                         <div class="mt-3.5 md:mt-4">
                             <div class="flex justify-between items-center text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1.5">
                                 <span>Attendance Progress</span>
                                 <span class="text-gray-700 font-extrabold text-xs">
                                     {{ $attendanceProgress }}%
                                 </span>
                             </div>
                             
                             <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden shadow-inner relative">
                                 <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-full rounded-full transition-all duration-700" 
                                      style="width: {{ $attendanceProgress }}%"></div>
                             </div>
                             
                             <div class="flex justify-between items-center mt-2 text-[10px] text-gray-450 font-bold">
                                 <span>{{ number_format($event->attended_count) }} Checked-In</span>
                                 <span>{{ number_format($event->registrations_count) }} Registered</span>
                             </div>
                         </div>
                     </div>
                     
                     <!-- Card Action Buttons -->
                     <div class="mt-4 md:mt-5 pt-3 md:pt-4 border-t border-gray-100 flex gap-2">
                         <a href="{{ route('admin.events.ongoing', $event->id) }}" 
                            class="flex-1 text-center py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-sm hover:shadow transition duration-200 cursor-pointer select-none">
                             Monitor Event
                         </a>
                         <a href="{{ route('admin.events.ongoing', $event->id) }}" 
                            class="px-3 py-2.5 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100/50 text-indigo-600 rounded-xl font-bold text-xs transition cursor-pointer flex items-center justify-center select-none"
                            title="View Details">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.43 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                             </svg>
                         </a>
                         <a href="{{ route('admin.tickets.index', $event->id) }}" 
                            class="px-3 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200/50 text-gray-650 rounded-xl font-bold text-xs transition cursor-pointer flex items-center justify-center select-none"
                            title="Ticket Categories">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-12v.75m0 3v.75m0 3v.75m0 3V18M3 8.25a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 8.25V15.75a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15.75V8.25Z" />
                             </svg>
                         </a>
                     </div>
                </div>
            @endforeach

            <!-- Empty State Card (Visible initially if empty, or shown dynamically by JS when all events expire) -->
            <div id="emptyState" class="{{ $events->isEmpty() ? '' : 'hidden' }} col-span-3 bg-white rounded-[32px] border border-gray-100/80 p-20 text-center flex flex-col items-center justify-center shadow-sm w-full">
                <div class="w-20 h-20 rounded-3xl bg-amber-50 flex items-center justify-center text-amber-500 mb-6 border border-amber-100/50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">No ongoing events found</h3>
                <p class="text-gray-400 font-semibold mt-2 max-w-sm">
                    There are currently no events flagged as ongoing or active.
                </p>
            </div>
        </div>

        <!-- No Results Search Placeholder Card (hidden by default) -->
        <div id="noResultsCard" class="hidden bg-white rounded-[32px] border border-gray-100/80 p-20 text-center flex flex-col items-center justify-center shadow-sm w-full col-span-3">
            <div class="w-20 h-20 rounded-3xl bg-slate-50 flex items-center justify-center text-slate-400 mb-6 border border-slate-100/50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.637 10.637Z" />
                </svg>
            </div>
            <h3 class="text-2xl font-extrabold text-gray-800 tracking-tight">No results found</h3>
            <p class="text-gray-400 font-semibold mt-2 max-w-sm">
                We couldn't find any ongoing events matching your search terms.
            </p>
        </div>

    @else
        <!-- ========================================================================== -->
        <!-- STANDARD LIST VIEW TABLE LAYOUT (DEFAULT) -->
        <!-- ========================================================================== -->
        
        <!-- White Card Container -->
        <div class="bg-white rounded-2xl md:rounded-[32px] shadow-sm border border-gray-100/80 overflow-hidden p-3 sm:p-6 md:p-10">

            <!-- Header: Title, Description, Search and Create Event -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2 md:gap-6 pb-2 md:pb-8 border-b border-gray-100 mb-2.5 md:mb-8">
                
                <div>
                    <h2 class="text-xl sm:text-3xl md:text-4xl font-extrabold text-gray-800 tracking-tight">
                        List Events
                    </h2>
                    <p class="text-gray-400 text-[11px] md:text-sm mt-0.5 md:mt-2 font-semibold">
                        Kelola semua event JoyVent dengan mudah dan cepat.
                    </p>
                </div>

                 <!-- Action Controls (Search + Filter + Create Event) -->
                 <div class="flex flex-col md:flex-row items-stretch md:items-center gap-2 md:gap-3.5 w-full md:w-auto">
                     
                     <!-- Tab Filter Buttons -->
                     <div class="flex items-center bg-gray-100/60 p-0.5 md:p-1 rounded-full border border-gray-200/40 gap-1 self-start md:self-auto h-[36px] md:h-[42px]">
                         <button type="button" data-filter="all" class="h-[30px] md:h-[34px] px-3 md:px-4 rounded-full text-[11px] md:text-xs font-semibold transition duration-200 select-none cursor-pointer filter-btn active bg-slate-900 text-white shadow-sm flex items-center justify-center whitespace-nowrap">
                             All Events
                         </button>
                         <button type="button" data-filter="ongoing" class="h-[30px] md:h-[34px] px-3 md:px-4 rounded-full text-[11px] md:text-xs font-semibold transition duration-200 select-none cursor-pointer filter-btn text-gray-500 hover:text-gray-955 hover:bg-gray-150/50 flex items-center justify-center whitespace-nowrap">
                             On-going
                         </button>
                         <button type="button" data-filter="upcoming" class="h-[30px] md:h-[34px] px-3 md:px-4 rounded-full text-[11px] md:text-xs font-semibold transition duration-200 select-none cursor-pointer filter-btn text-gray-500 hover:text-gray-955 hover:bg-gray-150/50 flex items-center justify-center whitespace-nowrap">
                             Upcoming
                         </button>
                         <button type="button" data-filter="finished" class="h-[30px] md:h-[34px] px-3 md:px-4 rounded-full text-[11px] md:text-xs font-semibold transition duration-200 select-none cursor-pointer filter-btn text-gray-500 hover:text-gray-955 hover:bg-gray-150/50 flex items-center justify-center whitespace-nowrap">
                             Finished
                         </button>
                     </div>
 
                     <div class="flex items-center gap-2 md:gap-3.5 flex-1 md:flex-none">
                         <!-- Search Input -->
                         <div class="relative w-60 h-[36px] md:h-[42px] flex-1 md:flex-none">
                             <!-- Magnifying glass outline icon -->
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                             </svg>
                             <input 
                                 type="text" 
                                 id="eventSearch" 
                                 placeholder="Search event..." 
                                 class="border border-gray-200/80 bg-gray-50/50 rounded-full pl-11 pr-4 w-full h-full focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition text-xs font-semibold text-gray-700 placeholder-gray-400"
                             >
                         </div>
 
                         <!-- Create Event Button -->
                         <a href="{{ route('admin.events.create') }}" class="px-4 md:px-5 h-[36px] md:h-[42px] bg-blue-600 hover:bg-blue-700 text-white rounded-full font-bold text-[11px] md:text-xs shadow-sm transition flex items-center justify-center gap-1.5 whitespace-nowrap cursor-pointer">
                             <!-- Plus outline icon -->
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-white">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                             </svg>
                             <span>Create Event</span>
                         </a>
                     </div>
 
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
                            data-location="{{ $event->location }}"
                            data-start-date="{{ $event->start_date }}"
                            data-end-date="{{ $event->end_date }}"
                            data-start-time="{{ $event->start_time }}"
                            data-end-time="{{ $event->end_time }}">
                            
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
                                        <h3 class="font-bold text-gray-800 text-base leading-snug hover:text-blue-600 transition-colors">
                                            <a href="{{ route('admin.events.show', $event->id) }}">
                                                {{ $event->name }}
                                            </a>
                                        </h3>
                                        <!-- Category Badge -->
                                        @php
                                            $categoryColors = [
                                                'Entertainment' => 'bg-purple-50 text-purple-600 border border-purple-100/50',
                                                'Education' => 'bg-indigo-50 text-indigo-600 border border-indigo-100/50',
                                                'Sports' => 'bg-emerald-50 text-emerald-600 border border-emerald-100/50',
                                                'Business' => 'bg-blue-50 text-blue-600 border border-blue-100/50',
                                                'Community' => 'bg-amber-50 text-amber-600 border border-amber-100/50',
                                            ];
                                            $catColor = $categoryColors[$event->category] ?? 'bg-gray-50 text-gray-600 border border-gray-150';
                                        @endphp
                                        <div class="mt-2 flex">
                                            <span class="{{ $catColor }} px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider">
                                                {{ $event->category }}
                                            </span>
                                        </div>
                                        <p class="text-gray-400 text-xs mt-1.5 line-clamp-1 max-w-[280px] font-medium">
                                            {{ $event->description }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Location -->
                            <td class="py-5 pl-4 pr-4">
                                <span class="flex items-center gap-1.5">
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
                                    $now = \Carbon\Carbon::now();
                                    $startDate = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time);
                                    $endDate = \Carbon\Carbon::parse($event->end_date . ' ' . $event->end_time);

                                    if ($now->between($startDate, $endDate)) {
                                        $statusLabel = 'On-going';
                                        $colorClass = 'bg-green-50 text-green-600 border border-green-100/50';
                                    } elseif ($startDate->isFuture()) {
                                        $statusLabel = 'Upcoming';
                                        $colorClass = 'bg-blue-50 text-blue-600 border border-blue-100/50';
                                    } else {
                                        $statusLabel = 'Finished';
                                        $colorClass = 'bg-slate-100 text-slate-700 border border-slate-200/60';
                                    }
                                @endphp
                                <span class="{{ $colorClass }} px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide whitespace-nowrap computed-status-badge">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            <!-- Action Row -->
                            <td class="py-5">
                                <div class="flex items-center gap-2.5">
                                    
                                    <a href="{{ route('admin.events.show', $event->id) }}" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-4 py-2 rounded-xl text-xs font-bold tracking-wide transition flex items-center gap-1.5 border border-indigo-100/50 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-indigo-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.43 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <span>View Details</span>
                                    </a>

                                    <a href="{{ route('admin.events.edit', $event->id) }}" class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-4 py-2 rounded-xl text-xs font-bold tracking-wide transition flex items-center gap-1.5 border border-blue-100/50 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-blue-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        <span>Edit</span>
                                    </a>

                                    <button type="button" onclick="triggerDelete('{{ $event->id }}', '{{ addslashes($event->name) }}')" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-xl text-xs font-bold tracking-wide transition flex items-center gap-1.5 border border-red-100/50 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-red-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        <span>Delete</span>
                                    </button>

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
    @endif

</div>

<!-- JavaScript: Client-Side Instant Search, Delete Confirm, and Realtime Countdown -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Search & Filter state variables
        let currentFilter = 'all';
        let searchQuery = '';

        // DOM elements cache
        const eventSearches = document.querySelectorAll('#eventSearch');
        const filterBtns = document.querySelectorAll('.filter-btn');
        const rows = document.querySelectorAll('tbody tr.event-row');
        const cards = document.querySelectorAll('.event-card');
        const noResultsRow = document.getElementById('noResultsRow');
        const noResultsCard = document.getElementById('noResultsCard');

        // Helper: Get computed status based on current browser time
        function getComputedStatus(element) {
            const startDateStr = element.getAttribute('data-start-date');
            const endDateStr = element.getAttribute('data-end-date');
            const startTimeStr = element.getAttribute('data-start-time') || '00:00:00';
            const endTimeStr = element.getAttribute('data-end-time') || '23:59:59';

            if (!startDateStr || !endDateStr) return 'finished';

            const now = new Date();
            const startDateTime = new Date(`${startDateStr}T${startTimeStr}`);
            const endDateTime = new Date(`${endDateStr}T${endTimeStr}`);

            if (now >= startDateTime && now <= endDateTime) {
                return 'ongoing';
            } else if (startDateTime > now) {
                return 'upcoming';
            } else {
                return 'finished';
            }
        }

        // Helper: Update status badges in DOM to ensure browser local time alignment
        function updateStatusBadges() {
            rows.forEach(row => {
                const status = getComputedStatus(row);
                const badge = row.querySelector('.computed-status-badge');
                if (badge) {
                    badge.className = 'px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide whitespace-nowrap computed-status-badge';
                    if (status === 'ongoing') {
                        badge.textContent = 'On-going';
                        badge.classList.add('bg-green-50', 'text-green-600', 'border', 'border-green-100/50');
                    } else if (status === 'upcoming') {
                        badge.textContent = 'Upcoming';
                        badge.classList.add('bg-blue-50', 'text-blue-600', 'border', 'border-blue-100/50');
                    } else {
                        badge.textContent = 'Finished';
                        badge.classList.add('bg-slate-100', 'text-slate-700', 'border', 'border-slate-200/60');
                    }
                }
            });
        }

        // Initialize status badges on load
        updateStatusBadges();

        // Logic Helper: Matches Filter Status
        function matchesFilter(element, filterValue) {
            if (filterValue === 'all') return true;
            return getComputedStatus(element) === filterValue;
        }

        // Logic Helper: Matches Search Query
        function matchesSearch(element, query) {
            if (!query) return true;
            const name = (element.getAttribute('data-name') || '').toLowerCase();
            const desc = (element.getAttribute('data-desc') || '').toLowerCase();
            const location = (element.getAttribute('data-location') || '').toLowerCase();
            return name.includes(query) || desc.includes(query) || location.includes(query);
        }

        // Combined Filter and Search Processor
        function applyFilterAndSearch() {
            let hasTableResults = false;
            let hasCardResults = false;

            // Apply to standard view table rows
            rows.forEach(row => {
                const filterMatch = matchesFilter(row, currentFilter);
                const searchMatch = matchesSearch(row, searchQuery);

                if (filterMatch && searchMatch) {
                    row.style.display = '';
                    hasTableResults = true;
                } else {
                    row.style.display = 'none';
                }
            });

            // Apply to monitoring view cards
            cards.forEach(card => {
                const filterMatch = matchesFilter(card, currentFilter);
                const searchMatch = matchesSearch(card, searchQuery);

                if (filterMatch && searchMatch) {
                    card.style.display = '';
                    hasCardResults = true;
                } else {
                    card.style.display = 'none';
                }
            });

            // Toggle Empty State Row for Tables
            if (rows.length > 0) {
                if (noResultsRow) {
                    if (!hasTableResults) {
                        noResultsRow.classList.remove('hidden');
                    } else {
                        noResultsRow.classList.add('hidden');
                    }
                }
            }

            // Toggle Empty State Card for Grid
            if (cards.length > 0) {
                if (noResultsCard) {
                    if (!hasCardResults) {
                        noResultsCard.classList.remove('hidden');
                    } else {
                        noResultsCard.classList.add('hidden');
                    }
                }
            }
        }

        // Set up search event listeners
        eventSearches.forEach(searchEl => {
            searchEl.addEventListener('input', (e) => {
                searchQuery = e.target.value.toLowerCase().trim();
                
                // Sync all search input elements
                eventSearches.forEach(el => {
                    if (el !== e.target) {
                        el.value = e.target.value;
                    }
                });
                
                applyFilterAndSearch();
            });
        });

        // Set up filter buttons event listeners
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const selectedFilter = btn.getAttribute('data-filter');
                currentFilter = selectedFilter;

                // Update styling active states for ALL filter controls on the page
                filterBtns.forEach(b => {
                    const bFilter = b.getAttribute('data-filter');
                    
                    // Clear all styles first
                    b.classList.remove(
                        'active', 'bg-white', 'text-blue-600', 'shadow-sm', 'border', 'border-gray-100',
                        'bg-slate-800', 'bg-slate-900', 'bg-emerald-500', 'bg-blue-600', 'bg-rose-500', 'text-white',
                        'text-gray-500', 'hover:text-gray-850', 'hover:text-gray-955', 'hover:bg-gray-100/40',
                        'hover:text-emerald-600', 'hover:text-blue-600', 'hover:text-rose-600',
                        'hover:bg-emerald-50/50', 'hover:bg-blue-50/50', 'hover:bg-rose-50/50',
                        'hover:text-gray-950', 'hover:bg-gray-150/50'
                    );

                    if (bFilter === selectedFilter) {
                        b.classList.add('active', 'bg-slate-900', 'text-white', 'shadow-sm');
                    } else {
                        b.classList.add('text-gray-500', 'hover:text-gray-955', 'hover:bg-gray-150/50');
                    }
                });

                applyFilterAndSearch();
            });
        });
        
        // 2. Realtime Countdown Script
        @if(request('status') === 'open')
        function updateCountdowns() {
            const elements = document.querySelectorAll('[data-countdown]');
            
            elements.forEach(el => {
                const endDateStr = el.getAttribute('data-countdown');
                const endDate = new Date(endDateStr.replace(' ', 'T')).getTime();
                const now = new Date().getTime();
                const diff = endDate - now;
                
                if (isNaN(endDate) || diff <= 0) {
                    const card = el.closest('.event-card');
                    if (card) {
                        card.remove(); // Automatically hides/removes card when event has ended
                    }
                } else {
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    
                    let text = "";
                    if (days > 0) {
                        text += `${days}d `;
                    }
                    text += `${hours.toString().padStart(2, '0')}h ${minutes.toString().padStart(2, '0')}m ${seconds.toString().padStart(2, '0')}s`;
                    el.innerHTML = text;
                }
            });
            
            // Check if all cards have been removed, to display empty state
            const remainingCards = document.querySelectorAll('.event-card');
            if (remainingCards.length === 0) {
                const emptyState = document.getElementById('emptyState');
                if (emptyState) {
                    emptyState.classList.remove('hidden');
                }
            }
        }
        
        setInterval(updateCountdowns, 1000);
        updateCountdowns();
        @endif
    });

    // 3. Confirm Delete dialogue
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