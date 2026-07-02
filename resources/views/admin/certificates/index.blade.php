@extends('admin.layouts.app')
 
@section('title', 'Certificates')
 
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
                Certificates Generator 🎓
            </h1>
            <p class="text-gray-400 text-sm mt-2 font-semibold">
                Unggah template latar belakang dan terbitkan sertifikat massal untuk peserta terdaftar yang hadir.
            </p>
        </div>
    </div>
 

        <!-- Grid layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
 
            <!-- Left Panel: Uploader / Locked Status -->
            <div class="lg:col-span-1 space-y-6">
                @if($eventStatus === 'upcoming')
                    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 text-center space-y-6">
                        <div class="w-16 h-16 bg-slate-50 border border-slate-200/50 text-slate-400 rounded-3xl flex items-center justify-center mx-auto shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold text-gray-800">Certificates Locked</h3>
                            <p class="text-xs text-gray-400 font-semibold mt-2 leading-relaxed">
                                Penerbitan sertifikat dinonaktifkan karena event belum dimulai.
                            </p>
                        </div>
                        
                        @if($templateUrl)
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100/50 text-left">
                                <span class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-wider mb-2.5">Template Aktif</span>
                                <div class="overflow-hidden rounded-xl border border-gray-200">
                                    <canvas id="templatePreviewCanvas" class="w-full h-auto bg-slate-100"></canvas>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 space-y-6">
                        <div>
                            <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">
                                Setup Template 🖼️
                            </h3>
                            <p class="text-gray-400 text-xs mt-1.5 font-semibold leading-relaxed">
                                Unggah template gambar beresolusi tinggi (misal landscape 1920x1080).
                            </p>
                        </div>
     
                        <form action="{{ route('admin.certificates.generate') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $eventId }}">
                            
                            <!-- Drag Drop Upload Area -->
                            <div class="border-2 border-dashed border-gray-200 hover:border-blue-500 rounded-2xl p-6 text-center cursor-pointer transition relative group">
                                <input type="file" name="template" id="templateFileInput" onchange="previewTemplateFile(this)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <div class="space-y-2 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 text-gray-400 mx-auto group-hover:text-blue-500 transition">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span class="block text-xs font-bold text-gray-500">Pilih Template Gambar</span>
                                    <span class="block text-[10px] text-gray-400">JPG, PNG berukuran maks. 4MB</span>
                                </div>
                            </div>
     
                            <!-- Template Preview Canvas Wrapper -->
                            @if($templateUrl)
                                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100/50">
                                    <span class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-wider mb-2.5">Live Preview Template</span>
                                    <div class="overflow-hidden rounded-xl border border-gray-200">
                                        <canvas id="templatePreviewCanvas" class="w-full h-auto bg-slate-100"></canvas>
                                    </div>
                                </div>
                            @endif
     
                            <!-- Action Button -->
                            @if($candidates->isEmpty())
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-4 px-6 rounded-2xl text-xs tracking-wider uppercase shadow-md transition cursor-pointer">
                                    🔄 Upload Template Saja
                                </button>
                            @else
                                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:scale-[1.02] hover:shadow-blue-500/20 hover:shadow-lg text-white font-extrabold py-4 px-6 rounded-2xl text-xs tracking-wider uppercase transition duration-300 cursor-pointer shadow-md">
                                    🚀 Terbitkan Massal ({{ $candidates->count() }} Orang)
                                </button>
                            @endif
                        </form>
     
                    </div>
                @endif
            </div>
 
            <!-- Right Panel: Issued Certificates Table -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">
                                Sertifikat Terbit 🏆
                            </h3>
                            <p class="text-gray-400 text-xs mt-1.5 font-semibold leading-relaxed">
                                Daftar sertifikat penghargaan digital yang berhasil diterbitkan.
                            </p>
                        </div>
                        <span class="bg-blue-50 border border-blue-100 text-blue-600 text-xs font-extrabold px-3 py-1.5 rounded-full">
                            {{ $certificates->count() }} Terbit
                        </span>
                    </div>
 
                    <!-- Table list -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama & Email</th>
                                    <th class="py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Kode Sertifikat</th>
                                    <th class="py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Status Valid</th>
                                    <th class="py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @if($certificates->isEmpty())
                                    <tr>
                                        <td colspan="4" class="py-12 text-center text-gray-400">
                                            <span class="block text-2xl mb-1">💡</span>
                                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Belum Ada Sertifikat Diterbitkan</span>
                                        </td>
                                    </tr>
                                @else
                                    @foreach($certificates as $cert)
                                        <tr>
                                            <td class="py-4">
                                                <span class="block text-sm font-extrabold text-gray-800 truncate max-w-[180px]">{{ $cert->registration->user->name }}</span>
                                                <span class="block text-[10px] text-gray-400 font-semibold truncate max-w-[180px] mt-0.5">{{ $cert->registration->user->email }}</span>
                                            </td>
                                            <td class="py-4 font-mono text-xs font-bold text-indigo-500">
                                                {{ $cert->certificate_code }}
                                            </td>
                                            <td class="py-4 text-center">
                                                <form action="{{ route('admin.certificates.toggle_valid', $cert->id) }}" method="POST">
                                                    @csrf
                                                    @if($cert->is_valid)
                                                        <button type="submit" class="bg-green-50 hover:bg-green-100 border border-green-150 text-green-600 text-[10px] font-extrabold px-3 py-1.5 rounded-full shadow-sm cursor-pointer transition select-none">
                                                            🟢 AKTIF (Valid)
                                                        </button>
                                                    @else
                                                        <button type="submit" class="bg-red-50 hover:bg-red-100 border border-red-150 text-red-600 text-[10px] font-extrabold px-3 py-1.5 rounded-full shadow-sm cursor-pointer transition select-none">
                                                            🔴 NONAKTIF (Banned)
                                                        </button>
                                                    @endif
                                                </form>
                                            </td>
                                            <td class="py-4 text-right">
                                                <button 
                                                    onclick="previewAndDownloadCertificate('{{ $cert->registration->user->name }}', '{{ $cert->certificate_code }}')" 
                                                    class="inline-flex items-center gap-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow-sm cursor-pointer select-none"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                                    </svg>
                                                    <span>Download</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
 
        </div>
 
