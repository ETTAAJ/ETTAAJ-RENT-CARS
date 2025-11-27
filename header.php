<?php 
require_once 'init.php';
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" class="transition-colors duration-300" dir="<?= getDir() ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ETTAAJ RENT CARS - Premium Car Rental in Morocco</title>

  <!-- Favicon -->
  <link rel="icon" href="pub_img/ETTAAJ-RENT-CARS.jpg" type="image/png" sizes="512x512">
  <link rel="icon" href="pub_img/favicon.ico" type="image/x-icon">
  <link rel="apple-touch-icon" href="pub_img/ETTAAJ-RENT-CARS.jpg">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        theme: {
          extend: {
            colors: { 
              gold: '#FFB22C', 
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
      --primary-color: #FFB22C;
      --secondary-color: #000000;
      --therde: #854836;
      --text-color: #F7F7F7;
      --light-bg: #353333;
      --shadow: 0 5px 15px rgba(246, 176, 0, 0.496);
      --bg: var(--light-bg);
      --bg-dark: var(--light-bg);
      --card: var(--light-bg);
      --card-dark: var(--light-bg);
      --border: #4A5A66;
      --primary: var(--text-color);
      --muted: #D1D5DB;
      --gold: var(--primary-color);
      --gold-dark: #E6C200;
      --hover-bg: rgba(255, 178, 44, 0.1);
      --card-dark-gradient: linear-gradient(135deg, #0B0B0C 0%, #121212 55%, var(--therde) 120%);
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
    html[dir="rtl"] .sidebar.closed { transform: translateX(100%); }
    /* Phone number always LTR */
    .phone-number { direction: ltr !important; display: inline-block; unicode-bidi: embed; }

    .nav-link { @apply text-current hover:text-[var(--gold-dark)] transition; }
    .nav-link:hover { background-color: var(--hover-bg); border-radius: 0.5rem; }

    .text-gold { color: var(--gold) !important; }
    .hover\:text-gold-dark:hover { color: var(--gold-dark) !important; }
    .car-card-bg { background: var(--card-dark-gradient); }

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
    .theme-toggle .slider { 
      position: absolute; 
      cursor: pointer; 
      top: 0; 
      left: 0; 
      right: 0; 
      bottom: 0; 
      background-color: #94a3b8; 
      transition: .4s; 
      border-radius: 34px; 
    }
    .theme-toggle .slider:before { 
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
    .theme-toggle input:checked + .slider { background-color: var(--gold); }
    .theme-toggle input:checked + .slider:before { transform: translateX(24px); }
    .theme-toggle .sun, .theme-toggle .moon { position: absolute; top: 3px; width: 20px; height: 20px; transition: opacity .3s; pointer-events: none; }
    .theme-toggle .sun { left: 4px; opacity: 1; }
    .theme-toggle .moon { right: 4px; opacity: 0; }
    .theme-toggle input:checked ~ .sun { opacity: 0; }
    .theme-toggle input:checked ~ .moon { opacity: 1; }

    /* Language Switcher */
    .lang-switcher, .currency-switcher {
      position: relative;
      display: inline-block;
    }
    .lang-dropdown, .currency-dropdown {
      position: absolute;
      top: 100%;
      right: 0;
      margin-top: 0.5rem;
      background: var(--bg-dark);
      border: 1px solid var(--border);
      border-radius: 0.75rem;
      padding: 0.5rem;
      min-width: 120px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.3);
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.3s ease;
      z-index: 1000;
    }
    .lang-switcher:hover .lang-dropdown,
    .lang-switcher.active .lang-dropdown,
    .currency-switcher:hover .currency-dropdown,
    .currency-switcher.active .currency-dropdown {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    .lang-option, .currency-option {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: all 0.2s ease;
      color: var(--primary);
      text-decoration: none;
    }
    .lang-option:hover, .currency-option:hover {
      background: var(--hover-bg);
      color: var(--gold);
    }
    .lang-option.active, .currency-option.active {
      background: rgba(255, 215, 0, 0.15);
      color: var(--gold);
      font-weight: 600;
    }
    .lang-flag {
      width: 24px;
      height: 18px;
      border-radius: 2px;
      object-fit: cover;
    }
    .lang-current, .currency-current {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 0.75rem;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .lang-current:hover, .currency-current:hover {
      background: var(--hover-bg);
    }
    /* RTL Support */
    html[dir="rtl"] {
      direction: rtl;
    }
    html[dir="rtl"] .lang-dropdown,
    html[dir="rtl"] .currency-dropdown {
      right: auto;
      left: 0;
    }
    
    /* Numbers always LTR (Western format) */
    .number, [dir="ltr"] {
      direction: ltr !important;
      unicode-bidi: embed;
    }
    
    /* Phone numbers always LTR */
    .phone-number {
      direction: ltr !important;
      display: inline-block;
      unicode-bidi: embed;
    }
    
    /* RTL Layout Adjustments - Only for navigation and specific elements, NOT cards */
    html[dir="rtl"] nav.flex,
    html[dir="rtl"] .nav-link,
    html[dir="rtl"] header .flex:not(.car-card-bg):not([class*="car"]):not([class*="card"]) {
      flex-direction: row-reverse;
    }
    
    /* Car cards should NOT reverse in RTL */
    html[dir="rtl"] .car-card-bg,
    html[dir="rtl"] [class*="car-card"],
    html[dir="rtl"] .group.relative.car-card-bg {
      direction: ltr !important;
    }
    
    /* Discount badge position for RTL */
    html[dir="rtl"] .discount-badge {
      right: auto;
      left: 16px;
    }
    
    /* RTL Text Alignment */
    html[dir="rtl"] .text-left {
      text-align: right;
    }
    html[dir="rtl"] .text-right {
      text-align: left;
    }
    
    /* RTL Margins and Padding - but not for cards */
    html[dir="rtl"] .ml-auto:not(.car-card-bg *):not([class*="car-card"] *) {
      margin-left: 0;
      margin-right: auto;
    }
    html[dir="rtl"] .mr-auto:not(.car-card-bg *):not([class*="car-card"] *) {
      margin-right: 0;
      margin-left: auto;
    }
    
    /* RTL Transform adjustments */
    html[dir="rtl"] .translate-x-2 {
      transform: translateX(-0.5rem);
    }
    
    /* Ensure car card content stays LTR */
    html[dir="rtl"] .car-card-bg *,
    html[dir="rtl"] [class*="car-card"] * {
      direction: ltr;
    }
    
    /* But allow RTL for text content inside cards */
    html[dir="rtl"] .car-card-bg .text-center,
    html[dir="rtl"] [class*="car-card"] .text-center {
      direction: rtl;
    }
  </style>
</head>
<body class="min-h-screen" dir="<?= getDir() ?>">

  <!-- OFFICIAL NUMBER -->
  <?php 
    $whatsapp_number   = "212772331080";           // Your correct number (no +)
    $formatted_number  = "+212 772 331 080";       // Display format
    $wa_link           = "https://wa.me/$whatsapp_number";
    
    // Language flags
    $langFlags = ['en' => '🇬🇧', 'fr' => '🇫🇷', 'ar' => '🇲🇦'];
    $langNames = ['en' => 'EN', 'fr' => 'FR', 'ar' => 'AR'];
    $currentPage = basename($_SERVER['PHP_SELF']);
    
    // Currency info
    $currencyInfo = getCurrency();
    $currencySymbols = ['MAD' => 'MAD', 'USD' => '$', 'EUR' => '€'];
    $currencyNames = ['MAD' => 'Moroccan Dirham', 'USD' => 'US Dollar', 'EUR' => 'Euro'];
    
    // Preserve current URL parameters (like id, search, etc.)
    $currentParams = $_GET;
    unset($currentParams['lang']); // Remove lang to avoid duplication
    unset($currentParams['currency']); // Remove currency to avoid duplication
  ?>

  <!-- Mobile Sidebar -->
  <div id="mobile-sidebar" class="fixed inset-y-0 <?= $lang === 'ar' ? 'right-0 border-l' : 'left-0 border-r' ?> w-64 bg-[var(--bg-dark)]/95 backdrop-blur-md shadow-2xl z-50 sidebar closed lg:hidden border-border">
    <div class="p-6">
      <div class="flex justify-between items-center mb-8">
        <a href="index.php" class="flex items-center space-x-2">
          <img src="pub_img/ETTAAJ-RENT-CARS.jpg" alt="Logo" class="w-10 h-10 rounded-full ring-2 ring-[var(--gold)]/30">
          <span class="text-xl font-bold bg-gradient-gold">ETTAAJ RENT CARS</span>
        </a>
        <button id="close-sidebar" class="text-current hover:text-[var(--gold-dark)] transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <nav class="space-y-4">
        <a href="<?= langUrl('index.php') ?>" class="nav-link block px-3 py-2 rounded-lg"><?= $text['home'] ?></a>
        <a href="<?= langUrl('index.php') ?>#cars" class="nav-link block px-3 py-2 rounded-lg"><?= $text['cars'] ?></a>
        <a href="<?= langUrl('about.php') ?>" class="nav-link block px-3 py-2 rounded-lg"><?= $text['about'] ?></a>
        <a href="<?= langUrl('rental-guide.php') ?>" class="nav-link block px-3 py-2 rounded-lg"><?= $text['rental_guide'] ?></a>
        <a href="<?= langUrl('contact.php') ?>" class="nav-link block px-3 py-2 rounded-lg"><?= $text['contact'] ?></a>
      </nav>

      <!-- Language Switcher Mobile -->
      <div class="mt-6 pt-4 border-t border-border">
        <div class="lang-switcher w-full">
          <div class="lang-current text-sm text-[var(--muted)]">
            <span><?= $langFlags[$lang] ?> <?= $langNames[$lang] ?></span>
            <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </div>
          <div class="lang-dropdown">
            <a href="?lang=en<?= !empty($currentParams) ? '&' . http_build_query($currentParams) : '' ?>" class="lang-option <?= $lang === 'en' ? 'active' : '' ?>">
              <span><?= $langFlags['en'] ?></span>
              <span>English</span>
            </a>
            <a href="?lang=fr<?= !empty($currentParams) ? '&' . http_build_query($currentParams) : '' ?>" class="lang-option <?= $lang === 'fr' ? 'active' : '' ?>">
              <span><?= $langFlags['fr'] ?></span>
              <span>Français</span>
            </a>
            <a href="?lang=ar<?= !empty($currentParams) ? '&' . http_build_query($currentParams) : '' ?>" class="lang-option <?= $lang === 'ar' ? 'active' : '' ?>">
              <span><?= $langFlags['ar'] ?></span>
              <span>العربية</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Currency Switcher Mobile -->
      <div class="mt-4 pt-4 border-t border-border">
        <div class="currency-switcher w-full">
          <div class="currency-current text-sm text-[var(--muted)]">
            <span><?= $currencySymbols[$currencyInfo['code']] ?> <?= $currencyInfo['code'] ?></span>
            <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </div>
          <div class="currency-dropdown">
            <a href="?currency=MAD<?= !empty($currentParams) ? '&' . http_build_query($currentParams) : '' ?>" class="currency-option <?= $currencyInfo['code'] === 'MAD' ? 'active' : '' ?>">
              <span>MAD</span>
              <span>Moroccan Dirham</span>
            </a>
            <a href="?currency=USD<?= !empty($currentParams) ? '&' . http_build_query($currentParams) : '' ?>" class="currency-option <?= $currencyInfo['code'] === 'USD' ? 'active' : '' ?>">
              <span>$</span>
              <span>US Dollar</span>
            </a>
            <a href="?currency=EUR<?= !empty($currentParams) ? '&' . http_build_query($currentParams) : '' ?>" class="currency-option <?= $currencyInfo['code'] === 'EUR' ? 'active' : '' ?>">
              <span>€</span>
              <span>Euro</span>
            </a>
          </div>
        </div>
      </div>

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
          <span class="font-semibold phone-number" style="direction: ltr; display: inline-block; unicode-bidi: embed;"><?= $formatted_number ?></span>
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
          <img src="pub_img/ETTAAJ-RENT-CARS.jpg" alt="Logo" class="w-10 h-10 rounded-full ring-2 ring-[var(--gold)]/30">
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
          <img src="pub_img/ETTAAJ-RENT-CARS.jpg" alt="Logo" class="w-10 h-10 rounded-full ring-2 ring-[var(--gold)]/30">
          <span class="text-xl sm:text-2xl font-bold bg-gradient-gold">ETTAAJ RENT CARS</span>
        </a>

        <div class="flex items-center space-x-8">
          <nav class="flex items-center space-x-8">
            <a href="<?= langUrl('index.php') ?>" class="nav-link"><?= $text['home'] ?></a>
            
            <!-- Pages Dropdown -->
            <div class="lang-switcher">
              <div class="lang-current cursor-pointer">
                <span class="text-sm"><?= $text['pages'] ?></span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
              <div class="lang-dropdown">
                <a href="<?= langUrl('about.php') ?>" class="lang-option">
                  <span><?= $text['about'] ?></span>
                </a>
                <a href="<?= langUrl('rental-guide.php') ?>" class="lang-option">
                  <span><?= $text['rental_guide'] ?></span>
                </a>
                <a href="<?= langUrl('contact.php') ?>" class="lang-option">
                  <span><?= $text['contact'] ?></span>
                </a>
              </div>
            </div>
          </nav>

          <!-- Currency Switcher Desktop -->
          <div class="currency-switcher">
            <div class="currency-current">
              <span class="text-sm"><?= $currencySymbols[$currencyInfo['code']] ?> <?= $currencyInfo['code'] ?></span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
            <div class="currency-dropdown">
              <a href="?currency=MAD<?= !empty($currentParams) ? '&' . http_build_query($currentParams) : '' ?>" class="currency-option <?= $currencyInfo['code'] === 'MAD' ? 'active' : '' ?>">
                <span>MAD</span>
                <span>Moroccan Dirham</span>
              </a>
              <a href="?currency=USD<?= !empty($currentParams) ? '&' . http_build_query($currentParams) : '' ?>" class="currency-option <?= $currencyInfo['code'] === 'USD' ? 'active' : '' ?>">
                <span>$</span>
                <span>US Dollar</span>
              </a>
              <a href="?currency=EUR<?= !empty($currentParams) ? '&' . http_build_query($currentParams) : '' ?>" class="currency-option <?= $currencyInfo['code'] === 'EUR' ? 'active' : '' ?>">
                <span>€</span>
                <span>Euro</span>
              </a>
            </div>
          </div>

          <!-- Language Switcher Desktop -->
          <div class="lang-switcher">
            <div class="lang-current">
              <span class="text-sm"><?= $langFlags[$lang] ?> <?= $langNames[$lang] ?></span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
            <div class="lang-dropdown">
              <a href="?lang=en<?= !empty($currentParams) ? '&' . http_build_query($currentParams) : '' ?>" class="lang-option <?= $lang === 'en' ? 'active' : '' ?>">
                <span><?= $langFlags['en'] ?></span>
                <span>English</span>
              </a>
              <a href="?lang=fr<?= !empty($currentParams) ? '&' . http_build_query($currentParams) : '' ?>" class="lang-option <?= $lang === 'fr' ? 'active' : '' ?>">
                <span><?= $langFlags['fr'] ?></span>
                <span>Français</span>
              </a>
              <a href="?lang=ar<?= !empty($currentParams) ? '&' . http_build_query($currentParams) : '' ?>" class="lang-option <?= $lang === 'ar' ? 'active' : '' ?>">
                <span><?= $langFlags['ar'] ?></span>
                <span>العربية</span>
              </a>
            </div>
          </div>

          <a href="<?= $wa_link ?>" class="flex items-center gap-2 text-gold hover:text-[var(--gold-dark)] font-semibold transition">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.198-.347.223-.644.075-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.297-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.884 3.488"/>
            </svg>
            <span class="phone-number" style="direction: ltr; display: inline-block; unicode-bidi: embed;"><?= $formatted_number ?></span>
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

    // Language Switcher - Keep dropdown open on click
    document.querySelectorAll('.lang-switcher').forEach(switcher => {
      const current = switcher.querySelector('.lang-current');
      if (current) {
        current.addEventListener('click', (e) => {
          e.stopPropagation();
          switcher.classList.toggle('active');
        });
      }
    });

    // Currency Switcher - Keep dropdown open on click
    document.querySelectorAll('.currency-switcher').forEach(switcher => {
      const current = switcher.querySelector('.currency-current');
      if (current) {
        current.addEventListener('click', (e) => {
          e.stopPropagation();
          switcher.classList.toggle('active');
        });
      }
    });

    // Close language/currency dropdown when clicking outside
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.lang-switcher')) {
        document.querySelectorAll('.lang-switcher').forEach(switcher => {
          switcher.classList.remove('active');
        });
      }
      if (!e.target.closest('.currency-switcher')) {
        document.querySelectorAll('.currency-switcher').forEach(switcher => {
          switcher.classList.remove('active');
        });
      }
    });
  </script>
</body>
</html>