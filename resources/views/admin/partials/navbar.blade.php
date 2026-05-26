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
        <div class="relative">
 
            <button id="notificationBell" class="w-12 h-12 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full flex items-center justify-center transition focus:outline-none relative shadow-sm cursor-pointer">
                <!-- Bell SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
 
                <!-- Red Notification Badge -->
                <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full border-2 border-white shadow-sm">
                    3
                </span>
            </button>
 
            <!-- Notification Dropdown Overlay -->
            <div id="notificationDropdown" class="hidden absolute right-0 mt-4 w-96 bg-white border border-gray-100 rounded-3xl shadow-xl z-50 p-6 space-y-4 transition-all duration-300">
                
                <!-- Dropdown Header -->
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <h4 class="font-extrabold text-gray-800 text-sm tracking-tight">
                        Email & Pesan Masuk
                    </h4>
                    <span class="text-xs bg-blue-50 text-blue-600 font-bold px-2.5 py-1 rounded-full">
                        3 Baru
                    </span>
                </div>
                
                <!-- Email Notifications List -->
                <div class="space-y-4">
                    
                    <!-- Email Item 1 -->
                    <div class="flex gap-3.5 items-start hover:bg-slate-50/60 p-3 rounded-2xl transition duration-150 cursor-pointer">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0 border border-blue-100/50">
                            <!-- Email Outline SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-blue-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-800 leading-normal">
                                <strong class="font-extrabold text-gray-900 block text-sm leading-none mb-1">rifqy@gmail.com</strong> 
                                mengirim: "Mohon info cara download sertifikat."
                            </p>
                            <span class="text-[10px] text-gray-400 font-bold flex items-center gap-1 mt-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>10 mins ago</span>
                            </span>
                        </div>
                    </div>
 
                    <!-- Email Item 2 -->
                    <div class="flex gap-3.5 items-start hover:bg-slate-50/60 p-3 rounded-2xl transition duration-150 cursor-pointer">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0 border border-blue-100/50">
                            <!-- Email Outline SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-blue-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-800 leading-normal">
                                <strong class="font-extrabold text-gray-900 block text-sm leading-none mb-1">budi.santoso@gmail.com</strong> 
                                mengirim: "Tanya ketersediaan kursi VIP Konser."
                            </p>
                            <span class="text-[10px] text-gray-400 font-bold flex items-center gap-1 mt-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>2 hours ago</span>
                            </span>
                        </div>
                    </div>
 
                    <!-- Email Item 3 -->
                    <div class="flex gap-3.5 items-start hover:bg-slate-50/60 p-3 rounded-2xl transition duration-150 cursor-pointer">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0 border border-blue-100/50">
                            <!-- Email Outline SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-blue-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-800 leading-normal">
                                <strong class="font-extrabold text-gray-900 block text-sm leading-none mb-1">anisa.fitria@gmail.com</strong> 
                                mengirim: "Konfirmasi pendaftaran seminar teknologi."
                            </p>
                            <span class="text-[10px] text-gray-400 font-bold flex items-center gap-1 mt-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>1 day ago</span>
                            </span>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Dropdown Footer -->
                <div class="border-t border-gray-50 pt-3 text-center">
                    <a href="#" class="text-xs text-blue-600 font-bold hover:underline transition">
                        Tandai semua dibaca
                    </a>
                </div>
                
            </div>
 
        </div>
 
        <!-- Profile info -->
        <div class="flex items-center gap-4">
 
            <div class="w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center font-bold text-xl shadow-sm transition">
                A
            </div>
 
            <div class="leading-tight">
 
                <h3 class="font-extrabold text-gray-800 text-base">
                    Admin
                </h3>
 
                <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider">
                    Event Organizer
                </p>
 
            </div>
 
        </div>
 
    </div>
 
</div>