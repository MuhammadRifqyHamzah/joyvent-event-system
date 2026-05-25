@extends('admin.layouts.app')

@section('title', 'Events Management')

@section('content')

<div class="space-y-8">

    <!-- Top Action -->
    <div class="flex justify-end">

        <<a href="{{ route('admin.events.create') }}"
            class="px-6 py-4 bg-blue-600 text-white rounded-2xl shadow-lg hover:bg-blue-700 transition">

            + Create Event

        </a>

    </div>

    <!-- Card -->
    <div
        class="bg-white/90 backdrop-blur-lg rounded-[32px] shadow-sm border border-gray-100 overflow-hidden">

        <!-- Header -->
        <div class="p-8 border-b flex justify-between items-center">

            <div>

                <h2 class="text-4xl font-bold text-gray-800">
                    List Events
                </h2>

                <p class="text-gray-500 mt-2">
                    Kelola semua event JoyVent dengan mudah 😄
                </p>

            </div>

            <!-- Search -->
            <div class="relative">

                <span
                    class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg">

                    🔍

                </span>

                <input
                    type="text"
                    placeholder="Search event..."
                    class="border border-gray-200 bg-gray-50 rounded-2xl pl-12 pr-5 py-4 w-80 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                >

            </div>

        </div>

        <!-- Table -->
        <div class="overflow-x-auto">

            <table class="w-full">

                <!-- Header -->
                <thead
                    class="bg-gradient-to-r from-gray-50 to-blue-50 text-gray-600 uppercase text-sm">

                    <tr>

                        <th class="text-left px-8 py-5 font-semibold">
                            Event
                        </th>

                        <th class="text-left px-8 py-5 font-semibold">
                            Location
                        </th>

                        <th class="text-left px-8 py-5 font-semibold">
                            Date
                        </th>

                        <th class="text-left px-8 py-5 font-semibold">
                            Capacity
                        </th>

                        <th class="text-left px-8 py-5 font-semibold">
                            Status
                        </th>

                        <th class="text-left px-8 py-5 font-semibold">
                            Action
                        </th>

                    </tr>

                </thead>

                <!-- Body -->
                <tbody>

                    @forelse($events as $event)

                    <tr
                        class="border-b hover:bg-blue-50/70 transition duration-200">

                        <!-- Event -->
                        <td class="px-8 py-6">

                            <div class="flex items-start gap-4">

                                <!-- Icon -->
                                <div
                                    class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-2xl">

                                    🎫

                                </div>

                                <!-- Content -->
                                <div>

                                    <h3
                                        class="font-bold text-xl text-gray-800">

                                        {{ $event->name }}

                                    </h3>

                                    <p
                                        class="text-gray-500 text-sm mt-2 max-w-sm">

                                        {{ $event->description }}

                                    </p>

                                </div>

                            </div>

                        </td>

                        <!-- Location -->
                        <td class="px-8 py-6 text-gray-700">

                            📍 {{ $event->location }}

                        </td>

                        <!-- Date -->
                        <td class="px-8 py-6 text-gray-700">

                            📅 {{ $event->start_date }}

                        </td>

                        <!-- Capacity -->
                        <td class="px-8 py-6 text-gray-700">

                            👥 {{ $event->capacity }} peserta

                        </td>

                        <!-- Status -->
                        <td class="px-8 py-6">

                            @if($event->status == 'active')

                                <span
                                    class="bg-green-100 text-green-700 px-5 py-2 rounded-full text-sm font-semibold shadow-sm">

                                    Active

                                </span>

                            @elseif($event->status == 'draft')

                                <span
                                    class="bg-yellow-100 text-yellow-700 px-5 py-2 rounded-full text-sm font-semibold shadow-sm">

                                    Draft

                                </span>

                            @else

                                <span
                                    class="bg-gray-200 text-gray-700 px-5 py-2 rounded-full text-sm font-semibold shadow-sm">

                                    Finished

                                </span>

                            @endif

                        </td>

                        <!-- Action -->
                        <td class="px-8 py-6">

                            <div class="flex gap-3">

                                <!-- Edit -->
                                <button
                                    class="bg-blue-100 hover:bg-blue-200 hover:scale-105 text-blue-700 px-5 py-3 rounded-2xl text-sm font-semibold transition">

                                    ✏️ Edit

                                </button>

                                <!-- Delete -->
                                <button
                                    class="bg-red-100 hover:bg-red-200 hover:scale-105 text-red-700 px-5 py-3 rounded-2xl text-sm font-semibold transition">

                                    🗑 Delete

                                </button>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="6"
                            class="text-center py-20 text-gray-500">

                            Belum ada event 😄

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection