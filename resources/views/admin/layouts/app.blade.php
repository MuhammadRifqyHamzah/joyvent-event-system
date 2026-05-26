<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>JoyVent Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
 
    <!-- Sidebar Collapsible CSS Layout -->
    <style>
        #sidebar {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s ease, border-color 0.3s ease;
            width: 18rem; /* w-72 (288px) */
        }
        #sidebar.collapsed {
            width: 0 !important;
            border-right-width: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            overflow: hidden !important;
        }
    </style>
 
</head>
 
<body class="bg-slate-50">
 
    <div class="flex">
 
        <!-- Sidebar -->
        @include('admin.partials.sidebar')
 
        <!-- Main -->
        <div class="flex-1 min-h-screen transition-all duration-300">
 
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
 
    <!-- Collapsible Sidebar & Notification Dropdown Interactivity Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Sidebar Toggle collapse/expand
            const toggleSidebar = document.getElementById('toggleSidebar');
            const sidebar = document.getElementById('sidebar');
 
            if (toggleSidebar && sidebar) {
                toggleSidebar.addEventListener('click', (e) => {
                    e.stopPropagation();
                    sidebar.classList.toggle('collapsed');
                });
            }
 
            // 2. Notification dropdown overlay toggle
            const notificationBell = document.getElementById('notificationBell');
            const notificationDropdown = document.getElementById('notificationDropdown');
 
            if (notificationBell && notificationDropdown) {
                notificationBell.addEventListener('click', (e) => {
                    e.stopPropagation();
                    notificationDropdown.classList.toggle('hidden');
                });
 
                // Close the notification overlay if user clicks outside of it
                document.addEventListener('click', (e) => {
                    if (!notificationDropdown.contains(e.target) && e.target !== notificationBell && !notificationBell.contains(e.target)) {
                        notificationDropdown.classList.add('hidden');
                    }
                });
            }
        });
    </script>
 
</body>
</html>