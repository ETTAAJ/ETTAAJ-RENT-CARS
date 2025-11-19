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
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

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

    .sidebar { transition: transform 0.3s ease-in-out; background-color: var(--bg-dark); border-color: var(--border); }
    .sidebar.open  { transform: translateX(0); }
    .sidebar.closed { transform: translateX(-100%); }

    .nav-link { @apply text-current hover:text-[var(--gold-dark)] transition; }
    .nav-link:hover { background-color: var(--hover-bg); border-radius: 0.5rem; }

    .text-gold { color: var(--gold) !important; }
    .hover\:text-gold-dark:hover { color: var(--gold-dark) !important; }

    .bg-gradient-gold {
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .btn-gold {
      @apply bg-gradient-to-r from-[var(--gold)] to-[var(--gold-dark)] 
             hover:from-[var(--gold-dark)] hover:to-[var(--gold)] 
             text-white font-bold rounded-full shadow-lg 
             transform transition-all duration-300 
             hover:scale-105 hover:shadow-[0_0_20px_rgba(255,215,0,0.4)] active:scale-95;
    }

    /* Theme Toggle */
    .theme-toggle { position: relative; width: 50px; height: 26px; }
    .theme-toggle input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; inset: 0; background-color: #94a3b8; transition: .4s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: var(--gold); }
    input:checked + .slider:before { transform: translateX(24px); }
    .sun, .moon { position: absolute; top: 3px; width: 20px; height: 20px; transition: opacity .3s; }
    .sun { left: 4px; opacity: 1; }
    .moon { right: 4px; opacity: 0; }
    input:checked ~ .sun { opacity: 0; }
    input:checked ~ .moon { opacity: 1; }
  </style>
</head>
<body class="min-h-screen">

  <!-- OFFICIAL NUMBER -->
  <?php 
    $whatsapp_number   = "212772331080";           // Your correct number (no +)
    $formatted_number  = "+212 772 331 080";       // Display format
    $wa_link           = "https://wa.me/$whatsapp_number";
  ?>

  <!-- Mobile Sidebar -->
  <div id="mobile-sidebar" class="fixed inset-y-0 left-0 w-64 bg-[var(--bg-dark)]/95 backdrop-blur-md shadow-2xl z-50 sidebar closed lg:hidden border-r border-border">
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

      <div class="mt-6 pt-4 border-t border-border flex items-center justify-between">
        <span class="text-sm text-[var(--muted)]">Dark Mode</span>
        <label class="theme-toggle">
          <input type="checkbox" id="theme-switch-mobile">
          <span class="slider">
            <svg class="sun w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 6.95a.5.5 0 01-.707 0l-1.414-1.414a.5.5 0 01.707-.707l1.414 1.414a.5.5 0 010 .707zM5.464 5.464a.5.5 0 01-.707 0L3.343 4.05a.5.5 0 11.707-.707l1.414 1.414a.5.5 0 010 .707zm9.072 9.072a.5.5 0 01-.707 0l-1.414-1.414a.5.5 0 11.707-.707l1.414 1.414a.5.5 0 010 .707zM5.464 14.536a.5.5 0 01-.707 0L3.343 13.122a.5.5 0 11.707-.707l1.414 1.414a.5.5 0 010 .707z"/></svg>
            <svg class="moon w-4 h-4 text-gray-700" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8 0 1010.586 10.586z"/></svg>
          </span>
        </label>
      </div>

      <div class="mt-6 pt-6 border-t border-border">
        <a href="<?= $wa_link ?>" class="flex items-center gap-2 text-gold hover:text-[var(--gold-dark)] transition">
          <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.198-.347.223-.644.075-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.297-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.884 3.488"/>
          </svg>
          <span class="font-semibold"><?= $formatted_number ?></span>
        </a>
      </div>
    </div>
  </div>

  <div id="sidebar-overlay" class="fixed inset-0 bg-black/70 z-40 hidden lg:hidden"></div>

  <!-- Desktop Header -->
  <header class="bg-[var(--bg-dark)]/90 backdrop-blur-md shadow-lg sticky top-0 z-30 border-b border-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
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

          <a href="<?= $wa_link ?>" class="flex items-center gap-2 text-gold hover:text-[var(--gold-dark)] font-semibold transition">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.198-.347.223-.644.075-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.297-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.884 3.488"/>
            </svg>
            <?= $formatted_number ?>
          </a>

          <div class="flex items-center gap-2">
            <span class="text-sm text-[var(--muted)]">Dark Mode</span>
            <label class="theme-toggle">
              <input type="checkbox" id="theme-switch-desktop">
              <span class="slider">
                <svg class="sun w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 6.95a.5.5 0 01-.707 0l-1.414-1.414a.5.5 0 01.707-.707l1.414 1.414a.5.5 0 010 .707zM5.464 5.464a.5.5 0 01-.707 0L3.343 4.05a.5.5 0 11.707-.707l1.414 1.414a.5.5 0 010 .707zm9.072 9.072a.5.5 0 01-.707 0l-1.414-1.414a.5.5 0 11.707-.707l1.414 1.414a.5.5 0 010 .707zM5.464 14.536a.5.5 0 01-.707 0L3.343 13.122a.5.5 0 11.707-.707l1.414 1.414a.5.5 0 010 .707z"/></svg>
                <svg class="moon w-4 h-4 text-gray-700" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8 0 1010.586 10.586z"/></svg>
              </span>
            </label>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Floating WhatsApp Button (appears on all pages) -->
  <a href="<?= $wa_link ?>?text=Hi%20ETTAAJ%20RENT%20CARS!%20I%20want%20to%20rent%20a%20car%20in%20Marrakech." 
     target="_blank"
     class="fixed bottom-6 right-6 bg-green-500 hover:bg-green-600 text-white w-16 h-16 rounded-full shadow-2xl flex items-center justify-center z-50 transition-all hover:scale-110">
    <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 24 24">
      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.198-.347.223-.644.075-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.297-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.884 3.488"/>
    </svg>
  </a>

  <script>
    const sidebar = document.getElementById('mobile-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const openBtn = document.getElementById('open-sidebar');
    const closeBtn = document.getElementById('close-sidebar');

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

    // Theme Toggle Sync
    const html = document.documentElement;
    const mobileToggle = document.getElementById('theme-switch-mobile');
    const desktopToggle = document.getElementById('theme-switch-desktop');

    const applyTheme = (isLight) => {
      if (isLight) html.classList.add('light');
      else html.classList.remove('light');
      if (mobileToggle) mobileToggle.checked = isLight;
      if (desktopToggle) desktopToggle.checked = isLight;
      localStorage.setItem('theme', isLight ? 'light' : 'dark');
    };

    const saved = localStorage.getItem('theme');
    const prefersLight = window.matchMedia('(prefers-color-scheme: light)').matches;
    const isLight = saved === 'light' || (!saved && prefersLight);
    applyTheme(isLight);

    [mobileToggle, desktopToggle].forEach(toggle => {
      if (toggle) toggle.addEventListener('change', () => applyTheme(toggle.checked));
    });
  </script>
</body>
</html>