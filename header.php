<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ETTAAJ RENT CARS - Premium Car Rental in Morocco</title>

  <!-- Favicon -->
  <link rel="icon" href="pub_img/GoldCar.png" type="image/png" sizes="512x512">
  <link rel="icon" href="pub_img/favicon.ico" type="image/x-icon">
  <link rel="apple-touch-icon" href="pub_img/GoldCar.png">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { 
            gold: '#FFD700', 
            'gold-dark': '#E6C200',
            'light-gold': '#d97706'
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- ==== CSS VARIABLES (UNIFIED & FIXED) ==== -->
  <style>
    :root {
      --bg: #36454F;
      --bg-dark: #2C3A44;
      --card: #36454F;
      --card-dark: #2C3A44;
      --border: #4A5A66;
      --primary: #FFFFFF;
      --muted: #D1D5DB;
      --gold: #FFD700;
      --gold-dark: #E6C200;
      --hover-bg: rgba(255, 215, 0, 0.1);
    }
    .light {
      --bg: #f8fafc;
      --bg-dark: #e2e8f0;
      --card: #ffffff;
      --card-dark: #f1f5f9;
      --border: #cbd5e1;
      --primary: #1e293b;
      --muted: #64748b;
      --gold: #d97706;
      --gold-dark: #b45309;
      --hover-bg: rgba(217, 119, 6, 0.1);
    }

    body { 
      font-family: 'Inter', sans-serif; 
      background-color: var(--bg); 
      color: var(--primary);
      scroll-behavior: smooth; 
    }

    /* Layout */
    .sidebar {
      transition: transform 0.3s ease-in-out;
      background-color: var(--bg-dark);
      border-color: var(--border);
    }
    .sidebar.open  { transform: translateX(0); }
    .sidebar.closed { transform: translateX(-100%); }

    /* Links & Hovers */
    .nav-link {
      @apply text-current hover:text-[var(--gold-dark)] transition;
    }
    .nav-link:hover {
      background-color: var(--hover-bg);
      border-radius: 0.5rem;
    }

    /* Gold text */
    .text-gold { color: var(--gold) !important; }
    .hover\:text-gold-dark:hover { color: var(--gold-dark) !important; }

    /* Borders & Backgrounds */
    .border-border { border-color: var(--border); }
    .bg-card { background-color: var(--card); }

    /* ==== GOLD GRADIENT UTILITIES ==== */
    .bg-gradient-gold {
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .bg-gradient-gold-reverse {
      background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .bg-gradient-gold-soft {
      background: linear-gradient(135deg, rgba(255,215,0,0.2) 0%, rgba(230,194,0,0.1) 100%);
    }

    /* Button Gradient */
    .btn-gold {
      @apply bg-gradient-to-r from-[var(--gold)] to-[var(--gold-dark)] 
             hover:from-[var(--gold-dark)] hover:to-[var(--gold)] 
             text-white font-bold rounded-full shadow-lg 
             transform transition-all duration-300 
             hover:scale-105 hover:shadow-[0_0_20px_rgba(255,215,0,0.4)] 
             active:scale-95;
    }

    /* ==== Theme Toggle (Reusable) ==== */
    .theme-toggle {
      position: relative;
      width: 50px;
      height: 26px;
    }
    .theme-toggle input { opacity: 0; width: 0; height: 0; }
    .slider {
      position: absolute;
      cursor: pointer;
      inset: 0;
      background-color: #94a3b8;
      transition: .4s;
      border-radius: 34px;
    }
    .slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }
    input:checked + .slider {
      background-color: var(--gold);
    }
    input:checked + .slider:before {
      transform: translateX(24px);
    }
    .sun, .moon {
      position: absolute;
      top: 3px;
      width: 20px;
      height: 20px;
      transition: opacity .3s;
    }
    .sun { left: 4px; opacity: 1; }
    .moon { right: 4px; opacity: 0; }
    input:checked ~ .sun { opacity: 0; }
    input:checked ~ .moon { opacity: 1; }
  </style>
</head>
<body class="min-h-screen">

  <!-- ==== MOBILE SIDEBAR ==== -->
  <div id="mobile-sidebar"
       class="fixed inset-y-0 left-0 w-64 bg-[var(--bg-dark)]/95 backdrop-blur-md shadow-2xl z-50 sidebar closed lg:hidden border-r border-border">
    <div class="p-6">
      <div class="flex justify-between items-center mb-8">
        <a href="index.php" class="flex items-center space-x-2">
          <img src="pub_img/GoldCar.png" alt="Logo" class="w-10 h-10 rounded-full ring-2 ring-[var(--gold)]/30">
          <span class="text-xl font-bold bg-gradient-gold">ETTAAJ RENT CARS</span>
        </a>
        <button id="close-sidebar" class="text-current hover:text-[var(--gold-dark)] transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <nav class="space-y-4">
        <a href="index.php" class="nav-link block px-3 py-2 rounded-lg">Home</a>
        <a href="index.php#cars" class="nav-link block px-3 py-2 rounded-lg">Cars</a>
        <a href="about.php" class="nav-link block px-3 py-2 rounded-lg">About</a>
        <a href="contact.php" class="nav-link block px-3 py-2 rounded-lg">Contact</a>
      </nav>

      <!-- DARK MODE TOGGLE (MOBILE) -->
      <div class="mt-6 pt-4 border-t border-border flex items-center justify-between">
        <span class="text-sm text-[var(--muted)]">Dark Mode</span>
        <label class="theme-toggle">
          <input type="checkbox" id="theme-switch-mobile">
          <span class="slider">
            <svg class="sun w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 6.95a.5.5 0 01-.707 0l-1.414-1.414a.5.5 0 01.707-.707l1.414 1.414a.5.5 0 010 .707zM5.464 5.464a.5.5 0 01-.707 0L3.343 4.05a.5.5 0 11.707-.707l1.414 1.414a.5.5 0 010 .707zm9.072 9.072a.5.5 0 01-.707 0l-1.414-1.414a.5.5 0 11.707-.707l1.414 1.414a.5.5 0 010 .707zM5.464 14.536a.5.5 0 01-.707 0L3.343 13.122a.5.5 0 11.707-.707l1.414 1.414a.5.5 0 010 .707z"/>
            </svg>
            <svg class="moon w-4 h-4 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
              <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8 0 1010.586 10.586z"/>
            </svg>
          </span>
        </label>
      </div>

      <div class="mt-6 pt-6 border-t border-border">
        <a href="tel:+212772331080" class="flex items-center gap-2 text-gold hover:text-[var(--gold-dark)] transition">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
          </svg>
          <span class="font-semibold">+212 772 331 080</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Overlay -->
  <div id="sidebar-overlay" class="fixed inset-0 bg-black/70 z-40 hidden lg:hidden"></div>

  <!-- ==== HEADER (DESKTOP) ==== -->
  <header class="bg-[var(--bg-dark)]/90 backdrop-blur-md shadow-lg sticky top-0 z-30 border-b border-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

      <!-- Mobile -->
      <div class="flex items-center justify-between lg:hidden">
        <a href="index.php" class="flex items-center space-x-2">
          <img src="pub_img/GoldCar.png" alt="Logo" class="w-10 h-10 rounded-full ring-2 ring-[var(--gold)]/30">
          <span class="text-xl sm:text-2xl font-bold bg-gradient-gold">ETTAAJ RENT CARS</span>
        </a>
        <button id="open-sidebar" class="text-current hover:text-[var(--gold-dark)] transition">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>

      <!-- Desktop -->
      <div class="hidden lg:flex items-center justify-between">
        <a href="index.php" class="flex items-center space-x-2">
          <img src="pub_img/GoldCar.png" alt="Logo" class="w-10 h-10 rounded-full ring-2 ring-[var(--gold)]/30">
          <span class="text-xl sm:text-2xl font-bold bg-gradient-gold">ETTAAJ RENT CARS</span>
        </a>

        <div class="flex items-center space-x-8">
          <nav class="flex space-x-8">
            <a href="index.php" class="nav-link">Home</a>
            <a href="index.php#cars" class="nav-link">Cars</a>
            <a href="about.php" class="nav-link">About</a>
            <a href="contact.php" class="nav-link">Contact</a>
          </nav>

          <a href="tel:+212772331080" class="flex items-center gap-2 text-gold hover:text-[var(--gold-dark)] font-semibold transition">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
            </svg>
            +212 772 331 080
          </a>

          <!-- DARK MODE TOGGLE (DESKTOP) -->
          <div class="flex items-center gap-2">
            <span class="text-sm text-[var(--muted)]">Dark Mode</span>
            <label class="theme-toggle">
              <input type="checkbox" id="theme-switch-desktop">
              <span class="slider">
                <svg class="sun w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 6.95a.5.5 0 01-.707 0l-1.414-1.414a.5.5 0 01.707-.707l1.414 1.414a.5.5 0 010 .707zM5.464 5.464a.5.5 0 01-.707 0L3.343 4.05a.5.5 0 11.707-.707l1.414 1.414a.5.5 0 010 .707zm9.072 9.072a.5.5 0 01-.707 0l-1.414-1.414a.5.5 0 11.707-.707l1.414 1.414a.5.5 0 010 .707zM5.464 14.536a.5.5 0 01-.707 0L3.343 13.122a.5.5 0 11.707-.707l1.414 1.414a.5.5 0 010 .707z"/>
                </svg>
                <svg class="moon w-4 h-4 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8 0 1010.586 10.586z"/>
                </svg>
              </span>
            </label>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- ==== Scripts ==== -->
  <script>
    // Sidebar
    const sidebar   = document.getElementById('mobile-sidebar');
    const overlay   = document.getElementById('sidebar-overlay');
    const openBtn   = document.getElementById('open-sidebar');
    const closeBtn  = document.getElementById('close-sidebar');

    openBtn?.addEventListener('click', () => {
      sidebar.classList.replace('closed', 'open');
      overlay.classList.remove('hidden');
    });
    const closeSidebar = () => {
      sidebar.classList.replace('open', 'closed');
      overlay.classList.add('hidden');
    };
    closeBtn?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    // === DARK MODE: Sync Mobile + Desktop ===
    const html           = document.documentElement;
    const mobileToggle   = document.getElementById('theme-switch-mobile');
    const desktopToggle  = document.getElementById('theme-switch-desktop');

    const applyTheme = (isLight) => {
      if (isLight) {
        html.classList.add('light');
      } else {
        html.classList.remove('light');
      }
      // Sync both toggles
      if (mobileToggle) mobileToggle.checked = isLight;
      if (desktopToggle) desktopToggle.checked = isLight;
      localStorage.setItem('theme', isLight ? 'light' : 'dark');
    };

    // Initial load
    const saved        = localStorage.getItem('theme');
    const prefersLight = window.matchMedia('(prefers-color-scheme: light)').matches;
    const isLight      = saved === 'light' || (!saved && prefersLight);
    applyTheme(isLight);

    // Listen to both toggles
    [mobileToggle, desktopToggle].forEach(toggle => {
      if (toggle) {
        toggle.addEventListener('change', () => applyTheme(toggle.checked));
      }
    });
  </script>
</body>
</html>