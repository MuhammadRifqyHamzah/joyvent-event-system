@extends('admin.layouts.app')
 
@section('title', 'Lucky Draw')
 
@section('content')
 
<div class="space-y-8">
    @include('admin.events.partials.header')
    @php
        $eventStatus = $event->calculated_status;
    @endphp
 
    {{-- Success Alert Notification --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4.5 rounded-2xl flex items-center gap-3 shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-green-600 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif
 
    <!-- Header: Title -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">
                Lucky Draw 🎰
            </h1>
            <p class="text-gray-400 text-sm mt-2 font-semibold">
                Undi hadiah menarik bagi para peserta yang telah hadir di lokasi event.
            </p>
        </div>
    </div>
 

        <!-- Seated Event Layout Editor -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative">
            
            <!-- Confetti Canvas -->
            <canvas id="confettiCanvas" class="absolute inset-0 w-full h-full pointer-events-none z-50 rounded-[32px]"></canvas>
 
            <!-- Left Side: Interactive neon Slot Machine Card (Takes 2 Columns) -->
            @if($eventStatus !== 'finished')
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-slate-950 rounded-[32px] border border-slate-800 shadow-2xl p-10 text-center relative overflow-hidden min-h-[420px] flex flex-col justify-between">
                    
                    <!-- Cyberpunk background decoration glow -->
                    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px] pointer-events-none"></div>
                    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-pink-500/10 rounded-full blur-[100px] pointer-events-none"></div>
 
                    <!-- Top Ribbon -->
                    <div class="z-10">
                        <span class="inline-block bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-[10px] px-3.5 py-1 rounded-full font-bold uppercase tracking-widest">
                            JoyVent Interactive Raffle
                        </span>
                    </div>
 
                    <!-- Neon Slot Display -->
                    <div class="my-8 z-10">
                        <div class="bg-slate-900 border-2 border-indigo-500/30 rounded-[32px] p-10 shadow-2xl shadow-indigo-500/5 relative">
                            <div class="absolute inset-0 bg-gradient-to-b from-indigo-500/5 to-transparent rounded-[32px] pointer-events-none"></div>
                            
                            <!-- Large Text Box -->
                            <div id="slotDisplay" class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 tracking-wide uppercase select-none min-h-[60px] flex items-center justify-center">
                                SIAP DIUNDI 🎰
                            </div>
                            
                            <!-- Subtext winner info -->
                            <div id="slotSubDisplay" class="text-slate-400 text-xs mt-3 font-semibold min-h-[16px]">
                                Masukkan nama hadiah, lalu klik tombol putar di bawah
                            </div>
                        </div>
                    </div>
 
                    <!-- Controls Form -->
                    <div class="z-10 max-w-md mx-auto w-full space-y-4">
                        <div class="flex gap-4">
                            <input 
                                type="text" 
                                id="prizeInput" 
                                value="Merchandise Eksklusif" 
                                placeholder="Masukkan Nama Hadiah..." 
                                class="w-full bg-slate-900 border border-slate-800 rounded-xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-bold text-white placeholder-slate-600"
                            >
                        </div>
 
                        @if($eventStatus === 'upcoming')
                            <button disabled class="w-full bg-slate-805 text-slate-500 font-extrabold py-4 px-6 rounded-2xl text-xs tracking-widest uppercase cursor-not-allowed select-none shadow-md">
                                Undian Belum Bisa Dimulai (Event Belum Mulai) 🔒
                            </button>
                        @elseif($candidates->isEmpty())
                            <button disabled class="w-full bg-slate-800 text-slate-500 font-extrabold py-4 px-6 rounded-2xl text-xs tracking-widest uppercase cursor-not-allowed select-none shadow-md">
                                Tidak Ada Kandidat (Harus Check-In) 💡
                            </button>
                        @else
                            <button id="drawBtn" onclick="startLuckyDraw()" class="w-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 hover:scale-[1.02] hover:shadow-indigo-500/20 hover:shadow-lg text-white font-extrabold py-4 px-6 rounded-2xl text-xs tracking-widest uppercase transition duration-300 cursor-pointer shadow-md">
                                PUTAR UNDIAN 🎰
                            </button>
                        @endif
                    </div>
 
                </div>
            </div>
            @endif
 
            <!-- Right Side: Winner History Logs -->
            <div class="{{ $eventStatus === 'finished' ? 'lg:col-span-3' : 'lg:col-span-1' }} space-y-6">
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 flex flex-col justify-between {{ $eventStatus === 'finished' ? 'min-h-[250px]' : 'min-h-[420px]' }}">
                    
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-800 tracking-tight flex items-center gap-2">
                            <span>🏆 Winner History</span>
                        </h3>
                        <p class="text-gray-400 text-xs mt-1.5 font-semibold leading-relaxed">
                            Daftar pemenang undian yang sah untuk event ini.
                        </p>
                    </div>
 
                    <!-- History List -->
                    <div class="my-6 flex-grow overflow-y-auto pr-2 {{ $eventStatus === 'finished' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4' : 'space-y-3 max-h-[220px]' }}" id="winnersListContainer">
                        @if($winners->isEmpty())
                            <div class="h-full flex flex-col items-center justify-center text-center p-6 bg-gray-50 border border-gray-100/50 rounded-2xl">
                                <span class="text-2xl mb-1">🎁</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Belum Ada Pemenang</span>
                            </div>
                        @else
                            @foreach($winners as $win)
                                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 flex items-center justify-between gap-3 relative group">
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-xs text-gray-800 truncate">
                                            {{ $win->registration->user->name }}
                                        </div>
                                        <div class="text-[9px] font-semibold text-indigo-500 mt-0.5 truncate">
                                            🎁 {{ $win->prize_name }}
                                        </div>
                                    </div>
                                    
                                    <!-- Delete Reset Button -->
                                    @if($eventStatus !== 'finished')
                                    <form action="{{ route('admin.lucky_draw.destroy', $win->id) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition flex-shrink-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 hover:bg-red-50 hover:text-red-650 text-gray-400 rounded-lg cursor-pointer transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                    
                    <!-- Candidates Summary Badge -->
                    @if($eventStatus !== 'finished')
                    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 text-center">
                        <span class="text-[9px] font-extrabold text-indigo-500 uppercase tracking-widest">Kandidat Peserta Hadir</span>
                        <span class="block text-2xl font-black text-indigo-750 mt-1" id="candidatesCountBadge">
                            {{ $candidates->count() }} Orang
                        </span>
                    </div>
                    @endif
 
                </div>
            </div>
 
        </div>
 
</div>
 
{{-- Interactive Winner Success Celebration Modal Overlay --}}
<div id="winnerModal" class="fixed inset-0 z-50 hidden flex items-center justify-center px-4 bg-slate-900/60 backdrop-blur-sm transition">
    <div class="bg-white rounded-[32px] p-10 max-w-md w-full border border-gray-100 text-center shadow-2xl relative overflow-hidden transform scale-95 transition-transform duration-300">
        
        <!-- Ornaments -->
        <div class="w-20 h-20 bg-amber-50 border border-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="text-4xl">👑</span>
        </div>
        
        <h3 class="text-2xl font-black text-gray-800 tracking-tight">Selamat Pemenang!</h3>
        <p class="text-gray-400 text-xs font-semibold mt-1 uppercase tracking-widest" id="modalPrizeName">MERCHANDISE EKSKLUSIF</p>
        
        <div class="my-6 bg-slate-50 border border-slate-100 rounded-2xl p-6">
            <div class="text-2xl font-black text-indigo-600 truncate" id="modalWinnerName">Rifqy Hamzah</div>
            <div class="text-[10px] text-slate-400 mt-1 font-semibold truncate" id="modalWinnerEmail">rifqy@joyvent.com</div>
        </div>
 
        <button onclick="closeWinnerModal()" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-extrabold py-4 px-6 rounded-2xl text-xs tracking-wider uppercase transition cursor-pointer shadow-md">
            Tutup & Klaim Hadiah 🎁
        </button>
    </div>
</div>
 
<script>
    // JS Candidates List for intervals shuffler
    const candidates = @json($candidates->map(fn($c) => [
        'registration_id' => $c->id,
        'name' => $c->user->name,
        'email' => $c->user->email
    ]));
 
    let isDrawing = false;
    let shuffleInterval = null;
    let confettiInterval = null;
 
    function startLuckyDraw() {
        if (isDrawing || candidates.length === 0) return;
 
        const prizeName = document.getElementById('prizeInput').value.trim();
        if (!prizeName) {
            alert('Silakan masukkan nama hadiah terlebih dahulu!');
            return;
        }
 
        isDrawing = true;
        const drawBtn = document.getElementById('drawBtn');
        drawBtn.disabled = true;
        drawBtn.innerText = 'MENGUNDI...';
 
        const display = document.getElementById('slotDisplay');
        const subDisplay = document.getElementById('slotSubDisplay');
        
        let counter = 0;
        let speed = 50; // Milliseconds per shuffle
        
        subDisplay.innerText = 'Mengacak nama kandidat...';
 
        // Run slot machine interval shuffler
        function shuffle() {
            const randomIndex = Math.floor(Math.random() * candidates.length);
            const candidate = candidates[randomIndex];
            
            display.innerText = candidate.name;
            
            counter++;
            if (counter < 25) {
                // Keep fast speed
                shuffleInterval = setTimeout(shuffle, speed);
            } else if (counter < 35) {
                // Slower speed
                shuffleInterval = setTimeout(shuffle, speed + 100);
            } else if (counter < 40) {
                // Very slow speed
                shuffleInterval = setTimeout(shuffle, speed + 300);
            } else {
                // Final selection lock
                const finalWinner = candidates[randomIndex];
                saveWinner(finalWinner, prizeName);
            }
        }
        
        shuffle();
    }
 
    function saveWinner(winner, prizeName) {
        const eventId = "{{ $eventId }}";
        
        // POST request to record victory in database
        fetch("{{ route('admin.lucky_draw.draw') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                event_id: eventId,
                registration_id: winner.registration_id,
                prize_name: prizeName
            })
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                // Visual modal updates
                document.getElementById('modalWinnerName').innerText = winner.name;
                document.getElementById('modalWinnerEmail').innerText = winner.email;
                document.getElementById('modalPrizeName').innerText = prizeName.toUpperCase();
                
                // Show modal overlay
                const modal = document.getElementById('winnerModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                
                // Trigger celebratory canvas confetti effects
                startConfettiEffect();
            } else {
                alert('Gagal mencatat pemenang undian!');
                resetDrawBtn();
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan!');
            resetDrawBtn();
        });
    }
 
    function resetDrawBtn() {
        isDrawing = false;
        const drawBtn = document.getElementById('drawBtn');
        if (drawBtn) {
            drawBtn.disabled = false;
            drawBtn.innerText = 'PUTAR UNDIAN 🎰';
        }
    }
 
    function closeWinnerModal() {
        // Hide modal
        const modal = document.getElementById('winnerModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        
        // Stop confetti
        stopConfettiEffect();
        
        // Reload page to naturally sync states and candidate logs cleanly
        window.location.reload();
    }
 
    // Pure Canvas-based Celebratory Confetti Engine
    const canvas = document.getElementById('confettiCanvas');
    let ctx = null;
    let particles = [];
    let animationFrame = null;
 
    function startConfettiEffect() {
        if (!canvas) return;
        ctx = canvas.getContext('2d');
        canvas.width = canvas.parentElement.clientWidth;
        canvas.height = canvas.parentElement.clientHeight;
        
        const colors = ['#6366f1', '#a855f7', '#ec4899', '#3b82f6', '#10b981', '#f59e0b'];
        
        for (let i = 0; i < 100; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height - canvas.height,
                r: Math.random() * 6 + 4,
                d: Math.random() * canvas.height,
                color: colors[Math.floor(Math.random() * colors.length)],
                tilt: Math.random() * 10 - 5,
                tiltAngleIncremental: Math.random() * 0.07 + 0.02,
                tiltAngle: 0
            });
        }
 
        function drawConfetti() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            particles.forEach((p, index) => {
                p.tiltAngle += p.tiltAngleIncremental;
                p.y += (Math.cos(p.d) + 3 + p.r / 2) / 2;
                p.tilt = Math.sin(p.tiltAngle) * 15;
 
                if (p.y > canvas.height) {
                    particles[index] = {
                        x: Math.random() * canvas.width,
                        y: -20,
                        r: p.r,
                        d: p.d,
                        color: p.color,
                        tilt: p.tilt,
                        tiltAngleIncremental: p.tiltAngleIncremental,
                        tiltAngle: p.tiltAngle
                    };
                }
 
                ctx.beginPath();
                ctx.lineWidth = p.r;
                ctx.strokeStyle = p.color;
                ctx.moveTo(p.x + p.tilt + p.r / 2, p.y);
                ctx.lineTo(p.x + p.tilt, p.y + p.tilt + p.r / 2);
                ctx.stroke();
            });
 
            animationFrame = requestAnimationFrame(drawConfetti);
        }
        
        drawConfetti();
    }
 
    function stopConfettiEffect() {
        if (animationFrame) {
            cancelAnimationFrame(animationFrame);
        }
        if (ctx && canvas) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
        particles = [];
    }
</script>
 
@endsection