</div>
 
{{-- Preview and Download Modal Canvas Overlay --}}
<div id="previewModal" class="fixed inset-0 z-50 hidden flex items-center justify-center px-4 bg-slate-900/60 backdrop-blur-sm transition">
    <div class="bg-white rounded-[32px] p-8 max-w-4xl w-full border border-gray-100 text-center shadow-2xl relative overflow-hidden transform scale-95 transition-transform duration-300">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Pratinjau Sertifikat Peserta</h3>
                <p class="text-gray-400 text-xs mt-1 font-semibold leading-relaxed" id="modalCertificateTitle">JV-0000</p>
            </div>
            <button onclick="closePreviewModal()" class="p-2 hover:bg-gray-100 text-gray-400 rounded-full cursor-pointer transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
 
        <!-- Large high-res rendering Canvas -->
        <div class="bg-slate-50 border border-gray-200/60 rounded-2xl p-4 overflow-hidden mb-6 flex justify-center items-center">
            <canvas id="largeRenderCanvas" class="max-w-full h-auto rounded-lg shadow-sm border border-gray-150 bg-slate-100"></canvas>
        </div>
 
        <div class="flex justify-end gap-3">
            <button onclick="closePreviewModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-650 font-bold px-6 py-3.5 rounded-2xl text-xs uppercase transition cursor-pointer select-none">
                Tutup
            </button>
            <button onclick="triggerActualDownload()" class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-8 py-3.5 rounded-2xl text-xs uppercase tracking-wider transition cursor-pointer shadow-md select-none inline-flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <span>Unduh Gambar PNG</span>
            </button>
        </div>
    </div>
</div>
 
