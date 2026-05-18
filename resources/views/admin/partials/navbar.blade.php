<div class="bg-white border-b px-10 py-6 flex justify-between items-center">

    <!-- Title -->
    <div>

        <h1 class="text-5xl font-bold text-gray-800">
            {{ $title ?? 'Dashboard' }}
        </h1>

    </div>

    <!-- Right -->
    <div class="flex items-center gap-6">

        <!-- Notification -->
        <div class="relative">

            <button class="text-2xl">
                🔔
            </button>

            <span
                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-6 h-6 flex items-center justify-center rounded-full">
                3
            </span>

        </div>

        <!-- Profile -->
        <div class="flex items-center gap-4">

            <div
                class="w-14 h-14 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-xl">

                A

            </div>

            <div>

                <h3 class="font-bold text-lg">
                    Admin
                </h3>

                <p class="text-gray-500">
                    Event Organizer
                </p>

            </div>

        </div>

    </div>

</div>