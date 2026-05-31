@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">

    <!-- Success & Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4.5 rounded-2xl flex items-center gap-3 shadow-sm transition duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-green-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-100 text-red-600 px-6 py-4.5 rounded-2xl flex flex-col gap-1 shadow-sm transition duration-300">
            <div class="flex items-center gap-3 font-bold text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-red-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <span>Terjadi beberapa kesalahan:</span>
            </div>
            <ul class="list-disc list-inside pl-8 text-xs font-semibold mt-1.5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Section -->
    <div class="pb-2">
        <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight flex items-center gap-3">
            <span>⚙️ Settings</span>
        </h1>
        <p class="text-gray-400 text-sm mt-2 font-semibold">
            Kelola informasi akun admin dan pengaturan dasar JoyVent.
        </p>
    </div>

    <!-- Main Settings Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Column 1: Profile & Security -->
        <div class="space-y-8">

            <!-- SECTION 1 : PROFILE CARD -->
            <div class="bg-white rounded-[32px] border border-gray-100/80 shadow-sm p-8 transition duration-300">
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight mb-6 flex items-center gap-2">
                    <span>👤</span> Profile Settings
                </h2>

                <!-- View Mode -->
                <div id="profile-view-mode" class="space-y-6">
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <!-- Profile Photo -->
                        <div class="relative w-28 h-28 rounded-full border-4 border-slate-50 overflow-hidden shadow-inner flex-shrink-0 bg-slate-100 flex items-center justify-center">
                            @if($user->profile_photo)
                                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <!-- High-quality default SVG avatar -->
                                <svg class="w-16 h-16 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0 1 12.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 1 1-8 0 4 4 0 0 1 8 0z" />
                                </svg>
                            @endif
                        </div>

                        <!-- Name & Email Info -->
                        <div class="text-center sm:text-left space-y-2.5">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">NAMA ADMIN</label>
                                <span class="text-lg font-extrabold text-gray-800 block">{{ $user->name }}</span>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">EMAIL ADMIN</label>
                                <span class="text-sm font-semibold text-gray-500 block">{{ $user->email }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button onclick="toggleProfileEdit(true)" class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-xs shadow-md hover:shadow-lg transition flex items-center justify-center gap-2 cursor-pointer select-none">
                            <!-- Edit Pencil Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.83 20.013a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                            <span>Edit Profile</span>
                        </button>
                    </div>
                </div>

                <!-- Edit Mode (Hidden by default) -->
                <form id="profile-edit-mode" action="{{ route('admin.settings.profile') }}" method="POST" enctype="multipart/form-data" class="hidden space-y-6">
                    @csrf
                    
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <!-- Photo Upload with Preview -->
                        <div class="relative w-28 h-28 rounded-full border-4 border-slate-50 overflow-hidden shadow-inner flex-shrink-0 bg-slate-100 flex items-center justify-center">
                            <img id="avatar-preview" 
                                 src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : '' }}" 
                                 alt="Avatar Preview" 
                                 class="w-full h-full object-cover {{ !$user->profile_photo ? 'hidden' : '' }}">
                            
                            <div id="avatar-placeholder" class="text-slate-300 flex flex-col items-center justify-center {{ $user->profile_photo ? 'hidden' : '' }}">
                                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0 1 12.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 1 1-8 0 4 4 0 0 1 8 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Upload file control -->
                        <div class="flex-1 w-full text-center sm:text-left space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">GANTI FOTO PROFIL</label>
                            <input type="file" name="profile_photo" id="profile_photo" accept="image/*" onchange="previewImage(event)" 
                                   class="text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 cursor-pointer w-full">
                            <p class="text-[10px] text-gray-400 font-medium">JPEG, PNG, JPG (Maks. 2MB)</p>
                        </div>
                    </div>

                    <!-- Input Fields -->
                    <div class="space-y-4 pt-2">
                        <div class="space-y-1.5">
                            <label for="name" class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Nama</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-semibold text-sm text-gray-700 bg-white">
                        </div>

                        <div class="space-y-1.5">
                            <label for="email" class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-semibold text-sm text-gray-700 bg-white">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button type="submit" class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-md transition cursor-pointer select-none">
                            Save Changes
                        </button>
                        <button type="button" onclick="toggleProfileEdit(false)" class="px-5 py-3 bg-gray-150 hover:bg-gray-200 text-gray-600 rounded-xl font-bold text-xs transition cursor-pointer select-none">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- SECTION 2 : SECURITY CARD -->
            <div class="bg-white rounded-[32px] border border-gray-100/80 shadow-sm p-8 transition duration-300">
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight mb-6 flex items-center gap-2">
                    <span>🔒</span> Security Settings
                </h2>

                <form action="{{ route('admin.settings.password') }}" method="POST" class="space-y-5" onsubmit="return validatePasswordForm()">
                    @csrf
                    
                    <div class="space-y-1.5">
                        <label for="current_password" class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-semibold text-sm text-gray-700 bg-white">
                    </div>

                    <div class="space-y-1.5">
                        <label for="new_password" class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Password Baru</label>
                        <input type="password" name="new_password" id="new_password" required minlength="6"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-semibold text-sm text-gray-700 bg-white">
                        <p class="text-[10px] text-gray-400 font-medium mt-1">Minimal 6 karakter</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="new_password_confirmation" class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-semibold text-sm text-gray-700 bg-white">
                    </div>

                    <!-- Client-side Password Error Banner -->
                    <div id="password-validation-error" class="hidden bg-red-50 border border-red-100 text-red-600 px-4 py-3.5 rounded-xl flex items-center gap-2">
                        <span class="text-xs font-bold" id="password-error-message"></span>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3.5 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl font-bold text-xs shadow-md hover:shadow-lg transition cursor-pointer select-none">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Column 2: Organizer Info & About System -->
        <div class="space-y-8">

            <!-- SECTION 3 : ORGANIZER INFO CARD -->
            <div class="bg-white rounded-[32px] border border-gray-100/80 shadow-sm p-8 transition duration-300">
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight mb-6 flex items-center gap-2">
                    <span>🏢</span> Organizer Info
                </h2>

                <form action="{{ route('admin.settings.organizer') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div class="space-y-1.5">
                        <label for="organizer_name" class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Nama Organizer</label>
                        <input type="text" name="organizer_name" id="organizer_name" value="{{ old('organizer_name', $organizerName) }}" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-semibold text-sm text-gray-700 bg-white">
                    </div>

                    <div class="space-y-1.5">
                        <label for="organizer_email" class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Email Organizer</label>
                        <input type="email" name="organizer_email" id="organizer_email" value="{{ old('organizer_email', $organizerEmail) }}" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-semibold text-sm text-gray-700 bg-white">
                    </div>

                    <div class="space-y-1.5">
                        <label for="organizer_phone" class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Nomor Telepon Organizer</label>
                        <input type="text" name="organizer_phone" id="organizer_phone" value="{{ old('organizer_phone', $organizerPhone) }}" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-semibold text-sm text-gray-700 bg-white">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-xs shadow-md hover:shadow-lg transition cursor-pointer select-none">
                            Save Organizer Info
                        </button>
                    </div>
                </form>
            </div>

            <!-- SECTION 4 : ABOUT SYSTEM CARD -->
            <div class="bg-white rounded-[32px] border border-gray-100/80 shadow-sm p-8 transition duration-300">
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight mb-6 flex items-center gap-2">
                    <span>💡</span> About System
                </h2>

                <div class="space-y-6">
                    <!-- Tech Stack specs grid -->
                    <div class="bg-slate-50 border border-gray-100 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Application</span>
                            <span class="text-sm font-extrabold text-gray-800">JoyVent Admin Panel</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Version</span>
                            <span class="text-xs font-extrabold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">1.0</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-200/40 pt-4">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Laravel Version</span>
                            <span class="text-sm font-semibold text-gray-600">{{ $laravelVersion }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">PHP Version</span>
                            <span class="text-sm font-semibold text-gray-600">{{ $phpVersion }}</span>
                        </div>
                    </div>

                    <!-- Statistics grid inside card -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Stat 1: Total Event -->
                        <div class="bg-white border border-gray-150 rounded-2xl p-4 flex flex-col justify-between hover:shadow-sm transition">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">JUMLAH EVENT</span>
                            <span class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($totalEvents) }}</span>
                        </div>

                        <!-- Stat 2: Total Peserta -->
                        <div class="bg-white border border-gray-150 rounded-2xl p-4 flex flex-col justify-between hover:shadow-sm transition">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">JUMLAH PESERTA</span>
                            <span class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($totalParticipants) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- JavaScript for Interactivity -->
