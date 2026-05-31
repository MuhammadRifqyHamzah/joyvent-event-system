<div id="sidebar" class="bg-white border-r min-h-screen flex flex-col justify-between flex-shrink-0">
 
    <!-- Top Branding -->
    <div>
 
        <!-- Logo -->
        <div class="px-8 py-10">
 
            <h1 class="text-5xl font-extrabold">
                <span class="text-black">Joy</span><span class="text-blue-600">Vent</span>
            </h1>
 
            <p class="text-gray-400 tracking-[6px] text-sm mt-2 uppercase">
                Event System
            </p>
 
        </div>
 
        <!-- Navigation Menu -->
        <div class="px-6 space-y-2">
 
            <!-- Dashboard -->
            <a href="/admin/dashboard"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition duration-200
                {{ request()->is('admin/dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
 
                <!-- Dashboard Grid Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
 
                <span>Dashboard</span>
 
            </a>
 
            <!-- Events -->
            <a href="/admin/events"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition duration-200
                {{ request()->is('admin/events') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
 
                <!-- Events Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6v12m0-12A2.25 2.25 0 0 1 6 3.75h12A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6ZM6 7.5h12m-12 4.5h12m-12 4.5h12" />
                </svg>
 
                <span>Events</span>
 
            </a>
 
            <!-- Reports -->
            <a href="/admin/reports"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition duration-200
                {{ request()->is('admin/reports') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
 
                <!-- Reports Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v5.25c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 0 1 3 18.375v-5.25ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125v-9.75ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v14.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                </svg>
 
                <span>Reports</span>
 
            </a>
 
            <!-- Notifications -->
            <a href="/admin/notifications"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition duration-200
                {{ request()->is('admin/notifications') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
 
                <!-- Bell Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
 
                <span>Notifications</span>
 
            </a>
 
            <!-- Settings -->
            <a href="/admin/settings"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition duration-200
                {{ request()->is('admin/settings') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
 
                <!-- Gear Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.43l1.004-.827c.292-.24.437-.613.43-.991a6.936 6.936 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
 
                <span>Settings</span>
 
            </a>
 
        </div>
 
    </div>
 
    <!-- Logout -->
    <div class="p-6">
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full bg-red-500 hover:bg-red-600 text-white py-4 rounded-2xl font-bold shadow-lg transition cursor-pointer select-none">
                Logout
            </button>
        </form>
    </div>
 
</div>