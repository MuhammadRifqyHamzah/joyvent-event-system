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
 
            <!-- Participants -->
            <a href="{{ route('admin.participants.index') }}"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition duration-200
                {{ request()->is('admin/participants*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
 
                <!-- User Group Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0 1 10.089 20.4c-2.114 0-4.082-.54-5.8-1.485a4.125 4.125 0 0 1 7.533-2.493c.501.91.786 1.957.786 3.07v-.003m-2.225-7.61c.642.457 1.412.728 2.247.728 2.21 0 4-1.79 4-4s-1.79-4-4-4c-.835 0-1.605.271-2.247.728m2.247 7.272a3.5 3.5 0 1 1-4.5 0H12.75a3.5 3.5 0 0 1-1.003-7.272M7.75 12a2.75 2.75 0 1 0 0-5.5 2.75 2.75 0 0 0 0 5.5Z" />
                </svg>
 
                <span>Participants</span>
 
            </a>
 
            <!-- Seats -->
            <a href="{{ route('admin.seats.index') }}"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition duration-200
                {{ request()->is('admin/seats*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
 
                <!-- Seats Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6c0-1.243 1.007-2.25 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5Z" />
                </svg>
 
                <span>Seats</span>
 
            </a>
 
            <!-- Lucky Draw -->
            <a href="{{ route('admin.lucky_draw.index') }}"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition duration-200
                {{ request()->is('admin/lucky-draw*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
 
                <!-- Gift Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0-2.625V7.5m0 12.75V7.5m-9 0h18" />
                </svg>
 
                <span>Lucky Draw</span>
 
            </a>
 
            <!-- Certificates -->
            <a href="{{ route('admin.certificates.index') }}"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition duration-200
                {{ request()->is('admin/certificates*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
 
                <!-- Badge/Shield Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                </svg>
 
                <span>Certificates</span>
 
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