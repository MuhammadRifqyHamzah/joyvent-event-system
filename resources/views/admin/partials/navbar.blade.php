<div class="bg-white border-b px-10 py-6 flex justify-between items-center relative">
 
    <!-- Left: Hamburger Toggle & Title -->
    <div class="flex items-center gap-5">
 
        <!-- Circular Hamburger Button -->
        <button id="toggleSidebar" class="w-12 h-12 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full flex items-center justify-center transition focus:outline-none shadow-sm cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
            </svg>
        </button>
 
        <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">
            {{ $title ?? 'Dashboard' }}
        </h1>
 
    </div>
 
    <!-- Right: Notification & Profile -->
    <div class="flex items-center gap-6">
 
        <!-- Styled Notification Bell Wrapper -->
        @php
            // Sync database logs and notifications
            \App\Models\Notification::checkTableAndSync();
            
            // Get unread notification counts
            $unreadCount = \App\Models\Notification::where('is_read', 0)->count();
            
            // Fetch 5 most recent active notifications
            $recentNotifications = \App\Models\Notification::whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        @endphp
        <div class="relative">
 
            <button id="notificationBell" class="w-12 h-12 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full flex items-center justify-center transition focus:outline-none relative shadow-sm cursor-pointer">
                <!-- Bell SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
 
                <!-- Red Notification Badge -->
                @if($unreadCount > 0)
                <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] font-bold w-6 h-6 flex items-center justify-center rounded-full border-2 border-white shadow-sm">
                    {{ $unreadCount }}
                </span>
                @endif
            </button>
 
            <!-- Notification Dropdown Overlay -->
            <div id="notificationDropdown" class="hidden absolute right-0 mt-4 w-96 bg-white border border-gray-100 rounded-3xl shadow-xl z-50 p-6 space-y-4 transition-all duration-300">
                
                <!-- Dropdown Header -->
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <h4 class="font-extrabold text-gray-800 text-sm tracking-tight">
                        Notifikasi Terbaru
                    </h4>
                    <span class="text-xs bg-blue-50 text-blue-600 font-bold px-2.5 py-1 rounded-full">
                        {{ $unreadCount }} Baru
                    </span>
                </div>
                
                <!-- Notifications List -->
                <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1">
                    @forelse($recentNotifications as $notif)
                        <a href="{{ route('admin.notifications.click', $notif->id) }}" class="flex gap-3 items-start hover:bg-slate-100 hover:shadow-sm p-2.5 rounded-2xl transition-all duration-200 transform hover:-translate-y-0.5 relative cursor-pointer block {{ !$notif->is_read ? 'bg-blue-50/20 border-l-4 border-blue-500 pl-1.5' : '' }}">
                            
                            @if(!$notif->is_read)
                            <span class="absolute right-3 top-3.5 w-2 h-2 bg-blue-500 rounded-full"></span>
                            @endif

                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0 border border-blue-100/50 text-xs">
                                🔔
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-700 leading-snug">
                                    <strong class="font-extrabold text-gray-900 block mb-0.5">{{ $notif->title }}</strong>
                                    {{ $notif->message }}
                                </p>
                                <span class="text-[9px] text-gray-400 font-bold flex items-center gap-1 mt-1.5 uppercase tracking-wider">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span>{{ $notif->created_at->diffForHumans() }}</span>
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8 text-xs text-gray-400 font-semibold">
                            Belum ada aktivitas baru.
                        </div>
                    @endforelse
                </div>
                
                <!-- Dropdown Footer -->
                <div class="border-t border-gray-50 pt-3 flex justify-between items-center text-xs">
                    @if($unreadCount > 0)
                    <form action="{{ route('admin.notifications.mark_all_read') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-blue-600 font-bold hover:underline bg-transparent border-0 cursor-pointer p-0 select-none">
                            Tandai semua dibaca
                        </button>
                    </form>
                    @else
                    <span class="text-gray-400">Sudah dibaca semua</span>
                    @endif
                    
                    <a href="{{ route('admin.notifications') }}" class="text-blue-600 font-bold hover:underline transition">
                        Lihat Semua
                    </a>
                </div>
                
            </div>
 
        </div>
 
        <!-- Profile info -->
        <div class="flex items-center gap-4">

            @if(auth()->user() && auth()->user()->profile_photo)
                <div class="w-12 h-12 rounded-full overflow-hidden shadow-sm transition flex-shrink-0 bg-slate-100 flex items-center justify-center">
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Avatar" class="w-full h-full object-cover">
                </div>
            @else
                <div class="w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center font-bold text-xl shadow-sm transition">
                    {{ auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}
                </div>
            @endif

            <div class="leading-tight">

                <h3 class="font-extrabold text-gray-800 text-base">
                    {{ auth()->user()->name ?? 'Admin' }}
                </h3>

                <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider">
                    Event Organizer
                </p>

            </div>

        </div>
 
    </div>
 
</div>