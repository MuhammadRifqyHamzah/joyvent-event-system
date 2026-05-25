@extends('admin.layouts.app')

@section('title', 'Dashboard Overview')

@section('content')

<div class="space-y-8">

    <!-- Statistik Cards -->
    <div class="grid grid-cols-4 gap-6">

        <!-- Card -->
        <div class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition">

            <div class="flex justify-between items-start">

                <div>
                    <p class="text-gray-500 font-semibold uppercase text-sm">
                        Total Event
                    </p>

                    <h1 class="text-5xl font-bold mt-5">
                        124
                    </h1>

                    <p class="text-green-500 font-semibold mt-4 text-sm">
                        ↗ +12% Bulan ini
                    </p>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-2xl">
                    📅
                </div>

            </div>

        </div>

        <!-- Card -->
        <div class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition">

            <div class="flex justify-between items-start">

                <div>
                    <p class="text-gray-500 font-semibold uppercase text-sm">
                        Total Peserta
                    </p>

                    <h1 class="text-5xl font-bold mt-5">
                        8,432
                    </h1>

                    <p class="text-green-500 font-semibold mt-4 text-sm">
                        ↗ +5.4% vs kemarin
                    </p>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-2xl">
                    👥
                </div>

            </div>

        </div>

        <!-- Card -->
        <div class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition">

            <div class="flex justify-between items-start">

                <div>
                    <p class="text-gray-500 font-semibold uppercase text-sm">
                        Total Check-In
                    </p>

                    <h1 class="text-5xl font-bold mt-5">
                        6,120
                    </h1>

                    <p class="text-blue-500 font-semibold mt-4 text-sm">
                        ⚡ 72.5% Attendance
                    </p>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-2xl">
                    ✅
                </div>

            </div>

        </div>

        <!-- Card -->
        <div class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition">

            <div class="flex justify-between items-start">

                <div>
                    <p class="text-gray-500 font-semibold uppercase text-sm">
                        Total Certificate
                    </p>

                    <h1 class="text-5xl font-bold mt-5">
                        5,890
                    </h1>

                    <p class="text-gray-500 font-semibold mt-4 text-sm">
                        ⏱ 230 Pending
                    </p>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-2xl">
                    🏆
                </div>

            </div>

        </div>

    </div>

    <!-- Chart Section -->
    <div class="grid grid-cols-3 gap-6">

        <!-- Bar Chart -->
        <div class="col-span-2 bg-white rounded-3xl p-8 shadow-sm">

            <div class="flex justify-between items-center mb-10">

                <h2 class="text-3xl font-bold">
                    Pertumbuhan Peserta
                </h2>

                <button class="bg-gray-100 px-5 py-2 rounded-xl text-gray-700">
                    7 Hari Terakhir
                </button>

            </div>

            <!-- Fake Chart -->
            <div class="flex items-end gap-6 h-80">

                <div class="flex flex-col items-center gap-3">
                    <div class="w-16 h-28 bg-blue-200 rounded-t-2xl"></div>
                    <span>Sen</span>
                </div>

                <div class="flex flex-col items-center gap-3">
                    <div class="w-16 h-40 bg-blue-300 rounded-t-2xl"></div>
                    <span>Sel</span>
                </div>

                <div class="flex flex-col items-center gap-3">
                    <div class="w-16 h-32 bg-blue-200 rounded-t-2xl"></div>
                    <span>Rab</span>
                </div>

                <div class="flex flex-col items-center gap-3">
                    <div class="w-16 h-56 bg-blue-500 rounded-t-2xl"></div>
                    <span>Kam</span>
                </div>

                <div class="flex flex-col items-center gap-3">
                    <div class="w-16 h-44 bg-blue-300 rounded-t-2xl"></div>
                    <span>Jum</span>
                </div>

                <div class="flex flex-col items-center gap-3">
                    <div class="w-16 h-32 bg-blue-200 rounded-t-2xl"></div>
                    <span>Sab</span>
                </div>

                <div class="flex flex-col items-center gap-3">
                    <div class="w-16 h-20 bg-blue-100 rounded-t-2xl"></div>
                    <span>Min</span>
                </div>

            </div>

        </div>

        <!-- Donut -->
        <div class="bg-white rounded-3xl p-8 shadow-sm">

            <div class="flex justify-between items-center mb-8">

                <h2 class="text-3xl font-bold">
                    Statistik Check-in
                </h2>

                <button>
                    ⋮
                </button>

            </div>

            <div class="flex justify-center">

                <div class="relative w-56 h-56 rounded-full border-[22px] border-gray-200">

                    <div class="absolute inset-0 rounded-full border-[22px] border-blue-500 border-t-transparent rotate-45"></div>

                    <div class="absolute inset-0 flex flex-col items-center justify-center">

                        <h1 class="text-5xl font-bold">
                            72%
                        </h1>

                        <p class="text-gray-500 mt-2">
                            Check-in
                        </p>

                    </div>

                </div>

            </div>

            <div class="mt-10 space-y-4">

                <div class="flex items-center gap-3">
                    <div class="w-4 h-4 bg-blue-500 rounded-full"></div>

                    <span class="font-semibold">
                        Hadir 6,120
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-4 h-4 bg-gray-300 rounded-full"></div>

                    <span class="font-semibold">
                        Belum Hadir 2,312
                    </span>
                </div>

            </div>

        </div>

    </div>

    <!-- Bottom Section -->
    <div class="grid grid-cols-3 gap-6">

        <!-- Table -->
        <div class="col-span-2 bg-white rounded-3xl shadow-sm overflow-hidden">

            <div class="flex justify-between items-center p-8 border-b">

                <h2 class="text-3xl font-bold">
                    Event Terbaru
                </h2>

                <button class="text-blue-600 font-semibold">
                    Lihat Semua
                </button>

            </div>

            <table class="w-full">

                <thead class="bg-gray-50 text-gray-500 uppercase text-sm">

                    <tr>

                        <th class="text-left px-8 py-5">
                            Nama Event
                        </th>

                        <th class="text-left px-8 py-5">
                            Tanggal
                        </th>

                        <th class="text-left px-8 py-5">
                            Lokasi
                        </th>

                        <th class="text-left px-8 py-5">
                            Peserta
                        </th>

                        <th class="text-left px-8 py-5">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr class="border-b hover:bg-gray-50">

                        <td class="px-8 py-6 font-semibold">
                            Tech Summit 2024
                        </td>

                        <td class="px-8 py-6">
                            24 Okt 2023
                        </td>

                        <td class="px-8 py-6">
                            Jakarta
                        </td>

                        <td class="px-8 py-6">
                            1,250
                        </td>

                        <td class="px-8 py-6">
                            <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm">
                                Aktif
                            </span>
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <!-- Activity -->
        <div class="bg-white rounded-3xl shadow-sm p-8">

            <h2 class="text-3xl font-bold mb-8">
                Aktivitas Terbaru
            </h2>

            <div class="space-y-8">

                <div class="flex gap-4">

                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        👤
                    </div>

                    <div>
                        <p>
                            <span class="font-bold">Andi Wijaya</span>
                            baru check-in.
                        </p>

                        <p class="text-gray-400 text-sm mt-1">
                            2 menit lalu
                        </p>
                    </div>

                </div>

                <div class="flex gap-4">

                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        🏆
                    </div>

                    <div>
                        <p>
                            50 sertifikat berhasil dibuat.
                        </p>

                        <p class="text-gray-400 text-sm mt-1">
                            15 menit lalu
                        </p>
                    </div>

                </div>

                <div class="flex gap-4">

                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        🎲
                    </div>

                    <div>
                        <p>
                            Lucky draw selesai dilakukan.
                        </p>

                        <p class="text-gray-400 text-sm mt-1">
                            1 jam lalu
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection