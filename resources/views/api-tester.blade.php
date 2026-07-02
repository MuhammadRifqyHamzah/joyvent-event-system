<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Tester - JoyVent Event System</title>
    <meta name="description" content="Alat penguji API interaktif untuk Sistem Event JoyVent. Membantu menguji endpoint otentikasi, event, tiket, lucky draw, dan notifikasi secara langsung.">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: rgba(17, 24, 39, 0.7);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --accent: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #0ea5e9;
            --panel-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        header {
            background: linear-gradient(135deg, rgba(17, 24, 39, 0.8), rgba(9, 13, 22, 0.9));
            border-bottom: 1px solid var(--card-border);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .logo-area h1 {
            font-size: 1.25rem;
            font-weight: 700;
            background: linear-gradient(to right, #818cf8, #34d399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-area span {
            font-size: 0.75rem;
            font-weight: 500;
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            border: 1px solid rgba(99, 102, 241, 0.3);
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }

        .main-container {
            flex: 1;
            display: grid;
            grid-template-columns: 350px 1fr;
            height: calc(100vh - 73px);
            overflow: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            background: rgba(11, 15, 25, 0.6);
            border-right: 1px solid var(--card-border);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            overflow-y: auto;
            backdrop-filter: blur(8px);
        }

        .sidebar-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: var(--panel-shadow);
        }

        .sidebar-card h2 {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 0.75rem;
        }

        .form-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 0.35rem;
        }

        select, input, textarea {
            width: 100%;
            background: rgba(31, 41, 55, 0.5);
            border: 1px solid var(--card-border);
            border-radius: 8px;
            color: var(--text-primary);
            padding: 0.6rem 0.8rem;
            font-size: 0.85rem;
            outline: none;
            transition: all 0.2s;
            font-family: inherit;
        }

        select:focus, input:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.25);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
            gap: 0.5rem;
        }

        .btn:hover {
            background: var(--primary-hover);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
            border: 1px solid var(--card-border);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .flex-buttons {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 0.5rem;
            margin-top: 0.75rem;
        }

        .badge-req {
            font-size: 0.7rem;
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-auth {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .badge-no-auth {
            background: rgba(16, 185, 129, 0.15);
            color: var(--accent);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        /* Recent Requests Section */
        .recent-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            max-height: 150px;
            overflow-y: auto;
        }

        .recent-item {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.03);
            border-radius: 6px;
            padding: 0.4rem 0.6rem;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s;
        }

        .recent-item:hover {
            background: rgba(255,255,255,0.05);
        }

        .method-badge {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 0.7rem;
            padding: 0.1rem 0.3rem;
            border-radius: 4px;
            min-width: 55px;
            text-align: center;
        }

        .method-GET { background: rgba(14, 165, 233, 0.15); color: var(--info); }
        .method-POST { background: rgba(16, 185, 129, 0.15); color: var(--accent); }
        .method-PUT { background: rgba(245, 158, 11, 0.15); color: var(--warning); }
        .method-DELETE { background: rgba(239, 68, 68, 0.15); color: var(--danger); }
        .method-PATCH { background: rgba(139, 92, 246, 0.15); color: #a78bfa; }

        .status-badge {
            font-weight: 600;
            padding: 0.1rem 0.3rem;
            border-radius: 4px;
        }

        .status-2xx { color: var(--accent); }
        .status-4xx, .status-5xx { color: var(--danger); }

        /* Workspace Panels */
        .workspace {
            display: grid;
            grid-template-rows: auto 1fr;
            padding: 1.5rem;
            gap: 1rem;
            overflow: hidden;
            background: rgba(9, 13, 22, 0.4);
        }

        .url-bar {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: var(--panel-shadow);
        }

        .method-select {
            width: 100px;
            background: rgba(31, 41, 55, 0.7);
            font-weight: 700;
            border-color: rgba(255,255,255,0.1);
        }

        .url-input {
            flex: 1;
            font-family: 'JetBrains Mono', monospace;
            background: rgba(31, 41, 55, 0.3);
            border-color: rgba(255,255,255,0.05);
        }

        .btn-send {
            width: 150px;
            background: var(--primary);
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
        }

        .btn-send:hover {
            box-shadow: 0 0 25px rgba(99, 102, 241, 0.6);
        }

        .request-response-container {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 1rem;
            overflow: hidden;
        }

        .panel-flex {
            display: flex;
            flex-direction: column;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--panel-shadow);
        }

        .panel-header {
            background: rgba(31, 41, 55, 0.4);
            border-bottom: 1px solid var(--card-border);
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .panel-body {
            flex: 1;
            padding: 1rem;
            position: relative;
            overflow: auto;
            display: flex;
            flex-direction: column;
        }

        .json-editor {
            flex: 1;
            font-family: 'JetBrains Mono', monospace;
            background: rgba(17, 24, 39, 0.5);
            color: #38bdf8;
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 8px;
            resize: none;
            padding: 0.75rem;
            font-size: 0.85rem;
            line-height: 1.4;
        }

        /* Response Viewer */
        .response-meta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .res-badge {
            font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
        }

        .res-success { background: rgba(16, 185, 129, 0.15); color: var(--accent); border: 1px solid rgba(16, 185, 129, 0.3); }
        .res-error { background: rgba(239, 68, 68, 0.15); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.3); }

        .time-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            color: var(--text-secondary);
            background: rgba(255,255,255,0.05);
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
        }

        .code-pre {
            margin: 0;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            line-height: 1.5;
            color: #e5e7eb;
            white-space: pre-wrap;
            word-break: break-all;
            background: rgba(17, 24, 39, 0.8);
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.03);
            flex: 1;
            overflow: auto;
        }

        .copy-btn {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--card-border);
            color: var(--text-primary);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.75rem;
            transition: all 0.2s;
        }

        .copy-btn:hover {
            background: rgba(255,255,255,0.1);
        }

        /* Loading Micro-animations */
        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading .spinner {
            display: inline-block;
        }
        .loading span {
            display: none;
        }

        /* JSON formatting colors */
        .json-key { color: #f43f5e; }
        .json-string { color: #34d399; }
        .json-number { color: #38bdf8; }
        .json-boolean { color: #fb7185; }
        .json-null { color: #9ca3af; }
    </style>
</head>
<body>
    <header>
        <div class="logo-area">
            <h1>API Tester <span>JoyVent Event System</span></h1>
            <div class="subtitle">Alat Penguji API Interaktif Terintegrasi</div>
        </div>
    </header>

    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Authentication Card -->
            <div class="sidebar-card">
                <h2>Otentikasi <span class="badge-req badge-auth" id="auth-status">Belum Login</span></h2>
                <div class="form-group">
                    <label for="login-role">Pilih Role / Pengguna</label>
                    <select id="login-role">
                        <option value="custom">-- Kustom (Input Manual) --</option>
                        <option value="admin" selected>Admin (admin@joyvent.com)</option>
                        <option value="participant">Peserta (UAT User 1)</option>
                    </select>
                </div>
                <div id="credentials-wrapper">
                    <div class="form-group">
                        <label for="login-email">Email</label>
                        <input type="email" id="login-email" value="admin_uat@joyvent.com">
                    </div>
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" value="password">
                    </div>
                </div>
                <div class="flex-buttons">
                    <button class="btn" id="btn-login">Login</button>
                    <button class="btn btn-secondary" id="btn-reset-token">Reset</button>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label for="access-token">Access Token (Sanctum)</label>
                    <input type="text" id="access-token" placeholder="Masukkan token Anda..." style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem;">
                </div>
            </div>

            <!-- Endpoint Selector -->
            <div class="sidebar-card">
                <h2>Konfigurasi Endpoint</h2>
                <div class="form-group">
                    <label for="endpoint-select">Pilih Endpoint</label>
                    <select id="endpoint-select">
                        <optgroup label="Otentikasi (Public)">
                            <option value="login" data-method="POST" data-url="/api/login" data-auth="false">POST /api/login</option>
                            <option value="register" data-method="POST" data-url="/api/register" data-auth="false">POST /api/register</option>
                            <option value="forgot-password" data-method="POST" data-url="/api/forgot-password" data-auth="false">POST /api/forgot-password</option>
                            <option value="verify-otp" data-method="POST" data-url="/api/verify-otp" data-auth="false">POST /api/verify-otp</option>
                            <option value="reset-password" data-method="POST" data-url="/api/reset-password" data-auth="false">POST /api/reset-password</option>
                        </optgroup>
                        <optgroup label="Otentikasi (Protected)">
                            <option value="me" data-method="GET" data-url="/api/user" data-auth="true">GET /api/user (Info Login)</option>
                            <option value="profile" data-method="POST" data-url="/api/profile" data-auth="true">POST /api/profile (Update Profil)</option>
                            <option value="logout" data-method="POST" data-url="/api/logout" data-auth="true">POST /api/logout</option>
                        </optgroup>
                        <optgroup label="Event">
                            <option value="events-list" data-method="GET" data-url="/api/events" data-auth="true">GET /api/events (Daftar Event)</option>
                            <option value="events-show" data-method="GET" data-url="/api/events/{id}" data-auth="true">GET /api/events/{id}</option>
                            <option value="events-create" data-method="POST" data-url="/api/events" data-auth="true" data-admin="true">POST /api/events (Admin)</option>
                            <option value="events-update" data-method="PUT" data-url="/api/events/{id}" data-auth="true" data-admin="true">PUT /api/events/{id} (Admin)</option>
                            <option value="events-delete" data-method="DELETE" data-url="/api/events/{id}" data-auth="true" data-admin="true">DELETE /api/events/{id} (Admin)</option>
                        </optgroup>
                        <optgroup label="Tiket">
                            <option value="tickets-list" data-method="GET" data-url="/api/ticket-categories" data-auth="true">GET /api/ticket-categories</option>
                            <option value="tickets-create" data-method="POST" data-url="/api/ticket-categories" data-auth="true" data-admin="true">POST /api/ticket-categories (Admin)</option>
                        </optgroup>
                        <optgroup label="Registrasi & Pembayaran">
                            <option value="reg-list" data-method="GET" data-url="/api/registrations" data-auth="true">GET /api/registrations</option>
                            <option value="reg-create" data-method="POST" data-url="/api/registrations" data-auth="true">POST /api/registrations (Daftar Event)</option>
                            <option value="reg-payment-settings" data-method="GET" data-url="/api/payment-settings" data-auth="true">GET /api/payment-settings</option>
                            <option value="reg-simulate" data-method="POST" data-url="/api/registrations/{id}/simulate-payment" data-auth="true">POST /api/.../simulate-payment</option>
                            <option value="reg-refund" data-method="POST" data-url="/api/registrations/{id}/refund" data-auth="true">POST /api/.../refund</option>
                        </optgroup>
                        <optgroup label="Lucky Draw">
                            <option value="ld-winners" data-method="GET" data-url="/api/events/{eventId}/winners" data-auth="true">GET /api/events/{id}/winners</option>
                            <option value="ld-mywins" data-method="GET" data-url="/api/lucky-draw/my-wins" data-auth="true">GET /api/lucky-draw/my-wins</option>
                            <option value="ld-draw" data-method="POST" data-url="/api/lucky-draw" data-auth="true" data-admin="true">POST /api/lucky-draw (Admin)</option>
                        </optgroup>
                        <optgroup label="Sertifikat">
                            <option value="cert-my" data-method="GET" data-url="/api/my-certificates" data-auth="true">GET /api/my-certificates</option>
                            <option value="cert-download" data-method="GET" data-url="/api/certificates/{id}/download" data-auth="true">GET /api/certificates/{id}/download</option>
                            <option value="cert-generate" data-method="POST" data-url="/api/generate-certificate" data-auth="true" data-admin="true">POST /api/generate-certificate (Admin)</option>
                        </optgroup>
                        <optgroup label="Notifikasi & Check-In">
                            <option value="notif-list" data-method="GET" data-url="/api/notifications" data-auth="true">GET /api/notifications</option>
                            <option value="notif-unread" data-method="GET" data-url="/api/notifications/unread-count" data-auth="true">GET /api/notifications/unread-count</option>
                            <option value="notif-read-all" data-method="PATCH" data-url="/api/notifications/read-all" data-auth="true">PATCH /api/notifications/read-all</option>
                            <option value="check-in-qr" data-method="POST" data-url="/api/check-in" data-auth="true" data-admin="true">POST /api/check-in (Admin)</option>
                        </optgroup>
                    </select>
                </div>
                <div id="endpoint-meta-info" style="font-size: 0.75rem; color: var(--text-secondary); line-height: 1.4;">
                    <!-- Filled dynamically -->
                </div>
            </div>

            <!-- Recent Requests -->
            <div class="sidebar-card" style="flex: 1; display: flex; flex-direction: column;">
                <h2>Request Terakhir</h2>
                <ul class="recent-list" id="recent-list-container">
                    <li style="color: var(--text-secondary); font-size: 0.8rem; text-align: center; margin-top: 1rem;">Belum ada request.</li>
                </ul>
            </div>
        </div>

        <!-- Workspace (Main Panel) -->
        <div class="workspace">
            <!-- URL BAR -->
            <div class="url-bar">
                <select class="method-select" id="req-method">
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="DELETE">DELETE</option>
                    <option value="PATCH">PATCH</option>
                </select>
                <input type="text" class="url-input" id="req-url" value="/api/user">
                <button class="btn btn-send" id="btn-send-request">
                    <span>Kirim Request</span>
                    <div class="spinner"></div>
                </button>
            </div>

            <!-- JSON Body & Response Container -->
            <div class="request-response-container">
                <!-- Request Panel -->
                <div class="panel-flex">
                    <div class="panel-header">
                        Request Body (JSON)
                        <span class="badge-req badge-no-auth" id="body-status">Raw JSON</span>
                    </div>
                    <div class="panel-body">
                        <textarea class="json-editor" id="req-body" placeholder="Masukkan JSON request body di sini..."></textarea>
                    </div>
                </div>

                <!-- Response Panel -->
                <div class="panel-flex">
                    <div class="panel-header">
                        Response
                        <div class="response-meta">
                            <span class="res-badge res-success" id="res-status-code">200 OK</span>
                            <span class="time-badge" id="res-time">0 ms</span>
                            <button class="copy-btn" id="btn-copy-response">Copy</button>
                        </div>
                    </div>
                    <div class="panel-body" style="background: rgba(17, 24, 39, 0.8);">
                        <pre class="code-pre" id="response-content">{
  "message": "Klik 'Kirim Request' untuk memanggil API."
}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dictionary template body request untuk setiap endpoint
        const requestTemplates = {
            'login': {
                method: 'POST',
                url: '/api/login',
                auth: false,
                body: {
                    email: 'admin_uat@joyvent.com',
                    password: 'password'
                }
            },
            'register': {
                method: 'POST',
                url: '/api/register',
                auth: false,
                body: {
                    name: 'Budi Santoso',
                    email: 'budi_santoso@joyvent.com',
                    password: 'password',
                    password_confirmation: 'password',
                    role: 'participant'
                }
            },
            'forgot-password': {
                method: 'POST',
                url: '/api/forgot-password',
                auth: false,
                body: {
                    email: 'budi_santoso@joyvent.com'
                }
            },
            'verify-otp': {
                method: 'POST',
                url: '/api/verify-otp',
                auth: false,
                body: {
                    email: 'budi_santoso@joyvent.com',
                    otp: '123456'
                }
            },
            'reset-password': {
                method: 'POST',
                url: '/api/reset-password',
                auth: false,
                body: {
                    email: 'budi_santoso@joyvent.com',
                    otp: '123456',
                    password: 'password_baru',
                    password_confirmation: 'password_baru'
                }
            },
            'me': { method: 'GET', url: '/api/user', auth: true, body: null },
            'profile': {
                method: 'POST',
                url: '/api/profile',
                auth: true,
                body: {
                    name: 'Budi Santoso Baru',
                    email: 'budi_baru@joyvent.com'
                }
            },
            'logout': { method: 'POST', url: '/api/logout', auth: true, body: null },
            'events-list': { method: 'GET', url: '/api/events', auth: true, body: null },
            'events-show': { method: 'GET', url: '/api/events/1', auth: true, body: null },
            'events-create': {
                method: 'POST',
                url: '/api/events',
                auth: true,
                admin: true,
                body: {
                    name: 'Festival Musik JoyVent 2026',
                    location: 'Jakarta International Stadium',
                    start_date: '2026-08-10',
                    end_date: '2026-08-11',
                    start_time: '14:00:00',
                    end_time: '23:00:00',
                    capacity: 10000,
                    has_lucky_draw: true,
                    is_configured: true
                }
            },
            'events-update': {
                method: 'PUT',
                url: '/api/events/1',
                auth: true,
                admin: true,
                body: {
                    name: 'Festival Musik JoyVent 2026 (Updated)',
                    location: 'Jakarta International Stadium',
                    start_date: '2026-08-10',
                    end_date: '2026-08-11',
                    start_time: '14:00:00',
                    end_time: '23:00:00',
                    capacity: 12000
                }
            },
            'events-delete': { method: 'DELETE', url: '/api/events/1', auth: true, admin: true, body: null },
            'tickets-list': { method: 'GET', url: '/api/ticket-categories', auth: true, body: null },
            'tickets-create': {
                method: 'POST',
                url: '/api/ticket-categories',
                auth: true,
                admin: true,
                body: {
                    event_id: 1,
                    name: 'VIP Gold',
                    price: 2500000,
                    quota: 100
                }
            },
            'reg-list': { method: 'GET', url: '/api/registrations', auth: true, body: null },
            'reg-create': {
                method: 'POST',
                url: '/api/registrations',
                auth: true,
                body: {
                    event_id: 1,
                    ticket_category_id: 1
                }
            },
            'reg-payment-settings': { method: 'GET', url: '/api/payment-settings', auth: true, body: null },
            'reg-simulate': { method: 'POST', url: '/api/registrations/1/simulate-payment', auth: true, body: null },
            'reg-refund': {
                method: 'POST',
                url: '/api/registrations/1/refund',
                auth: true,
                body: {
                    amount: 500000,
                    reason: 'Uang kuliah mendadak'
                }
            },
            'ld-winners': { method: 'GET', url: '/api/events/1/winners', auth: true, body: null },
            'ld-mywins': { method: 'GET', url: '/api/lucky-draw/my-wins', auth: true, body: null },
            'ld-draw': {
                method: 'POST',
                url: '/api/lucky-draw',
                auth: true,
                admin: true,
                body: {
                    event_prize_id: 1
                }
            },
            'cert-my': { method: 'GET', url: '/api/my-certificates', auth: true, body: null },
            'cert-download': { method: 'GET', url: '/api/certificates/1/download', auth: true, body: null },
            'cert-generate': {
                method: 'POST',
                url: '/api/generate-certificate',
                auth: true,
                admin: true,
                body: {
                    event_id: 1
                }
            },
            'notif-list': { method: 'GET', url: '/api/notifications', auth: true, body: null },
            'notif-unread': { method: 'GET', url: '/api/notifications/unread-count', auth: true, body: null },
            'notif-read-all': { method: 'PATCH', url: '/api/notifications/read-all', auth: true, body: null },
            'check-in-qr': {
                method: 'POST',
                url: '/api/check-in',
                auth: true,
                admin: true,
                body: {
                    qr_code: 'QR-UAT-1'
                }
            }
        };

        // State variables
        let token = localStorage.getItem('joyvent_api_token') || '';
        const recentRequests = JSON.parse(localStorage.getItem('joyvent_recent_requests') || '[]');

        // DOM elements
        const loginRoleSelect = document.getElementById('login-role');
        const loginEmailInput = document.getElementById('login-email');
        const loginPasswordInput = document.getElementById('login-password');
        const credentialsWrapper = document.getElementById('credentials-wrapper');
        const accessTokenInput = document.getElementById('access-token');
        const authStatusSpan = document.getElementById('auth-status');
        const endpointSelect = document.getElementById('endpoint-select');
        const endpointMetaInfo = document.getElementById('endpoint-meta-info');
        const reqMethodSelect = document.getElementById('req-method');
        const reqUrlInput = document.getElementById('req-url');
        const reqBodyTextArea = document.getElementById('req-body');
        const btnSendRequest = document.getElementById('btn-send-request');
        const responseContentPre = document.getElementById('response-content');
        const resStatusCodeSpan = document.getElementById('res-status-code');
        const resTimeSpan = document.getElementById('res-time');
        const recentListContainer = document.getElementById('recent-list-container');
        const btnLogin = document.getElementById('btn-login');
        const btnResetToken = document.getElementById('btn-reset-token');
        const btnCopyResponse = document.getElementById('btn-copy-response');

        // Initialize state
        if (token) {
            accessTokenInput.value = token;
            updateAuthBadge(true);
        }
        updateRecentListUI();

        // Handle role change to preset credentials
        loginRoleSelect.addEventListener('change', () => {
            const role = loginRoleSelect.value;
            if (role === 'admin') {
                credentialsWrapper.style.display = 'block';
                loginEmailInput.value = 'admin_uat@joyvent.com';
                loginPasswordInput.value = 'password';
            } else if (role === 'participant') {
                credentialsWrapper.style.display = 'block';
                loginEmailInput.value = 'uat_user1@joyvent.com';
                loginPasswordInput.value = 'password';
            } else {
                credentialsWrapper.style.display = 'block';
                loginEmailInput.value = '';
                loginPasswordInput.value = '';
            }
        });

        // Trigger endpoint load templates
        endpointSelect.addEventListener('change', loadEndpointTemplate);
        
        // Initial load
        loadEndpointTemplate();

        function loadEndpointTemplate() {
            const selectedVal = endpointSelect.value;
            const template = requestTemplates[selectedVal];
            if (!template) return;

            reqMethodSelect.value = template.method;
            
            // Dapatkan domain host saat ini secara dinamis
            const baseUrl = window.location.origin;
            reqUrlInput.value = baseUrl + template.url;

            if (template.body) {
                reqBodyTextArea.value = JSON.stringify(template.body, null, 2);
                reqBodyTextArea.disabled = false;
            } else {
                reqBodyTextArea.value = '// Tidak diperlukan request body untuk endpoint ini.';
                reqBodyTextArea.disabled = true;
            }

            // Update meta info
            let metaHtml = `
                <div style="margin-top: 0.5rem; display:flex; flex-direction:column; gap:0.25rem;">
                    <div><strong>Metode:</strong> <span class="method-badge method-${template.method}">${template.method}</span></div>
                    <div><strong>Url Path:</strong> <code>${template.url}</code></div>
                    <div><strong>Autentikasi:</strong> ${template.auth ? '<span class="badge-req badge-auth">YA (Token)</span>' : '<span class="badge-req badge-no-auth">TIDAK (Publik)</span>'}</div>
                    ${template.admin ? '<div><strong>Role Akses:</strong> <span class="badge-req badge-auth" style="background:rgba(239,68,68,0.15); color:var(--danger); border-color:rgba(239,68,68,0.3);">ADMIN ONLY</span></div>' : ''}
                </div>
            `;
            endpointMetaInfo.innerHTML = metaHtml;
        }

        // Login AJAX request to fetch Token
        btnLogin.addEventListener('click', async () => {
            const email = loginEmailInput.value;
            const password = loginPasswordInput.value;

            if (!email || !password) {
                alert('Silakan masukkan email dan password.');
                return;
            }

            btnLogin.classList.add('loading');
            try {
                const startTime = performance.now();
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });
                const endTime = performance.now();
                const data = await response.json();

                if (response.ok && data.success && data.data && data.data.token) {
                    token = data.data.token;
                    localStorage.setItem('joyvent_api_token', token);
                    accessTokenInput.value = token;
                    updateAuthBadge(true);
                    displayResponse(200, Math.round(endTime - startTime), data);
                    addRecentRequest('POST', '/api/login', 200);
                } else {
                    displayResponse(response.status, Math.round(endTime - startTime), data);
                    alert('Login gagal: ' + (data.message || 'Error tidak diketahui.'));
                    addRecentRequest('POST', '/api/login', response.status);
                }
            } catch (err) {
                console.error(err);
                alert('Koneksi ke server gagal.');
            } finally {
                btnLogin.classList.remove('loading');
            }
        });

        // Reset Token
        btnResetToken.addEventListener('click', () => {
            token = '';
            localStorage.removeItem('joyvent_api_token');
            accessTokenInput.value = '';
            updateAuthBadge(false);
        });

        function updateAuthBadge(isLoggedIn) {
            if (isLoggedIn) {
                authStatusSpan.textContent = 'Aktif';
                authStatusSpan.className = 'badge-req badge-no-auth';
            } else {
                authStatusSpan.textContent = 'Belum Login';
                authStatusSpan.className = 'badge-req badge-auth';
            }
        }

        // Send API Request
        btnSendRequest.addEventListener('click', async () => {
            const method = reqMethodSelect.value;
            const url = reqUrlInput.value;
            const manualToken = accessTokenInput.value.trim();

            btnSendRequest.classList.add('loading');
            responseContentPre.textContent = '// Menghubungi server...';

            const headers = {
                'Accept': 'application/json'
            };

            if (manualToken) {
                headers['Authorization'] = 'Bearer ' + manualToken;
            }

            let fetchOptions = { method, headers };

            if (method !== 'GET' && method !== 'HEAD') {
                const bodyText = reqBodyTextArea.value;
                if (bodyText && !bodyText.startsWith('//')) {
                    try {
                        JSON.parse(bodyText); // Validasi JSON
                        headers['Content-Type'] = 'application/json';
                        fetchOptions.body = bodyText;
                    } catch (e) {
                        alert('Format JSON di Request Body salah. Silakan periksa kembali.');
                        btnSendRequest.classList.remove('loading');
                        return;
                    }
                }
            }

            try {
                const startTime = performance.now();
                const response = await fetch(url, fetchOptions);
                const endTime = performance.now();
                const duration = Math.round(endTime - startTime);

                let data;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    data = { message: await response.text() };
                }

                displayResponse(response.status, duration, data);
                
                // Get short path for logs
                let logPath = url;
                try {
                    const parsedUrl = new URL(url);
                    logPath = parsedUrl.pathname;
                } catch(e) {}
                addRecentRequest(method, logPath, response.status);

            } catch (err) {
                console.error(err);
                displayResponse(500, 0, { error: 'Koneksi ke API terputus atau server tidak merespons.', detail: err.message });
            } finally {
                btnSendRequest.classList.remove('loading');
            }
        });

        // Copy Response Code
        btnCopyResponse.addEventListener('click', () => {
            const codeText = responseContentPre.textContent;
            navigator.clipboard.writeText(codeText).then(() => {
                const originalText = btnCopyResponse.textContent;
                btnCopyResponse.textContent = 'Copied!';
                setTimeout(() => btnCopyResponse.textContent = originalText, 1500);
            }).catch(err => {
                alert('Gagal menyalin teks.');
            });
        });

        // Function to display response
        function displayResponse(status, duration, data) {
            resStatusCodeSpan.textContent = status + ' ' + getHttpStatusText(status);
            if (status >= 200 && status < 300) {
                resStatusCodeSpan.className = 'res-badge res-success';
            } else {
                resStatusCodeSpan.className = 'res-badge res-error';
            }

            resTimeSpan.textContent = duration + ' ms';
            responseContentPre.innerHTML = syntaxHighlightJSON(data);
        }

        function getHttpStatusText(status) {
            const codes = {
                200: 'OK',
                201: 'Created',
                204: 'No Content',
                400: 'Bad Request',
                401: 'Unauthorized',
                403: 'Forbidden',
                404: 'Not Found',
                409: 'Conflict',
                422: 'Unprocessable Entity',
                500: 'Internal Server Error'
            };
            return codes[status] || '';
        }

        // Add request to local logs
        function addRecentRequest(method, path, status) {
            recentRequests.unshift({ method, path, status, timestamp: new Date().toLocaleTimeString() });
            if (recentRequests.length > 8) {
                recentRequests.pop();
            }
            localStorage.setItem('joyvent_recent_requests', JSON.stringify(recentRequests));
            updateRecentListUI();
        }

        function updateRecentListUI() {
            if (recentRequests.length === 0) {
                recentListContainer.innerHTML = `<li style="color: var(--text-secondary); font-size: 0.8rem; text-align: center; margin-top: 1rem;">Belum ada request.</li>`;
                return;
            }

            let html = '';
            recentRequests.forEach((req, index) => {
                const statusClass = (req.status >= 200 && req.status < 300) ? 'status-2xx' : 'status-4xx';
                html += `
                    <li class="recent-item" onclick="loadRecentRequest(${index})">
                        <span class="method-badge method-${req.method}">${req.method}</span>
                        <span style="font-family: 'JetBrains Mono', monospace; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width: 150px; margin: 0 0.5rem; text-align:left; flex:1;">${req.path}</span>
                        <span class="status-badge ${statusClass}">${req.status}</span>
                    </li>
                `;
            });
            recentListContainer.innerHTML = html;
        }

        // Click recent item to load configuration back
        window.loadRecentRequest = function(index) {
            const req = recentRequests[index];
            if (!req) return;

            reqMethodSelect.value = req.method;
            const baseUrl = window.location.origin;
            reqUrlInput.value = baseUrl + req.path;

            // Cari dropdown yang cocok
            for (let i = 0; i < endpointSelect.options.length; i++) {
                const opt = endpointSelect.options[i];
                if (opt.getAttribute('data-method') === req.method && opt.getAttribute('data-url') === req.path) {
                    endpointSelect.selectedIndex = i;
                    loadEndpointTemplate();
                    break;
                }
            }
        };

        // Syntax highlighting function for JSON
        function syntaxHighlightJSON(json) {
            if (typeof json !== 'string') {
                json = JSON.stringify(json, undefined, 2);
            }
            json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+-]?\d+)?)/g, function (match) {
                var cls = 'json-number';
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) {
                        cls = 'json-key';
                    } else {
                        cls = 'json-string';
                    }
                } else if (/true|false/.test(match)) {
                    cls = 'json-boolean';
                } else if (/null/.test(match)) {
                    cls = 'json-null';
                }
                if (cls === 'json-key') {
                    return '<span class="' + cls + '">' + match.replace(/:$/, '') + '</span>:';
                } else {
                    return '<span class="' + cls + '">' + match + '</span>';
                }
            });
        }
    </script>
</body>
</html>
