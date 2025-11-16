<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gold Cars Rental</title>
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

  <!-- Mobile Sidebar -->
  <div id="mobile-sidebar"
       class="fixed inset-y-0 left-0 w-64 bg-darker-bg/95 backdrop-blur-md shadow-2xl z-50 sidebar closed lg:hidden border-r border-border">
    <div class="p-6">
      <div class="flex justify-between items-center mb-8">
        <!-- Logo + Name in Sidebar -->
        <a href="index.php" class="flex items-center space-x-2">
          <img src="pub_img/GoldCar.png" alt="Gold Cars Logo" class="w-10 h-10 rounded-full ring-2 ring-gold/30" >
          <span class="text-2xl font-bold text-gold">Gold Cars</span>
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
    </div>
  </div>

  <!-- Overlay (click to close) -->
  <div id="sidebar-overlay"
       class="fixed inset-0 bg-black bg-opacity-70 z-40 hidden lg:hidden"></div>

  <!-- Header -->
  <header class="bg-dark-bg/90 backdrop-blur-md shadow-lg sticky top-0 z-30 border-b border-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
      <!-- Logo + "Gold Cars" on ALL sizes (including mobile) -->
      <a href="index.php" class="flex items-center space-x-2">
        <img src="pub_img/GoldCar.png" alt="Gold Cars Logo" class="w-10 h-10 rounded-full ring-2 ring-gold/30" >
        <span class="text-2xl font-bold text-gold">Gold Cars</span>
      </a>

      <!-- Desktop Nav -->
      <nav class="hidden lg:flex space-x-8">
        <a href="index.php" class="text-white hover:text-gold transition">Home</a>
        <a href="index.php#cars" class="text-white hover:text-gold transition">Cars</a>
        <a href="about.php" class="text-white hover:text-gold transition">About</a>
        <a href="contact.php" class="text-white hover:text-gold transition">Contact</a>
      </nav>

      <!-- Mobile Hamburger – PURE WHITE -->
      <button id="open-sidebar" class="lg:hidden text-white hover:text-gold transition">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>
  </header>

  <!-- JavaScript: Sidebar Toggle -->
  <script>
    const sidebar     = document.getElementById('mobile-sidebar');
    const overlay     = document.getElementById('sidebar-overlay');
    const openBtn     = document.getElementById('open-sidebar');
    const closeBtn    = document.getElementById('close-sidebar');

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