<script>
    const templateUrl = "{{ $templateUrl }}";
    let activeWinnerName = "";
    let activeWinnerCode = "";
 
    document.addEventListener("DOMContentLoaded", () => {
        if (templateUrl) {
            renderTemplatePreview();
        }
    });
 
    // Render live thumbnail template preview on setup area
    function renderTemplatePreview() {
        const canvas = document.getElementById("templatePreviewCanvas");
        if (!canvas) return;
        
        const ctx = canvas.getContext("2d");
        const img = new Image();
        
        img.onload = () => {
            canvas.width = img.naturalWidth || 800;
            canvas.height = img.naturalHeight || 600;
            
            // Draw background template
            ctx.drawImage(img, 0, 0);
            
            // Draw sample overlay name
            ctx.fillStyle = "#1e293b"; // Slate-800
            ctx.font = "bold " + (canvas.height * 0.065) + "px Outfit, Times New Roman, serif";
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            ctx.fillText("NAMA PESERTA", canvas.width / 2, canvas.height * 0.48);
 
            // Draw sample certificate code overlay
            ctx.fillStyle = "#6366f1"; // Indigo-500
            ctx.font = "bold " + (canvas.height * 0.025) + "px monospace";
            ctx.fillText("CODE: JV-SAMPLE-123", canvas.width / 2, canvas.height * 0.88);
        };
        
        img.src = templateUrl;
    }
 
    // Open Preview modal and render high-res Canvas
    function previewAndDownloadCertificate(name, code) {
        if (!templateUrl) {
            alert("Silakan unggah gambar template terlebih dahulu di panel sebelah kiri!");
            return;
        }
 
        activeWinnerName = name;
        activeWinnerCode = code;
 
        document.getElementById("modalCertificateTitle").innerText = `Kode Verifikasi Keaslian: ${code}`;
        
        // Show modal
        const modal = document.getElementById("previewModal");
        modal.classList.remove("hidden");
        modal.classList.add("flex");
 
        // Render large certificate canvas
        const canvas = document.getElementById("largeRenderCanvas");
        const ctx = canvas.getContext("2d");
        const img = new Image();
        
        img.onload = () => {
            canvas.width = img.naturalWidth || 1920;
            canvas.height = img.naturalHeight || 1080;
            
            // Draw original high-res background template
            ctx.drawImage(img, 0, 0);
            
            // Draw elegante recipient name
            ctx.fillStyle = "#0f172a"; // Slate-900
            ctx.font = "bold " + (canvas.height * 0.075) + "px Outfit, Times New Roman, serif";
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            ctx.fillText(name.toUpperCase(), canvas.width / 2, canvas.height * 0.48);
 
            // Draw elegant unique certificate code
            ctx.fillStyle = "#4f46e5"; // Indigo-600
            ctx.font = "bold " + (canvas.height * 0.024) + "px monospace";
            ctx.fillText(`KODE SERTIFIKAT: ${code}`, canvas.width / 2, canvas.height * 0.88);
        };
        
        img.src = templateUrl;
    }
 
    function triggerActualDownload() {
        const canvas = document.getElementById("largeRenderCanvas");
        if (!canvas) return;
        
        // Convert canvas drawings to high-res PNG download link
        const imageURI = canvas.toDataURL("image/png");
        const link = document.createElement("a");
        link.download = `Sertifikat_${activeWinnerName.replace(/\s+/g, '_')}_${activeWinnerCode}.png`;
        link.href = imageURI;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
 
    function closePreviewModal() {
        const modal = document.getElementById("previewModal");
        modal.classList.remove("flex");
        modal.classList.add("hidden");
    }
 
    function previewTemplateFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                // Instantly update thumbnail if canvas exists
                const canvas = document.getElementById("templatePreviewCanvas");
                if (canvas) {
                    const ctx = canvas.getContext("2d");
                    const img = new Image();
                    img.onload = () => {
                        canvas.width = img.naturalWidth;
                        canvas.height = img.naturalHeight;
                        ctx.drawImage(img, 0, 0);
                        ctx.fillStyle = "#1e293b";
                        ctx.font = "bold " + (canvas.height * 0.065) + "px Outfit, serif";
                        ctx.textAlign = "center";
                        ctx.textBaseline = "middle";
                        ctx.fillText("PREVIEW TEMPLATE BARU", canvas.width / 2, canvas.height * 0.48);
                    };
                    img.src = e.target.result;
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
 
@endsection
