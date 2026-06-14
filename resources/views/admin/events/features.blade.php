@extends('admin.layouts.app')

@section('title', 'Event Feature Setup')

@section('content')

    <div class="space-y-8">

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-6 py-5 rounded-2xl">
                <ul class="list-disc pl-5 font-bold text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Progress Steps Indicator -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100/80 shadow-sm flex items-center justify-between max-w-4xl mx-auto w-full">
            <!-- Step 1: Create Event -->
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                    ✓
                </div>
                <div class="hidden sm:block">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Step 1</p>
                    <h4 class="text-xs font-bold text-gray-700">Create Event</h4>
                </div>
            </div>

            <!-- Line -->
            <div class="flex-1 h-0.5 bg-blue-100 mx-4 sm:mx-6"></div>

            <!-- Step 2: Tickets -->
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                    ✓
                </div>
                <div class="hidden sm:block">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Step 2</p>
                    <h4 class="text-xs font-bold text-gray-700">Tickets</h4>
                </div>
            </div>

            <!-- Line -->
            <div class="flex-1 h-0.5 bg-blue-100 mx-4 sm:mx-6"></div>

            <!-- Step 3: Features -->
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-sm ring-4 ring-blue-100">
                    3
                </div>
                <div>
                    <p class="text-[10px] text-blue-500 font-bold uppercase tracking-wider">Step 3</p>
                    <h4 class="text-xs font-bold text-blue-655">Features</h4>
                </div>
            </div>

            <!-- Line -->
            <div class="flex-1 h-0.5 bg-gray-100 mx-4 sm:mx-6"></div>

            <!-- Step 4: Complete -->
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 font-bold text-sm">
                    ★
                </div>
                <div class="hidden sm:block">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Step 4</p>
                    <h4 class="text-xs font-bold text-gray-400">Complete</h4>
                </div>
            </div>
        </div>

        <!-- Header -->
        <div class="flex justify-between items-center pb-2">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">
                    Event Feature Setup
                </h1>
                <p class="text-gray-400 text-sm mt-2 font-semibold flex items-center gap-1">
                    Configure features for:
                    <span class="text-gray-700 font-extrabold">
                        {{ $event->name }}
                    </span>
                </p>
            </div>
        </div>

        <form action="{{ route('admin.events.features.store', $event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- 1. Certificate Setup Card -->
            @if($event->has_certificate)
                <div class="bg-white rounded-[32px] shadow-sm border border-gray-100/80 p-8 space-y-6">
                    <div class="flex items-center gap-3 pb-5 border-b border-gray-100">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100/40">
                            <!-- Certificate outline icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-gray-800 tracking-tight">Certificate Settings</h2>
                            <p class="text-xs text-gray-400 font-semibold">Konfigurasikan judul dan template sertifikat peserta.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                Judul Sertifikat <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="certificate_title" required 
                                value="{{ old('certificate_title', $event->certificate_title ?? 'Certificate of Appreciation') }}" 
                                placeholder="Contoh: Certificate of Appreciation" 
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                Nama Penyelenggara / Organizer <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="organizer_name" required 
                                value="{{ old('organizer_name', $event->organizer_name ?? 'JoyVent Organizer') }}" 
                                placeholder="Contoh: JoyVent Event Ltd" 
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                Upload Template Background (Opsional, JPG/PNG, Max 4MB)
                            </label>
                            <input type="file" name="certificate_template" 
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 text-gray-500 font-semibold">
                            @if($event->certificate_template)
                                <p class="text-xs text-green-600 font-bold mt-2">✓ Template background has been uploaded</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                Upload Gambar Tanda Tangan / Signature (Opsional, PNG transparan, Max 2MB)
                            </label>
                            <input type="file" name="signature_image" 
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 text-gray-500 font-semibold">
                            @if($event->signature_image)
                                <p class="text-xs text-green-600 font-bold mt-2">✓ Signature image has been uploaded</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- 2. Seat Layout Setup Card -->
            @if($event->has_seat_layout)
                <div class="bg-white rounded-[32px] shadow-sm border border-gray-100/80 p-8 space-y-6">
                    <div class="flex items-center gap-3 pb-5 border-b border-gray-100">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100/40">
                            <!-- Seats icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6c0-1.243 1.007-2.25 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-gray-800 tracking-tight">Seat Management Setup</h2>
                            <p class="text-xs text-gray-400 font-semibold">Konfigurasikan layout kursi secara visual.</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-blue-50 border border-blue-100 text-blue-700 rounded-2xl p-6 text-sm font-semibold leading-relaxed">
                            ✨ <strong>Visual Seating Layout:</strong> Tata letak kursi untuk event ini akan dikonfigurasi secara visual menggunakan <strong>Seat Management Builder</strong> pada langkah berikutnya. 
                            Anda dapat langsung melanjutkan tanpa mengisi form ini.
                        </div>

                        <!-- Legacy Fallback Toggle -->
                        <div class="flex items-center gap-3 select-none">
                            <input type="checkbox" name="use_legacy_layout" id="use_legacy_layout" onchange="toggleLegacyLayout()" class="w-5 h-5 rounded border-gray-300 text-blue-650 focus:ring-blue-500">
                            <label for="use_legacy_layout" class="text-sm font-bold text-gray-500 cursor-pointer">Gunakan Input Teks Manual (Legacy Fallback)</label>
                        </div>

                        <div id="legacy-layout-section" class="hidden space-y-6 pt-4 border-t border-gray-100">
                            <div class="bg-amber-50 border border-amber-200 text-amber-700 rounded-2xl p-5 text-xs font-semibold leading-relaxed">
                                ⚠️ <strong>Perhatian:</strong> Menggunakan input teks manual akan mem-bypass Seat Management Builder visual. Gunakan format range (contoh: <code class="bg-white/60 px-1.5 py-0.5 rounded font-bold">A1-A10, B1-B10</code>).
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @forelse($tickets as $ticket)
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                            Layout Kursi untuk: <span class="text-blue-600 font-extrabold">{{ $ticket->name }}</span>
                                        </label>
                                        <input type="text" name="seat_layout[{{ $ticket->id }}]" id="seat_layout_{{ $ticket->id }}" 
                                            value="{{ old('seat_layout.' . $ticket->id, $seatLayouts[$ticket->id] ?? '') }}" 
                                            placeholder="Contoh: A1-A20, B1-B20" 
                                            class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700">
                                    </div>
                                @empty
                                    <div class="col-span-2 text-center py-6 text-gray-400 font-bold text-sm bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                        Belum ada kategori tiket terdaftar. Harap <a href="{{ route('admin.tickets.index', $event->id) }}" class="text-blue-600 underline">tambahkan tiket</a> terlebih dahulu.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 3. Lucky Draw Setup Card -->
            @if($event->has_lucky_draw)
                <div class="bg-white rounded-[32px] shadow-sm border border-gray-100/80 p-8 space-y-6">
                    <div class="flex items-center gap-3 pb-5 border-b border-gray-100">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100/40">
                            <!-- Lucky draw gift icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0-2.625V7.5m0 12.75V7.5m-9 0h18" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-gray-800 tracking-tight">Lucky Draw Configuration</h2>
                            <p class="text-xs text-gray-400 font-semibold">Tentukan hadiah utama dan jumlah pemenang undian.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                Nama Hadiah / Prize Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="prize_name" required 
                                value="{{ old('prize_name', $event->prize_name) }}" 
                                placeholder="Contoh: iPad Pro M4, Exclusive T-Shirt" 
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                Jumlah Pemenang / Winner Count <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="winner_count" required min="1" 
                                value="{{ old('winner_count', $event->winner_count ?? 1) }}" 
                                placeholder="Contoh: 3" 
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700">
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                Deskripsi Hadiah / Prize Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="prize_description" required rows="4" placeholder="Detail spesifikasi hadiah..." 
                                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold text-gray-700">{{ old('prize_description', $event->prize_description) }}</textarea>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Submit action footer -->
            <div class="flex justify-end gap-4 mt-8">
                <a href="{{ route('admin.tickets.index', $event->id) }}" 
                    class="bg-gray-100 hover:bg-gray-200 text-gray-650 px-8 py-4 rounded-2xl font-bold text-sm shadow-sm transition">
                    ← Back to Tickets
                </a>

                <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-bold text-sm shadow-sm hover:shadow transition">
                    💾 Save & Complete Setup
                </button>
            </div>
        </form>
    </div>

<script>
    function toggleLegacyLayout() {
        const legacySec = document.getElementById('legacy-layout-section');
        const legacyCheckbox = document.getElementById('use_legacy_layout');
        const legacyInputs = document.querySelectorAll('[id^="seat_layout_"]');

        if (legacyCheckbox.checked) {
            legacySec.classList.remove('hidden');
            legacyInputs.forEach(input => {
                input.required = true;
            });
        } else {
            legacySec.classList.add('hidden');
            legacyInputs.forEach(input => {
                input.required = false;
                input.value = '';
            });
        }
    }
</script>
@endsection
