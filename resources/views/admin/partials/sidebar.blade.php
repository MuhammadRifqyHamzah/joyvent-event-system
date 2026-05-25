<div class="w-72 bg-white border-r min-h-screen flex flex-col justify-between">

    <!-- Top -->
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

        <!-- Menu -->
        <div class="px-6 space-y-3">

            <!-- Dashboard -->
            <a href="/admin/dashboard"
                class="{{ request()->is('admin/dashboard') ? 'bg-blue-100 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}
                flex items-center gap-3 px-5 py-4 rounded-2xl font-semibold transition">

                📊 Dashboard

            </a>

            <!-- Events -->
            <a href="/admin/events"
                class="{{ request()->is('admin/events') ? 'bg-blue-100 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}
                flex items-center gap-3 px-5 py-4 rounded-2xl font-semibold transition">

                🎫 Events

            </a>

            <!-- Participants -->
            <a href="#"
                class="text-gray-700 hover:bg-gray-100 flex items-center gap-3 px-5 py-4 rounded-2xl font-semibold transition">

                👥 Participants

            </a>

            <!-- Seats -->
            <a href="#"
                class="text-gray-700 hover:bg-gray-100 flex items-center gap-3 px-5 py-4 rounded-2xl font-semibold transition">

                💺 Seats

            </a>

            <!-- Lucky Draw -->
            <a href="#"
                class="text-gray-700 hover:bg-gray-100 flex items-center gap-3 px-5 py-4 rounded-2xl font-semibold transition">

                🎉 Lucky Draw

            </a>

            <!-- Certificates -->
            <a href="#"
                class="text-gray-700 hover:bg-gray-100 flex items-center gap-3 px-5 py-4 rounded-2xl font-semibold transition">

                🏆 Certificates

            </a>

        </div>

    </div>

    <!-- Logout -->
    <div class="p-6">

        <button
            class="bg-red-500 hover:bg-red-600 text-white px-8 py-4 rounded-2xl font-semibold shadow-lg transition">

            Logout

        </button>

    </div>

</div>