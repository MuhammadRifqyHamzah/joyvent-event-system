<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JoyVent Admin Login</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 to-white min-h-screen flex items-center justify-center relative py-10 px-4">

    <!-- Background Blur -->
    <div class="absolute top-0 left-0 w-72 h-72 bg-blue-100 rounded-full opacity-50 blur-3xl"></div>

    <!-- Bottom Wave -->
    <div class="absolute bottom-0 left-0 w-full h-40 bg-gradient-to-r from-blue-500 to-blue-300 opacity-20 rounded-t-[100px]"></div>

    <!-- Login Card -->
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl p-6 md:p-8 relative z-10">

        <!-- Logo -->
        <div class="text-center mb-5">
            <h1 class="text-4xl font-bold">
                <span class="text-black">Joy</span>
                <span class="text-blue-600">Vent</span>
            </h1>

            <p class="text-gray-400 mt-1 tracking-[3px] text-xs">
                EVENT MANAGEMENT SYSTEM
            </p>
        </div>

        <!-- Icon -->
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-2xl">
                🔒
            </div>
        </div>

        <!-- Heading -->
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900 leading-tight">
                Welcome Back,<br>Admin!
            </h2>

            <p class="text-gray-500 mt-2 text-sm">
                Login untuk mengelola event di JoyVent
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-5">
                 {{ $errors->first() }}
            </div>
        @endif

        <!-- Form -->
        <form action="/admin/login" method="POST">

            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    placeholder="admin@joyvent.com"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- Remember -->
            <div class="flex justify-between items-center mb-5 text-sm">
                <label class="flex items-center gap-2 text-gray-600">
                    <input type="checkbox">
                    Remember me
                </label>

                <a href="#" class="text-blue-600 font-semibold hover:underline">
                    Forgot password?
                </a>
            </div>

            <!-- Login Button -->
            <button
                type="submit"
                class="w-full bg-gradient-to-r from-blue-500 to-blue-700 hover:opacity-90 transition text-white py-3 rounded-xl text-lg font-bold shadow-lg"
            >
                Login
            </button>

            <!-- Divider -->
            <div class="relative my-5">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>

                <div class="relative flex justify-center text-sm">
                    <span class="bg-white px-3 text-gray-500">
                        atau
                    </span>
                </div>
            </div>

            <!-- Google Login -->
            <button
                type="button"
                class="w-full border border-gray-300 hover:bg-gray-50 transition py-3 rounded-xl flex items-center justify-center gap-3 text-gray-700 font-semibold"
            >
                <img
                    src="https://www.svgrepo.com/show/475656/google-color.svg"
                    class="w-5 h-5"
                >

                Login dengan Google
            </button>

        </form>

        <!-- Footer -->
        <div class="text-center mt-5 text-gray-400 text-xs">
            © 2026 JoyVent. All rights reserved.
        </div>

    </div>

</body>
</html>