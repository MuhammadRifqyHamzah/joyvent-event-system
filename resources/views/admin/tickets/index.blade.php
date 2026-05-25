@extends('admin.layouts.app')

@section('title', 'Ticket Categories')

@section('content')

<div class="space-y-8">

    <!-- Header -->
    <div class="flex justify-between items-center">

        <div>

            <h1 class="text-4xl font-bold text-gray-800">
                Ticket Categories
            </h1>

            <p class="text-gray-500 mt-2">
                Event:
                <span class="font-semibold">
                    {{ $event->name }}
                </span>
            </p>

        </div>

        <button
            class="bg-gradient-to-r from-blue-600 to-blue-500 hover:scale-105 hover:shadow-2xl text-white px-7 py-4 rounded-2xl font-semibold shadow-lg transition duration-300">

            ➕ Add Ticket

        </button>

    </div>

    <!-- Card -->
    <div
        class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden">

        <!-- Header -->
        <div class="p-8 border-b flex justify-between items-center">

            <div>

                <h2 class="text-3xl font-bold text-gray-800">
                    List Ticket Categories
                </h2>

                <p class="text-gray-500 mt-2">
                    Kelola semua kategori tiket 😄
                </p>

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
                            Ticket
                        </th>

                        <th class="text-left px-8 py-5 font-semibold">
                            Price
                        </th>

                        <th class="text-left px-8 py-5 font-semibold">
                            Quota
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

                    @forelse($tickets as $ticket)

                    <tr
                        class="border-b hover:bg-blue-50/70 transition duration-200">

                        <!-- Ticket -->
                        <td class="px-8 py-6">

                            <div class="flex items-start gap-4">

                                <div
                                    class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-2xl">

                                    🎫

                                </div>

                                <div>

                                    <h3
                                        class="font-bold text-xl text-gray-800">

                                        {{ $ticket->name }}

                                    </h3>

                                    <p
                                        class="text-gray-500 text-sm mt-2">

                                        {{ $ticket->description }}

                                    </p>

                                </div>

                            </div>

                        </td>

                        <!-- Price -->
                        <td class="px-8 py-6 text-gray-700">

                            Rp {{ number_format($ticket->price) }}

                        </td>

                        <!-- Quota -->
                        <td class="px-8 py-6 text-gray-700">

                            {{ $ticket->quota }}

                        </td>

                        <!-- Status -->
                        <td class="px-8 py-6">

                            @if($ticket->is_active)

                                <span
                                    class="bg-green-100 text-green-700 px-5 py-2 rounded-full text-sm font-semibold shadow-sm">

                                    Active

                                </span>

                            @else

                                <span
                                    class="bg-gray-200 text-gray-700 px-5 py-2 rounded-full text-sm font-semibold shadow-sm">

                                    Inactive

                                </span>

                            @endif

                        </td>

                        <!-- Action -->
                        <td class="px-8 py-6">

                            <div class="flex gap-3">

                                <button
                                    class="bg-blue-100 hover:bg-blue-200 hover:scale-105 text-blue-700 px-5 py-3 rounded-2xl text-sm font-semibold transition">

                                    ✏️ Edit

                                </button>

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
                            colspan="5"
                            class="text-center py-20 text-gray-500">

                            Belum ada ticket category 😄

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection