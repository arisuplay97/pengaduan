<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tiara Smart Assistant')</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --primary:#6366F1;
            --primary-glow: rgba(99, 102, 241, .35);

            --bg-body:#F8FAFC;
            --bg-card:#FFFFFF;
            --bg-elev:#FFFFFF;
            --bg-soft:#F1F5F9;

            --text-main:#0F172A;
            --text-muted:#64748B;

            --danger:#EF4444;
            --warning:#F59E0B;
            --success:#10B981;

            --border-color:#E2E8F0;
            --radius: 18px;
            --radius-lg: 24px;

            --shadow-sm: 0 2px 10px rgba(2,6,23,.04);
            --shadow-md: 0 12px 30px rgba(2,6,23,.08);
            --focus: 0 0 0 4px var(--primary-glow);
        }

        /* Dark theme - Teal/Cyan matching nolana layout */
        html[data-theme="dark"]{
            --bg-body:#0d1b1e;
            --bg-card:#112428;
            --bg-elev:#0a1416;
            --bg-soft:#0f2225;

            --text-main:#e2e8f0;
            --text-muted:#94a3b8;

            --border-color:#1a3a3f;

            --shadow-sm: 0 2px 12px rgba(0,0,0,.35);
            --shadow-md: 0 18px 45px rgba(0,0,0,.45);
            
            --primary:#22d3ee;
            --primary-glow: rgba(34, 211, 238, .35);
        }
        
        html[data-theme="dark"] body,
        html[data-theme="dark"] html {
            background-color: #0d1b1e !important;
        }

        *{margin:0;padding:0;box-sizing:border-box;font-family:'Plus Jakarta Sans',sans-serif;outline:none;}
        body{
            background: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        html {
            background: var(--bg-body);
        }

        a{color:inherit;text-decoration:none;}
        button{font:inherit;}
        .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;}

        /* Layout */
        .app{
            display:flex;
            min-height:100vh;
        }

        /* Sidebar */
        .sidebar{
            width: 280px;
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
            padding: 2rem 1.5rem;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 50;
        }

        .brand{
            font-size:1.6rem;
            font-weight:800;
            color:var(--primary);
            display:flex;
            align-items:center;
            gap:12px;
            margin-bottom: 2rem;
            letter-spacing:-1px;
            user-select:none;
        }

        .nav-category{
            font-size:.7rem;
            text-transform:uppercase;
            letter-spacing:1.5px;
            color:var(--text-muted);
            margin: 22px 0 10px 10px;
            font-weight:800;
        }

        .nav{
            display:flex;
            flex-direction:column;
            gap:8px;
        }

        .nav-link{
            display:flex;
            align-items:center;
            gap:14px;
            padding: 12px 14px;
            border-radius: 16px;
            color: var(--text-muted);
            font-weight: 700;
            transition: transform .2s ease, background .2s ease, color .2s ease, box-shadow .2s ease;
        }
        .nav-link:hover{
            background: var(--bg-soft);
            color: var(--primary);
            transform: translateX(4px);
        }
        .nav-link.active{
            background: var(--primary);
            color:#fff;
            box-shadow: 0 10px 22px -8px var(--primary-glow);
        }
        .nav-link:focus-visible{
            box-shadow: var(--focus);
        }

        /* Main */
        .main{
            margin-left: 280px;
            width: calc(100% - 280px);
            padding: 2rem 2rem 3rem;
        }

        .container{
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header */
        .top-header{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        h1{
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.6px;
        }
        .welcome-msg{
            color: var(--text-muted);
            font-size: 1rem;
            margin-top: 6px;
        }

        .header-tools{
            display:flex;
            align-items:center;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .search-box{
            position: relative;
            width: 360px;
            max-width: 48vw;
        }
        .search-box input{
            width:100%;
            padding: 14px 16px 14px 52px;
            border-radius: 999px;
            border: 1px solid var(--border-color);
            background: var(--bg-elev);
            color: var(--text-main);
            font-size: .95rem;
            transition: .2s;
            box-shadow: var(--shadow-sm);
        }
        .search-box input::placeholder{color: var(--text-muted);}
        .search-box input:focus{
            border-color: var(--primary);
            box-shadow: var(--focus);
        }
        .search-icon{
            position:absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.25rem;
        }

        .icon-btn{
            width: 46px;
            height: 46px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            background: var(--bg-elev);
            color: var(--text-muted);
            display:grid;
            place-items:center;
            font-size: 1.35rem;
            cursor:pointer;
            transition: transform .18s ease, background .18s ease, color .18s ease, box-shadow .18s ease;
            box-shadow: var(--shadow-sm);
        }
        .icon-btn:hover{
            background: var(--primary);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .icon-btn:focus-visible{
            box-shadow: var(--focus);
        }

        .profile-card{
            display:flex;
            align-items:center;
            gap: 10px;
            padding: 6px 10px;
            background: var(--bg-elev);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            cursor:pointer;
            box-shadow: var(--shadow-sm);
            transition: .18s ease;
        }
        .profile-card:hover{transform: translateY(-1px); box-shadow: var(--shadow-md);}
        .profile-img{
            width: 36px;
            height: 36px;
            border-radius: 12px;
            object-fit: cover;
        }

        /* Mobile / responsive */
        .mobile-bar{
            display:none;
            position: sticky;
            top: 0;
            z-index: 60;
            padding: 14px 16px;
            background: rgba(255,255,255,.75);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
        }
        html[data-theme="dark"] .mobile-bar{
            background: rgba(15,23,42,.75);
        }
        .mobile-bar-inner{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: 12px;
        }

        .sidebar-overlay{
            display:none;
            position: fixed;
            inset:0;
            background: rgba(2,6,23,.55);
            z-index: 45;
        }

        @media (max-width: 1024px){
            .main{padding: 1.25rem 1.25rem 2.5rem;}
            .search-box{width: 320px;}
        }

        @media (max-width: 860px){
            .mobile-bar{display:block;}
            .sidebar{
                transform: translateX(-105%);
                transition: transform .25s ease;
                box-shadow: var(--shadow-md);
            }
            body.sidebar-open .sidebar{transform: translateX(0);}
            body.sidebar-open .sidebar-overlay{display:block;}
            .main{
                margin-left: 0;
                width: 100%;
                padding-top: 1rem;
            }
            .top-header{flex-direction:column; align-items:flex-start;}
            .header-tools{width:100%; justify-content:space-between;}
            .search-box{max-width: 100%; width:100%;}
        }

        /* SweetAlert Premium Override */
        .swal2-popup{border-radius: 28px !important; padding: 2.25rem !important; background: var(--bg-card) !important;}
        .swal2-title{font-weight: 900 !important; color: var(--text-main) !important;}
        .swal2-html-container{color: var(--text-muted) !important;}
        .swal2-input,.swal2-select{
            border-radius: 14px !important;
            border: 1px solid var(--border-color) !important;
            background: var(--bg-elev) !important;
            color: var(--text-main) !important;
            font-size: 1rem !important;
            height: 3.25rem !important;
        }
        .swal-label{display:block;text-align:left;font-size:.85rem;font-weight:800;color:var(--text-muted);margin: 14px 0 6px 5px;}
    </style>

    @stack('styles')
</head>

<body>
    <div class="app">

        <!-- Mobile top bar -->
        <div class="mobile-bar">
            <div class="mobile-bar-inner">
                <button class="icon-btn" id="sidebarToggle" aria-label="Buka menu">
                    <i class="ph ph-list"></i>
                </button>
                <div class="brand" style="margin:0;font-size:1.2rem;">
                    <i class="ph-fill ph-sparkle"></i> Tiara
                </div>
                <button class="icon-btn" id="themeToggleMobile" aria-label="Ganti tema">
                    <i class="ph-fill ph-moon"></i>
                </button>
            </div>
        </div>

        <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

        <nav class="sidebar" aria-label="Sidebar">
            <div class="brand"><i class="ph-fill ph-sparkle"></i> Tiara</div>

            <div class="nav-category">Management</div>
            <div class="nav">
                <a class="nav-link active" href="#" aria-current="page">
                    <i class="ph-fill ph-squares-four"></i> Dashboard
                </a>
                <a class="nav-link" href="#">
                    <i class="ph-bold ph-calendar"></i> Agenda Direksi
                </a>
                <a class="nav-link" href="#">
                    <i class="ph-bold ph-files"></i> Dokumen
                </a>
            </div>

            <div class="nav-category">Smart Features</div>
            <div class="nav">
                <a class="nav-link" href="#">
                    <i class="ph-bold ph-paper-plane-tilt"></i> Disposisi Surat
                </a>
            </div>
        </nav>

        <main class="main">
            <div class="container">

                <div class="top-header">
                    <div>
                        @yield('header')
                        @hasSection('header')
                            {{-- handled by pages --}}
                        @else
                            <h1>Halo, Pak Arief! 👋</h1>
                            <p class="welcome-msg">Berikut ringkasan prioritas hari ini.</p>
                        @endif
                    </div>

                    <div class="header-tools">
                        <div class="search-box">
                            <i class="ph ph-magnifying-glass search-icon" aria-hidden="true"></i>
                            <input type="text" placeholder="Cari agenda atau PIC..." aria-label="Cari agenda atau PIC">
                        </div>

                        <button class="icon-btn" aria-label="Notifikasi">
                            <i class="ph ph-bell" aria-hidden="true"></i>
                        </button>

                        <button class="icon-btn" id="themeToggle" aria-label="Ganti tema">
                            <i class="ph-fill ph-moon" aria-hidden="true"></i>
                        </button>

                        <div class="profile-card" role="button" tabindex="0" aria-label="Profil">
                            <img src="https://ui-avatars.com/api/?name=Arief+R&background=6366F1&color=fff" class="profile-img" alt="Avatar">
                            <span style="font-weight:800; font-size:.9rem;">AR</span>
                        </div>
                    </div>
                </div>

                @yield('content')

            </div>
        </main>
    </div>

    <script>
        // Persist theme
        const root = document.documentElement;
        const storedTheme = localStorage.getItem('tiara_theme');
        if (storedTheme) root.setAttribute('data-theme', storedTheme);

        function toggleTheme(){
            const cur = root.getAttribute('data-theme') || 'light';
            const next = cur === 'light' ? 'dark' : 'light';
            root.setAttribute('data-theme', next);
            localStorage.setItem('tiara_theme', next);
        }

        document.getElementById('themeToggle')?.addEventListener('click', toggleTheme);
        document.getElementById('themeToggleMobile')?.addEventListener('click', toggleTheme);

        // Mobile sidebar
        const toggleBtn = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebarOverlay');

        function openSidebar(){ document.body.classList.add('sidebar-open'); }
        function closeSidebar(){ document.body.classList.remove('sidebar-open'); }

        toggleBtn?.addEventListener('click', () => {
            document.body.classList.contains('sidebar-open') ? closeSidebar() : openSidebar();
        });

        overlay?.addEventListener('click', closeSidebar);
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeSidebar();
        });
    </script>

    @stack('scripts')
    
    <!-- Audio Notification Logic (Same as Monitor) -->
    <script>window.audioPath = "{{ asset('audio/notification.mp3') }}";</script>
    <script src="{{ asset('js/tts-notification.js') }}?v={{ time() }}"></script>
    <script>
        // Silent Audio Unlocker
        document.addEventListener('click', function() {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                const ctx = new AudioContext();
                if (ctx.state === 'suspended') ctx.resume();
            }
        }, { once: true });

        document.addEventListener('DOMContentLoaded', function() {
            const notifKey = 'tiara_notified_session_' + new Date().toDateString();
            let notifiedSet = new Set(JSON.parse(sessionStorage.getItem(notifKey) || '[]'));
            
            function checkGlobalNotifications() {
                // console.log('Checking dashboard notifications... (App Layout)'); 
                fetch('{{ route("api.upcoming") }}')
                    .then(res => res.json())
                    .then(events => {
                        if (!events || events.length === 0) return;
                        
                        events.forEach(event => {
                            if (!notifiedSet.has(event.id)) {
                                 console.log('Triggering notification for:', event.title);
                                 playTTSNotification("Hallo. Ini adalah pengingat dari Smart Agenda. Kegiatan Anda akan segera dimulai.");
                                 notifiedSet.add(event.id);
                            }
                        });
                        sessionStorage.setItem(notifKey, JSON.stringify([...notifiedSet]));
                    })
                    .catch(err => console.error('Notif Check Error:', err));
            }
            
            setTimeout(checkGlobalNotifications, 3000);
            setInterval(checkGlobalNotifications, 60000);
        });
    </script>
</body>
</html>
