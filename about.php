<?php 
require_once 'init.php';
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= getDir() ?>">
<head>
  <!-- Primary Meta Tags - FULLY OPTIMIZED FOR MARRAKECH -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About ETTAAJ Rent Cars | Best Car Rental Marrakech Airport - No Deposit</title>
  <meta name="description" content="Discover ETTAAJ Rent Cars - Marrakech's most trusted car rental agency. Free airport delivery, no deposit, luxury & cheap cars, 24/7 WhatsApp support. +212 772 331 080" />
  <meta name="keywords" content="about ettaaj rent cars, car rental in marrakech airport, cheap car rental in marrakech, best car rental in marrakech, car rental marrakech no deposit, luxury car rental in marrakech, car rental marrakech gueliz, car rental companies in marrakech, car rental agency marrakech" />
  <meta name="author" content="ETTAAJ Rent Cars" />
  <meta name="robots" content="index, follow" />
  <meta name="language" content="en" />
  <meta name="geo.region" content="MA" />
  <meta name="geo.placename" content="Marrakech" />
  <meta name="geo.position" content="31.6069;-8.0363" />
  <meta name="ICBM" content="31.6069, -8.0363" />

  <!-- Canonical URL -->
  <link rel="canonical" href="https://www.ettaajrentcars.ma/about.php" />

  <!-- Favicon -->
  <link rel="icon" href="/pub_img/favicon.ico" type="image/x-icon" />
  <link rel="apple-touch-icon" sizes="180x180" href="/pub_img/apple-touch-icon.png" />
  <link rel="icon" type="image/png" sizes="32x32" href="/pub_img/favicon-32x32.png" />
  <link rel="icon" type="image/png" sizes="16x16" href="/pub_img/favicon-16x16.png" />

  <!-- Open Graph -->
  <meta property="og:type" content="website" />
  <meta property="og:title" content="About ETTAAJ Rent Cars | Best Car Rental in Marrakech Airport" />
  <meta property="og:description" content="No deposit car rental at Marrakech Airport. Free delivery, luxury & economy cars, 24/7 service. Trusted by thousands of travelers." />
  <meta property="og:url" content="https://www.ettaajrentcars.com/about.php" />
  <meta property="og:site_name" content="ETTAAJ Rent Cars" />
  <meta property="og:image" content="https://www.ettaajrentcars.com/pub_img/ETTAAJ-RENT-CARS.jpg" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="About ETTAAJ Rent Cars - Top Car Rental Marrakech Airport" />
  <meta name="twitter:description" content="Free airport delivery • No deposit • Luxury & cheap cars • WhatsApp +212 772 331 080" />
  <meta name="twitter:image" content="https://www.ettaajrentcars.com/pub_img/ETTAAJ-RENT-CARS.jpg" />

  <!-- Updated Schema - Marrakech Airport Focused -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "CarRentalService",
    "name": "ETTAAJ Rent Cars Marrakech",
    "image": "https://www.ettaajrentcars.ma/pub_img/ETTAAJ-RENT-CARS.jpg",
    "url": "https://www.ettaajrentcars.ma",
    "telephone": "+212772331080",
    "email": "contact@ettaajrentcars.ma",
    "description": "Best car rental in Marrakech Airport with no deposit, free delivery at Menara (RAK), luxury and cheap cars, 24/7 support.",
    "priceRange": "MAD 250 - 5000",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Marrakech Menara Airport (RAK)",
      "addressLocality": "Marrakech",
      "addressRegion": "Marrakech-Safi",
      "postalCode": "40000",
      "addressCountry": "MA"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": 31.6069,
      "longitude": -8.0363
    },
    "openingHoursSpecification": {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
      "opens": "00:00",
      "closes": "23:59"
    },
    "areaServed": {
      "@type": "Place",
      "name": "Marrakech, Gueliz, Menara Airport, Morocco"
    }
  }
  </script>

  <!-- Tailwind + Fonts + AOS + Bootstrap Icons -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: { colors: { gold: '#FFD700' } } }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="format-detection" content="telephone=yes" />
  <meta name="theme-color" content="#36454F" />

  <style>
    :root {
      --bg: #36454F; --bg-dark: #2C3A44; --card: #36454F; --card-dark: #2C3A44;
      --border: #4A5A66; --primary: #FFFFFF; --muted: #D1D5DB; --gold: #FFD700;
      --text-primary: var(--primary); --text-muted: var(--muted);
      --card-dark-gradient: linear-gradient(135deg, #0B0B0C 0%, #121212 55%, #C6A667 120%);
    }
    .light {
      --bg: #f8fafc; --bg-dark: #e2e8f0; --card: #ffffff; --card-dark: #f1f5f9;
      --border: #cbd5e1; --primary: #1e293b; --muted: #64748b; --gold: #d97706;
      --text-primary: var(--primary); --text-muted: var(--muted);
    }
    body { background-color: var(--bg); color: var(--primary); font-family: 'Inter', sans-serif; }
    .bg-card { background-color: var(--card); }
    .bg-card-dark { background-color: var(--card-dark); }
    .car-card-bg { background: var(--card-dark-gradient); }
    .border-border { border-color: var(--border); }
    .text-primary { color: var(--primary); }
    .text-muted { color: var(--muted); }
    .text-gold { color: var(--gold); }
  </style>
</head>
<body class="min-h-screen">

<?php include 'header.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-16 text-center">
  <!-- Hero Section - Now Marrakech Optimized -->
  <section class="relative min-h-[80vh] flex items-center justify-center overflow-hidden rounded-3xl mb-16 bg-gradient-to-br from-card-dark via-[var(--bg)] to-card-dark shadow-2xl"
           style="background-image: url('https://images.unsplash.com/photo-1582719201252-0f1f7d041212?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;"
           data-aos="fade" data-aos-duration="1500">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="absolute inset-0 pointer-events-none">
      <div class="particle"></div>
      <div class="particle delay-1"></div>
      <div class="particle delay-2"></div>
      <div class="particle delay-3"></div>
      <div class="particle delay-4"></div>
    </div>

    <div class="relative z-10 max-w-5xl mx-auto px-4">
      <div data-aos="fade-down" data-aos-delay="400" class="mb-8">
        <div class="flex flex-col items-center gap-4">
          <div class="flex items-center gap-3">
            <img src="pub_img/ETTAAJ-RENT-CARS.jpg" alt="ETTAAJ Rent Cars Marrakech Logo" class="w-16 h-16 rounded-full ring-4 ring-gold/60 shadow-2xl">
            <h1 class="text-5xl md:text-7xl font-extrabold bg-gradient-to-r from-gold via-yellow-400 to-gold bg-clip-text text-transparent drop-shadow-2xl">
              ETTAAJ RENT CARS
            </h1>
          </div>
          <a href="https://wa.me/212772331080?text=Hi%20ETTAAJ%2C%20I%20arrived%20at%20Marrakech%20Airport!" 
             class="flex items-center gap-2 text-gold font-semibold text-lg hover:text-yellow-400 transition">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
            <span class="phone-number" dir="ltr">+212 772 331 080</span> (<?= $text['support_24_7'] ?>)
          </a>
        </div>
      </div>

      <h2 data-aos="zoom-in" data-aos-delay="700" class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
        <?= $text['about_title'] ?>
      </h2>

      <p data-aos="fade-up" data-aos-delay="1000" class="text-lg md:text-xl text-gray-200 mb-10 max-w-3xl mx-auto leading-relaxed">
        <?= $text['about_subtitle'] ?>
      </p>

      <div data-aos="fade-up" data-aos-delay="1300">
        <a href="https://wa.me/212772331080?text=Hello%20ETTAAJ%2C%20I%20need%20a%20car%20at%20Marrakech%20Airport!" 
           target="_blank"
           class="group inline-flex items-center gap-4 bg-green-600 hover:bg-green-700 text-white font-bold text-lg px-10 py-5 rounded-2xl shadow-2xl transform hover:scale-105 transition-all duration-300">
          <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-1.174-2.294-.174-.338-.434-.327-.672-.327-.227 0-.482.074-.735.174-.67.267-1.25.85-1.25 2.076 0 1.226.89 2.407 1.013 2.567.124.16 1.772 2.708 4.293 3.796 1.52.654 2.158.75 2.92.625.76-.124 2.03-.83 2.317-1.632.287-.802.287-1.49.2-1.632-.087-.15-.346-.25-.644-.3z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.132.547 4.135 1.507 5.987L0 24l6.2-1.625C8.002 23.227 9.973 23.773 12 23.773c6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
          <?= $text['book_on_whatsapp'] ?>
        </a>
      </div>
    </div>
  </section>

  <!-- Mission & Vision -->
  <section id="mission" class="grid md:grid-cols-2 gap-8 mb-16">
    <!-- Our Mission - FIXED ICON -->
    <div data-aos="fade-right" class="bg-card/90 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-border hover:shadow-gold/20 transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gradient-to-br from-gold to-yellow-500 rounded-full mx-auto mb-6 flex items-center justify-center shadow-lg animate-pulse">
        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" 
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
      </div>
      <h3 class="font-bold text-2xl mb-4 text-primary"><?= $text['our_mission'] ?></h3>
      <p class="text-muted leading-relaxed">
        <?= $text['mission_desc'] ?>
      </p>
    </div>

    <div data-aos="fade-left" class="bg-card/90 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-border hover:shadow-gold/20 transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gradient-to-br from-gold to-yellow-500 rounded-full mx-auto mb-6 flex items-center justify-center shadow-lg animate-pulse">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
      </div>
      <h3 class="font-bold text-2xl mb-4 text-primary"><?= $text['our_vision'] ?></h3>
      <p class="text-muted leading-relaxed">
        <?= $text['vision_desc'] ?>
      </p>
    </div>
  </section>

  <!-- Values Grid -->
  <section class="grid md:grid-cols-3 gap-8 mb-16">
    <div data-aos="zoom-in" class="bg-card/90 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-border hover:shadow-gold/20 transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gradient-to-br from-gold to-yellow-500 rounded-full mx-auto mb-4 flex items-center justify-center shadow-lg animate-pulse">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
      </div>
      <h3 class="font-bold text-xl mb-2 text-primary"><?= $text['no_hidden_fees'] ?></h3>
      <p class="text-muted"><?= $text['no_hidden_fees_desc'] ?></p>
    </div>

    <div data-aos="zoom-in" data-aos-delay="200" class="bg-card/90 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-border hover:shadow-gold/20 transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gradient-to-br from-gold to-yellow-500 rounded-full mx-auto mb-4 flex items-center justify-center shadow-lg animate-pulse">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <h3 class="font-bold text-xl mb-2 text-primary"><?= $text['support_24_7'] ?></h3>
      <p class="text-muted"><?= $text['support_24_7_desc'] ?></p>
    </div>

    <div data-aos="zoom-in" data-aos-delay="400" class="bg-card/90 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-border hover:shadow-gold/20 transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gradient-to-br from-gold to-yellow-500 rounded-full mx-auto mb-4 flex items-center justify-center shadow-lg animate-pulse">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
      </div>
      <h3 class="font-bold text-xl mb-2 text-primary"><?= $text['fastest_delivery'] ?></h3>
      <p class="text-muted"><?= $text['fastest_delivery_desc'] ?></p>
    </div>
  </section>

  <!-- Final CTA -->
  <div data-aos="fade-up" class="mt-16">
    <a href="<?= langUrl('index.php') ?>" class="inline-flex items-center gap-3 bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-400 text-black font-bold text-lg py-5 px-12 rounded-full shadow-2xl transform transition-all duration-300 hover:scale-110">
      <?= $text['view_cars_airport'] ?>
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
    </a>
  </div>
</main>

<?php include 'footer.php'; ?>

<!-- AOS + Scripts (unchanged) -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once: true, duration: 1000, easing: 'ease-out-quart' });
  const observer = new MutationObserver(() => AOS.refreshHard());
  observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
</script>

<!-- Original Styles (100% unchanged) -->
<style>
  .particle {
    position: absolute; width: 4px; height: 4px; background: #FFD700; border-radius: 50%;
    opacity: 0.6; animation: float 6s infinite ease-in-out;
  }
  .particle:nth-child(1) { top: 20%; left: 15%; animation-delay: 0s; }
  .particle:nth-child(2) { top: 60%; left: 80%; animation-delay: 1s; width: 6px; height: 6px; }
  .particle:nth-child(3) { top: 40%; left: 50%; animation-delay: 2s; }
  .particle:nth-child(4) { top: 80%; left: 30%; animation-delay: 3s; width: 5px; height: 5px; }
  .particle:nth-child(5) { top: 30%; left: 70%; animation-delay: 4s; }
  @keyframes float { 0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.6; } 50% { transform: translateY(-20px) rotate(180deg); opacity: 1; } }
  .animate-pulse { animation: pulse 2s infinite; }
  @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
</style>
</body>
</html>