@extends('admin.layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="space-y-8">
    
    <!-- Success Banner -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4.5 rounded-2xl flex items-center gap-3 shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-green-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Warning Banner -->
    @if(session('warning'))
        <div class="bg-amber-50 border border-amber-100 text-amber-600 px-6 py-4.5 rounded-2xl flex items-center gap-3 shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-amber-600 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <span class="font-bold text-sm">{{ session('warning') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 pb-2">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight flex items-center gap-3">
                <span>🔔 Notifications</span>
            </h1>
            <p class="text-gray-400 text-sm mt-2 font-semibold">
                Pantau aktivitas terbaru yang terjadi di JoyVent.
            </p>
        </div>

        <!-- Mark All Read -->
        @php
            $unreadNotificationsCount = $notifications->where('is_read', false)->count();
        @endphp
        @if($unreadNotificationsCount > 0)
            <form action="{{ route('admin.notifications.mark_all_read') }}" method="POST" class="w-full md:w-auto">
                @csrf
                <button type="submit" class="w-full px-5 h-[46px] bg-slate-900 hover:bg-slate-800 text-white rounded-2xl font-bold text-xs shadow-md hover:shadow-lg transition flex items-center justify-center gap-2 cursor-pointer select-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>Tandai Semua Sudah Dibaca</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Filter Buttons Bar -->
    <div class="flex flex-wrap items-center gap-2.5">
        <button onclick="filterType('all')" data-tag="all" class="px-5 py-2.5 rounded-full text-xs font-bold transition border border-slate-900 bg-slate-900 text-white shadow-sm cursor-pointer select-none">
            All
        </button>
        <button onclick="filterType('unread')" data-tag="unread" class="px-5 py-2.5 rounded-full text-xs font-bold transition border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 cursor-pointer select-none">
            Unread
        </button>
        <button onclick="filterType('participants')" data-tag="participants" class="px-5 py-2.5 rounded-full text-xs font-bold transition border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 cursor-pointer select-none">
            Participants
        </button>
        <button onclick="filterType('events')" data-tag="events" class="px-5 py-2.5 rounded-full text-xs font-bold transition border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 cursor-pointer select-none">
            Events
        </button>
        <button onclick="filterType('check_in')" data-tag="check_in" class="px-5 py-2.5 rounded-full text-xs font-bold transition border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 cursor-pointer select-none">
            Check-In
        </button>
        <button onclick="filterType('refunds')" data-tag="refunds" class="px-5 py-2.5 rounded-full text-xs font-bold transition border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 cursor-pointer select-none">
            Refunds
        </button>
        <button onclick="filterType('lucky_draw')" data-tag="lucky_draw" class="px-5 py-2.5 rounded-full text-xs font-bold transition border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 cursor-pointer select-none">
            Lucky Draw
        </button>
        <button onclick="filterType('certificates')" data-tag="certificates" class="px-5 py-2.5 rounded-full text-xs font-bold transition border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 cursor-pointer select-none">
            Certificates
        </button>
    </div>

    <!-- Notifications List Box -->
    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8">
        
        <div class="space-y-4">
            @forelse($notifications as $notif)
                @php
                    // Set color and icon based on notification type
                    $badgeBg = 'bg-blue-50 text-blue-600 border-blue-100/50';
                    $icon = '🔔';
                    
                    if ($notif->type === 'participants') {
                        $badgeBg = 'bg-cyan-50 text-cyan-600 border-cyan-100/50';
                        $icon = '👤';
                    } elseif ($notif->type === 'check_in') {
                        $badgeBg = 'bg-emerald-50 text-emerald-600 border-emerald-100/50';
                        $icon = '✓';
                    } elseif ($notif->type === 'events') {
                        $badgeBg = 'bg-indigo-50 text-indigo-600 border-indigo-100/50';
                        $icon = '📅';
                    } elseif ($notif->type === 'refunds') {
                        $badgeBg = 'bg-amber-50 text-amber-600 border-amber-100/50';
                        $icon = '💵';
                    } elseif ($notif->type === 'lucky_draw') {
                        $badgeBg = 'bg-purple-50 text-purple-600 border-purple-100/50';
                        $icon = '🎁';
                    } elseif ($notif->type === 'certificates') {
                        $badgeBg = 'bg-teal-50 text-teal-600 border-teal-100/50';
                        $icon = '🎖';
                    }

                    $isClickable = !is_null($notif->event_id);
                @endphp
                <div class="notification-item flex flex-col md:flex-row md:items-center justify-between p-5 rounded-2xl border transition-all duration-200 transform {{ $isClickable ? 'hover:-translate-y-0.5 hover:shadow-md cursor-pointer' : 'opacity-85 cursor-default' }} relative gap-4
                     {{ !$notif->is_read ? 'bg-blue-50/15 border-blue-100/80' : 'bg-white border-gray-100' }}"
                     data-type="{{ $notif->type }}"
                     data-read="{{ $notif->is_read ? 'true' : 'false' }}"
                     @if($isClickable) onclick="window.location.href='{{ route('admin.notifications.click', $notif->id) }}'" @endif>
                    
                    <!-- Blue unread dot -->
                    @if(!$notif->is_read)
                        <span class="absolute left-3.5 top-1/2 transform -translate-y-1/2 w-2 h-2 bg-blue-500 rounded-full" title="Belum dibaca"></span>
                    @endif

                    <div class="flex items-start gap-4 {{ !$notif->is_read ? 'pl-4' : '' }}">
                        <!-- Styled icon badge -->
                        <div class="w-11 h-11 rounded-full flex items-center justify-center border flex-shrink-0 text-lg shadow-xs {{ $badgeBg }}">
                            {{ $icon }}
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-extrabold text-gray-800 text-sm leading-snug">
                                    {{ $notif->title }}
                                </h3>
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-md text-[9px] font-bold uppercase tracking-wider">
                                    {{ $notif->type }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 font-semibold leading-relaxed">
                                {{ $notif->message }}
                            </p>
                            <span class="text-[10px] text-gray-400 font-bold flex items-center gap-1.5 mt-2 tracking-wider">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>{{ $notif->created_at->diffForHumans() }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 self-end md:self-auto pl-15 md:pl-0" onclick="event.stopPropagation()">
                        @if(!$notif->is_read)
                            <form action="{{ route('admin.notifications.mark_read', $notif->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-50 hover:bg-blue-100 border border-blue-100/50 text-blue-600 rounded-xl font-bold text-xs transition cursor-pointer select-none">
                                    Tandai Sudah Dibaca
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-gray-400 font-bold flex items-center gap-1 px-3">
                                <span>✓ Read</span>
                            </span>
                        @endif

                        <form action="{{ route('admin.notifications.destroy', $notif->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-50 hover:bg-red-100 border border-red-100/50 text-red-600 rounded-xl font-bold text-xs transition cursor-pointer select-none">
                                Hapus Notifikasi
                            </button>
                        </form>
                    </div>

                </div>
            @empty
                <!-- Inherent Empty State if list completely blank -->
                <div class="py-16 text-center text-gray-400">
                    <div class="flex flex-col items-center justify-center gap-4">
                        <div class="w-16 h-16 rounded-3xl bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <div class="font-extrabold text-gray-700">Belum ada aktivitas baru.</div>
                    </div>
                </div>
            @endforelse

            <!-- JS Filter Empty State (Hidden initially) -->
            <div id="notificationsEmptyState" class="hidden py-16 text-center text-gray-400">
                <div class="flex flex-col items-center justify-center gap-4">
                    <div class="w-16 h-16 rounded-3xl bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div class="font-extrabold text-gray-700">Belum ada aktivitas baru.</div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- JavaScript dynamic filtering -->
<script>
    function filterType(type) {
        const items = document.querySelectorAll('.notification-item');
        let visibleCount = 0;

        // Sync active class styles for tags
        document.querySelectorAll('[data-tag]').forEach(btn => {
            const btnTag = btn.getAttribute('data-tag');
            btn.className = 'px-5 py-2.5 rounded-full text-xs font-bold transition border cursor-pointer select-none';
            if (btnTag === type) {
                btn.classList.add('bg-slate-900', 'text-white', 'border-slate-900', 'shadow-sm');
            } else {
                btn.classList.add('bg-white', 'text-gray-500', 'border-gray-200', 'hover:bg-gray-50');
            }
        });

        // Loop items and toggle visibility
        items.forEach(item => {
            const itemType = item.getAttribute('data-type');
            const isRead = item.getAttribute('data-read') === 'true';

            let isVisible = false;
            if (type === 'all') {
                isVisible = true;
            } else if (type === 'unread') {
                isVisible = !isRead;
            } else {
                isVisible = itemType === type;
            }

            if (isVisible) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Toggle Empty state view if count is 0
        const emptyState = document.getElementById('notificationsEmptyState');
        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }
</script>
@endsection