<script>
    /**
     * Toggles between view and edit modes for the Profile section.
     */
    function toggleProfileEdit(showEdit) {
        const viewMode = document.getElementById('profile-view-mode');
        const editMode = document.getElementById('profile-edit-mode');

        if (showEdit) {
            viewMode.classList.add('hidden');
            editMode.classList.remove('hidden');
        } else {
            viewMode.classList.remove('hidden');
            editMode.classList.add('hidden');
        }
    }

    /**
     * Shows a real-time preview of the selected profile photo.
     */
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('avatar-preview');
            const placeholder = document.getElementById('avatar-placeholder');
            
            preview.src = reader.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    /**
     * Client-side validation for the security password form.
     */
    function validatePasswordForm() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('new_password_confirmation').value;
        const errorContainer = document.getElementById('password-validation-error');
        const errorMessage = document.getElementById('password-error-message');

        errorContainer.classList.add('hidden');

        // Check if length is at least 6
        if (newPassword.length < 6) {
            errorMessage.textContent = 'Password baru harus minimal 6 karakter.';
            errorContainer.classList.remove('hidden');
            return false;
        }

        // Check if new password and confirmation match
        if (newPassword !== confirmPassword) {
            errorMessage.textContent = 'Konfirmasi password baru tidak cocok.';
            errorContainer.classList.remove('hidden');
            return false;
        }

        return true;
    }
</script>
@endsection
