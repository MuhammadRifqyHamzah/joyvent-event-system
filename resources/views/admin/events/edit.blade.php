@extends('admin.layouts.app')
 
@section('title', 'Edit Event')
 
@section('content')
 
    <div class="space-y-8">
 
        {{-- VALIDATION ERROR --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-6 py-5 rounded-2xl">
                <ul class="list-disc pl-5 font-bold text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
 
        <!-- Header -->
        <div class="flex justify-between items-center">
            
            <div>
                <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">
                    Edit Event
                </h1>
                <p class="text-gray-400 text-sm mt-2 font-semibold">
                    Perbarui informasi event Anda untuk JoyVent 😄
                </p>
            </div>
 
            <!-- Back -->
            <a href="{{ route('admin.events') }}"
                class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-6 py-3.5 rounded-2xl font-bold text-sm transition shadow-sm cursor-pointer">
                ← Kembali
            </a>
 
        </div>
 
        <!-- Form Card -->
        <div class="bg-white rounded-[32px] shadow-sm border border-gray-100/80 p-10">
 
            <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                
                @csrf
                @method('PUT')
 
                <!-- Grid layout -->
                <div class="grid grid-cols-2 gap-10">
 
                    <!-- LEFT COLUMN -->
                    <div>
 
                        <!-- Event Name -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider text-xs">
                                Nama Event
                            </label>
                            <input type="text" name="name" value="{{ old('name', $event->name) }}"
                                placeholder="Masukkan nama event"
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700">
                        </div>
 
                        <!-- Event Category -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider text-xs">
                                Kategori Event
                            </label>
                            <select name="category" required
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-bold text-gray-700 bg-white">
                                <option value="Entertainment" {{ old('category', $event->category) == 'Entertainment' ? 'selected' : '' }}>Entertainment</option>
                                <option value="Education" {{ old('category', $event->category) == 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Sports" {{ old('category', $event->category) == 'Sports' ? 'selected' : '' }}>Sports</option>
                                <option value="Business" {{ old('category', $event->category) == 'Business' ? 'selected' : '' }}>Business</option>
                                <option value="Community" {{ old('category', $event->category) == 'Community' ? 'selected' : '' }}>Community</option>
                            </select>
                        </div>
 
                        <!-- Description -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider text-xs">
                                Deskripsi Event
                            </label>
                            <textarea name="description" rows="6" placeholder="Deskripsi event..."
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700">{{ old('description', $event->description) }}</textarea>
                        </div>
 
                        <!-- Location -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider text-xs">
                                Lokasi
                            </label>
                            <input type="text" name="location" value="{{ old('location', $event->location) }}"
                                placeholder="Contoh: Bekasi Convention Center"
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700">
                        </div>
 
                    </div>
 
                    <!-- RIGHT COLUMN -->
                    <div>
 
                        <!-- Start Date & End Date Grid -->
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider text-xs">
                                    Tanggal Mulai
                                </label>
                                <input type="date" name="start_date" value="{{ old('start_date', $event->start_date) }}"
                                    class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700">
                            </div>
 
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider text-xs">
                                    Tanggal Selesai
                                </label>
                                <input type="date" name="end_date" value="{{ old('end_date', $event->end_date) }}"
                                    class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700">
                            </div>
                        </div>
 
                        <!-- Start Time & End Time Grid -->
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider text-xs">
                                    Jam Mulai
                                </label>
                                <input type="time" name="start_time" value="{{ old('start_time', $event->start_time) }}"
                                    class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700">
                            </div>
 
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider text-xs">
                                    Jam Selesai
                                </label>
                                <input type="time" name="end_time" value="{{ old('end_time', $event->end_time) }}"
                                    class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700">
                            </div>
                        </div>
 
                        <!-- Capacity & Banner Grid -->
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider text-xs">
                                    Kapasitas Peserta
                                </label>
                                <input type="number" name="capacity" value="{{ old('capacity', $event->capacity) }}" placeholder="300"
                                    class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700">
                            </div>
 
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider text-xs">
                                    Banner Event (Opsional)
                                </label>
                                <input type="file" name="banner"
                                    class="w-full border border-gray-200 rounded-2xl px-5 py-4 font-semibold text-gray-500">
                            </div>
                        </div>
 
                    </div>
 
                </div>
 
                <!-- Features List -->
                <div class="mt-10 border-t border-gray-50 pt-8">
                    
                    <h3 class="text-2xl font-extrabold text-gray-800 mb-6 tracking-tight">
                        Fitur Event
                    </h3>
 
                    <div class="flex gap-10">
                        
                        <!-- Certificate -->
                        <label class="flex items-center gap-3 text-gray-700 font-bold text-sm cursor-pointer select-none">
                            <input type="checkbox" name="has_certificate" value="1"
                                {{ old('has_certificate', $event->has_certificate) ? 'checked' : '' }}
                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span>Certificate</span>
                        </label>
 
                        <!-- Seat -->
                        <label class="flex items-center gap-3 text-gray-700 font-bold text-sm cursor-pointer select-none">
                            <input type="checkbox" name="has_seat_layout" value="1"
                                {{ old('has_seat_layout', $event->has_seat_layout) ? 'checked' : '' }}
                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span>Seat Management</span>
                        </label>
 
                        <!-- Lucky Draw -->
                        <label class="flex items-center gap-3 text-gray-700 font-bold text-sm cursor-pointer select-none">
                            <input type="checkbox" name="has_lucky_draw" value="1"
                                {{ old('has_lucky_draw', $event->has_lucky_draw) ? 'checked' : '' }}
                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span>Lucky Draw</span>
                        </label>
 
                    </div>
 
                </div>
 
                <!-- Submit Buttons -->
                <div class="flex justify-end gap-4 mt-12 border-t border-gray-50 pt-8">
                    
                    <a href="{{ route('admin.events') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-8 py-4 rounded-2xl font-bold text-sm shadow-sm transition">
                        Batal
                    </a>
 
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-bold text-sm shadow-sm transition">
                        💾 Update Event
                    </button>
 
                </div>
 
            </form>
 
        </div>
 
    </div>
 
@endsection
