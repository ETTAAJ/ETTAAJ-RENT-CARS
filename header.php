<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ETTAAJ RENT CARS - Premium Car Rental in Morocco</title>
  <!-- Favicon (Browser Tab Icon) -->
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
            'dark-bg': '#36454F',
            'darker-bg': '#2C3A44',
            'border': '#4A5A66'
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { 
      font-family: 'Inter', sans-serif; 
      background-color: #36454F; 
      color: #FFFFFF;
      scroll-behavior: smooth; 
    }
    .sidebar {
      transition: transform 0.3s ease-in-out;
    }
    .sidebar.open  { transform: translateX(0); }
    .sidebar.closed { transform: translateX(-100%); }
    .hover\:text-gold:hover { color: #FFD700 !important; }
    .hover\:bg-gold\/10:hover { background-color: rgba(255, 215, 0, 0.1); }
  </style>
</head>
<body class="bg-dark-bg text-white min-h-screen">

  <!-- Mobile Sidebar (unchanged) -->
  <div id="mobile-sidebar"
       class="fixed inset-y-0 left-0 w-64 bg-darker-bg/95 backdrop-blur-md shadow-2xl z-50 sidebar closed lg:hidden border-r border-border">
    <div class="p-6">
      <div class="flex justify-between items-center mb-8">
        <a href="index.php" class="flex items-center space-x-2">
          <img src="pub_img/GoldCar.png" alt="ETTAAJ RENT CARS Logo" class="w-10 h-10 rounded-full ring-2 ring-gold/30">
          <span class="text-xl font-bold text-gold">ETTAAJ RENT CARS</span>
        </a>
        <button id="close-sidebar" class="text-white hover:text-gold transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <nav class="space-y-4">
        <a href="index.php" class="block text-white hover:text-gold hover:bg-gold/10 px-3 py-2 rounded-lg transition">Home</a>
        <a href="index.php#cars" class="block text-white hover:text-gold hover:bg-gold/10 px-3 py-2 rounded-lg transition">Cars</a>
        <a href="about.php" class="block text-white hover:text-gold hover:bg-gold/10 px-3 py-2 rounded-lg transition">About</a>
        <a href="contact.php" class="block text-white hover:text-gold hover:bg-gold/10 px-3 py-2 rounded-lg transition">Contact</a>
      </nav>
      <div class="mt-8 pt-6 border-t border-border">
        <a href="tel:+212772331080" class="flex items-center gap-2 text-gold hover:text-gold-dark transition">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
          </svg>
          <span class="font-semibold">+212 772 331 080</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Overlay -->
  <div id="sidebar-overlay"
       class="fixed inset-0 bg-black bg-opacity-70 z-40 hidden lg:hidden"></div>

  <!-- ======================== NEW HEADER ======================== -->
  <header class="bg-dark-bg/90 backdrop-blur-md shadow-lg sticky top-0 z-30 border-b border-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

      <!-- Mobile + Tablet: Logo + Hamburger (side-by-side) -->
      <div class="flex items-center justify-between lg:hidden">
        <!-- Logo + Name -->
        <a href="index.php" class="flex items-center space-x-2">
          <img src="pub_img/GoldCar.png" alt="ETTAAJ RENT CARS Logo"
               class="w-10 h-10 rounded-full ring-2 ring-gold/30">
          <span class="text-xl sm:text-2xl font-bold text-gold">ETTAAJ RENT CARS</span>
        </a>

        <!-- Hamburger (right next to name) -->
        <button id="open-sidebar" class="text-white hover:text-gold transition">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>

      <!-- Desktop: Logo + Nav + Phone (unchanged) -->
      <div class="hidden lg:flex items-center justify-between">
        <!-- Logo + Name -->
        <a href="index.php" class="flex items-center space-x-2">
          <img src="pub_img/GoldCar.png" alt="ETTAAJ RENT CARS Logo"
               class="w-10 h-10 rounded-full ring-2 ring-gold/30">
          <span class="text-xl sm:text-2xl font-bold text-gold">ETTAAJ RENT CARS</span>
        </a>

        <!-- Nav + Phone -->
        <div class="flex items-center space-x-8">
          <nav class="flex space-x-8">
            <a href="index.php" class="text-white hover:text-gold transition">Home</a>
            <a href="index.php#cars" class="text-white hover:text-gold transition">Cars</a>
            <a href="about.php" class="text-white hover:text-gold transition">About</a>
            <a href="contact.php" class="text-white hover:text-gold transition">Contact</a>
          </nav>
          <a href="tel:+212772331080"
             class="flex items-center gap-2 text-gold hover:text-gold-dark font-semibold transition">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
            </svg>
            +212 772 331 080
          </a>
        </div>
      </div>

    </div>
  </header>
  <!-- ====================== END NEW HEADER ====================== -->

  <!-- Sidebar toggle script (unchanged) -->
  <script>
    const sidebar   = document.getElementById('mobile-sidebar');
    const overlay   = document.getElementById('sidebar-overlay');
    const openBtn   = document.getElementById('open-sidebar');
    const closeBtn  = document.getElementById('close-sidebar');

    openBtn.addEventListener('click', () => {
      sidebar.classList.replace('closed', 'open');
      overlay.classList.remove('hidden');
    });

    const closeSidebar = () => {
      sidebar.classList.replace('open', 'closed');
      overlay.classList.add('hidden');
    };

    closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);
  </script>

  <!-- Rest of your page content goes here -->