@extends('admin.layouts.app')

@section('title', 'Create Event')

@section('content')

    <div class="space-y-8">

        {{-- VALIDATION ERROR --}}
        @if ($errors->any())

            <div class="bg-red-100 border border-red-300 text-red-700 px-5 py-4 rounded-2xl">

                <ul class="list-disc pl-5">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif

        <!-- Header -->
        <div class="flex justify-between items-center">

            <div>

                <h1 class="text-4xl font-bold text-gray-800">
                    Create Event
                </h1>

                <p class="text-gray-500 mt-2">
                    Buat event baru untuk JoyVent 😄
                </p>

            </div>

            <!-- Back -->
            <a href="/admin/events"
                class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-6 py-4 rounded-2xl font-semibold transition">

                ← Kembali

            </a>

        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-10">

            <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">

                @csrf

                <!-- Grid -->
                <div class="grid grid-cols-2 gap-10">

                    <!-- LEFT -->
                    <div>

                        <!-- Event Name -->
                        <div class="mb-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Nama Event
                            </label>

                            <input type="text" name="name" value="{{ old('name') }}"
                                placeholder="Masukkan nama event"
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500">

                        </div>

                        <!-- Event Category -->
                        <div class="mb-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Kategori Event
                            </label>

                            <select name="category" required
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="Entertainment" {{ old('category') == 'Entertainment' ? 'selected' : '' }}>Entertainment</option>
                                <option value="Education" {{ old('category') == 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Sports" {{ old('category') == 'Sports' ? 'selected' : '' }}>Sports</option>
                                <option value="Business" {{ old('category') == 'Business' ? 'selected' : '' }}>Business</option>
                                <option value="Community" {{ old('category') == 'Community' ? 'selected' : '' }}>Community</option>
                            </select>

                        </div>

                        <!-- Description -->
                        <div class="mb-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Deskripsi Event
                            </label>

                            <textarea name="description" rows="6" placeholder="Deskripsi event..."
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>

                        </div>

                        <!-- Location -->
                        <div class="mb-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Lokasi
                            </label>

                            <input type="text" name="location" value="{{ old('location') }}"
                                placeholder="Contoh: Bekasi Convention Center"
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500">

                        </div>

                        <!-- Google Maps URL -->
                        <div class="mb-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Google Maps Link
                            </label>

                            <input type="url" name="google_maps_url" value="{{ old('google_maps_url') }}"
                                placeholder="https://maps.app.goo.gl/xxxxx"
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500">

                            <p class="text-xs text-gray-400 mt-2 font-semibold">
                                Buka Google Maps → Share → Copy Link → Tempel di sini
                            </p>

                        </div>

                    </div>

                    <!-- RIGHT -->
                    <div>

                        <!-- Start Date -->
                        <div class="mb-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Tanggal Mulai
                            </label>

                            <input type="date" name="start_date" value="{{ old('start_date') }}"
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500">

                        </div>

                        <!-- End Date -->
                        <div class="mb-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Tanggal Selesai
                            </label>

                            <input type="date" name="end_date" value="{{ old('end_date') }}"
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500">

                        </div>

                        <!-- Start Time -->
                        <div class="mb-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Jam Mulai
                            </label>

                            <input type="time" name="start_time" value="{{ old('start_time') }}"
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500">

                        </div>

                        <!-- End Time -->
                        <div class="mb-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Jam Selesai
                            </label>

                            <input type="time" name="end_time" value="{{ old('end_time') }}"
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500">

                        </div>



                        <!-- Capacity -->
                        <div class="mb-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Kapasitas Peserta
                            </label>

                            <input type="number" name="capacity" value="{{ old('capacity') }}" placeholder="300"
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500">

                        </div>

                        <!-- Banner -->
                        <div class="mb-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Banner Event
                            </label>

                            <input type="file" name="banner"
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4">

                        </div>

                    </div>

                </div>

                <!-- Features -->
                <div class="mt-10">

                    <h3 class="text-2xl font-bold text-gray-800 mb-6">
                        Fitur Event
                    </h3>

                    <div class="flex gap-10">

                        <!-- Certificate -->
                        <label class="flex items-center gap-3 text-gray-700">

                            <input type="checkbox" name="has_certificate" value="1"
                                {{ old('has_certificate') ? 'checked' : '' }}>

                            Certificate

                        </label>

                        <!-- Seat -->
                        <label class="flex items-center gap-3 text-gray-700">

                            <input type="checkbox" name="has_seat_layout" value="1"
                                {{ old('has_seat_layout') ? 'checked' : '' }}>

                            Seat Management

                        </label>

                        <!-- Lucky Draw -->
                        <label class="flex items-center gap-3 text-gray-700">

                            <input type="checkbox" name="has_lucky_draw" value="1"
                                {{ old('has_lucky_draw') ? 'checked' : '' }}>

                            Lucky Draw

                        </label>

                    </div>

                </div>

                <!-- Submit -->
                <div class="flex justify-end mt-12">

                    <button type="submit"
                        class="bg-gradient-to-r from-blue-600 to-blue-500 hover:scale-105 hover:shadow-2xl text-white px-8 py-4 rounded-2xl font-semibold shadow-lg transition duration-300">

                        🚀 Create Event

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
