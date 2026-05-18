<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>JoyVent Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100">

    <div class="flex">

        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main -->
        <div class="flex-1 min-h-screen">

            <!-- Navbar -->
            @include('admin.partials.navbar', [
                'title' => trim($__env->yieldContent('title'))
            ])

            <!-- Content -->
            <main class="p-8">

                @yield('content')

            </main>

        </div>

    </div>

</body>
</html>