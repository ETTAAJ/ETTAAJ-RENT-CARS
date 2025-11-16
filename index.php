<?php
  require 'config.php';
  /* -------------------------------------------------
     1. Build Query
     ------------------------------------------------- */
  $search = trim($_GET['search'] ?? '');
  $gear = $_GET['gear'] ?? '';
  $fuel = $_GET['fuel'] ?? '';
  $sort = $_GET['sort'] ?? 'low';
  $where = [];
  $params = [];
  if ($search !== '') {
      $where[] = "name LIKE ?";
      $params[] = "%$search%";
  }
  if ($gear !== '' && in_array($gear, ['Manual', 'Automatic'])) {
      $where[] = "gear = ?";
      $params[] = $gear;
  }
  if ($fuel !== '' && in_array($fuel, ['Diesel', 'Petrol'])) {
      $where[] = "fuel = ?";
      $params[] = $fuel;
  }
  $order = ($sort === 'high') ? 'price_day DESC' : 'price_day ASC';
  $sql = "SELECT * FROM cars";
  if (!empty($where)) {
      $sql .= " WHERE " . implode(' AND ', $where);
  }
  $sql .= " ORDER BY $order";

  /* -------------------------------------------------
     2. renderCarCard() – DARK GLASS + GOLD ACCENTS
     ------------------------------------------------- */
  function renderCarCard($car, $index = 0): string
  {
      $baseImg = !empty($car['image'])
          ? 'uploads/' . basename($car['image'])
          : 'https://via.placeholder.com/600x338/36454F/FFFFFF?text=' . urlencode($car['name']);
      $cacheBuster = '';
      $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $baseImg;
      if (file_exists($fullPath)) {
          $cacheBuster = '?v=' . filemtime($fullPath);
      }
      $imgUrl = $baseImg . $cacheBuster;
      $delay = 100 + ($index % 8) * 80;
      ob_start(); ?>
      <div data-aos="fade-up" data-aos-delay="<?= $delay ?>" data-aos-duration="700"
           class="group relative bg-card/90 backdrop-blur-md rounded-3xl overflow-hidden shadow-2xl hover:shadow-gold/20
                  transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02]
                  border border-border flex flex-col h-full">
          <!-- Image -->
          <div class="relative w-full pt-[56.25%] bg-card-dark overflow-hidden border-b border-border">
              <img src="<?= htmlspecialchars($imgUrl) ?>"
                   alt="<?= htmlspecialchars($car['name']) ?> - ETTAAJ RENT CARS"
                   class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
                   onerror="this.onerror=null; this.src='https://via.placeholder.com/600x338/36454F/FFFFFF?text=No+Image';
                            this.classList.add('object-contain','p-8');">
          </div>
          <!-- Card Body -->
          <div class="px-5 pb-5 sm:px-6 sm:pb-6 flex-1 flex flex-col bg-card">
              <h3 class="text-xl sm:text-2xl font-extrabold text-primary mb-2 text-center line-clamp-1">
                  <?= htmlspecialchars($car['name']) ?>
              </h3>
              <!-- Specs -->
              <div class="flex justify-center gap-6 sm:gap-8 text-muted mb-4 text-xs sm:text-sm">
                  <div class="flex flex-col items-center">
                      <svg class="w-5 h-5 mb-1 text-gold" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                      </svg>
                      <span class="font-medium text-primary"><?= (int)$car['seats'] ?> Seats</span>
                  </div>
                  <div class="flex flex-col items-center">
                      <svg class="w-5 h-5 mb-1 text-gold" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M5 3h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                      </svg>
                      <span class="font-medium text-primary"><?= (int)$car['bags'] ?> Bags</span>
                  </div>
              </div>
              <!-- Gear & Fuel -->
              <div class="flex justify-center gap-4 text-xs text-muted mb-5 font-medium">
                  <span class="px-3 py-1 bg-card-dark rounded-full text-primary border border-border"><?= htmlspecialchars($car['gear']) ?></span>
                  <span class="px-3 py-1 bg-card-dark rounded-full text-primary border border-border"><?= htmlspecialchars($car['fuel']) ?></span>
              </div>
              <!-- Price -->
              <div class="flex flex-col items-center mt-4 mb-3">
                  <div class="flex items-baseline gap-2">
                      <span class="text-4xl sm:text-5xl font-extrabold text-primary">
                          <?= number_format((float)$car['price_day']) ?>
                      </span>
                      <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold text-primary bg-gradient-to-r from-gold to-yellow-500 rounded-full shadow-lg animate-pulse">
                          <span>MAD</span>
                          <span>/day</span>
                      </span>
                  </div>
                  <div class="flex gap-3 mt-3 text-xs font-medium">
                      <span class="px-3 py-1 bg-card-dark rounded-full border border-border text-muted">
                          Week: <strong class="text-primary">MAD<?= number_format((float)$car['price_week']) ?></strong>
                      </span>
                      <span class="px-3 py-1 bg-card-dark rounded-full border border-border text-muted">
                          Month: <strong class="text-primary">MAD<?= number_format((float)$car['price_month']) ?></strong>
                      </span>
                  </div>
              </div>
              <!-- CTA -->
              <div class="mt-auto">
                  <a href="car-detail.php?id=<?= (int)$car['id'] ?>"
                     class="block w-full text-center bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-400
                            text-primary font-bold py-3 px-6 rounded-2xl shadow-lg transition-all duration-300
                            transform hover:scale-105 active:scale-95">
                      View Details
                  </a>
              </div>
          </div>
      </div>
      <?php
      return ob_get_clean();
  }

  /* -------------------------------------------------
     3. AJAX Response
     ------------------------------------------------- */
  if (isset($_GET['ajax']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
      try {
          $stmt = $pdo->prepare($sql);
          $stmt->execute($params);
          $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
          $html = '';
          foreach ($cars as $i => $c) {
              $html .= renderCarCard($c, $i);
          }
          header('Content-Type: application/json; charset=utf-8');
          echo json_encode(['html' => $html, 'count' => count($cars)]);
          exit;
      } catch (Throwable $e) {
          http_response_code(500);
          header('Content-Type: application/json; charset=utf-8');
          echo json_encode([
              'html' => '<p class="col-span-full text-center text-red-400">Server error.</p>',
              'count' => 0
          ]);
          exit;
      }
  }

  /* -------------------------------------------------
     4. Normal Page Load
     ------------------------------------------------- */
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
  <!-- Primary Meta Tags -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ETTAAJ RENT CARS | Premium Car Rental in Casablanca, Morocco</title>
  <meta name="description" content="Rent luxury cars in Casablanca with ETTAAJ RENT CARS. Best prices, 24/7 support, automatic & manual. Book now: +212 772 331 080" />
  <meta name="keywords" content="car rental Casablanca, rent a car Morocco, luxury car hire, ETTAAJ RENT CARS, location voiture Casablanca, location auto Maroc" />
  <meta name="author" content="ETTAAJ RENT CARS" />
  <meta name="robots" content="index, follow" />
  <meta name="language" content="en, ar" />
  <meta name="geo.region" content="MA" />
  <meta name="geo.placename" content="Casablanca" />
  <meta name="geo.position" content="33.5731;-7.5898" />
  <meta name="ICBM" content="33.5731, -7.5898" />

  <!-- Canonical URL -->
  <link rel="canonical" href="https://www.ettaajrentcars.ma<?php echo $_SERVER['REQUEST_URI']; ?>" />

  <!-- FAVICON & LOGO IN BROWSER TAB -->
  <link rel="icon" href="pub_img/GoldCar.png" type="image/png" sizes="512x512">
  <link rel="icon" href="pub_img/favicon.ico" type="image/x-icon">
  <link rel="apple-touch-icon" href="pub_img/GoldCar.png">

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website" />
  <meta property="og:title" content="ETTAAJ RENT CARS | Premium Car Rental in Casablanca" />
  <meta property="og:description" content="Luxury car rental in Morocco. Best rates, 24/7 service. Call +212 772 331 080" />
  <meta property="og:url" content="https://www.ettaajrentcars.ma<?php echo $_SERVER['REQUEST_URI']; ?>" />
  <meta property="og:site_name" content="ETTAAJ RENT CARS" />
  <meta property="og:image" content="https://www.ettaajrentcars.ma/pub_img/og-image.jpg" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:locale" content="en_US" />
  <meta property="og:locale:alternate" content="ar_MA" />

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="ETTAAJ RENT CARS | Car Rental Casablanca" />
  <meta name="twitter:description" content="Premium cars, best prices. Book now: +212 772 331 080" />
  <meta name="twitter:image" content="https://www.ettaajrentcars.ma/pub_img/og-image.jpg" />
  <meta name="twitter:site" content="@ettaajrentcars" />
  <meta name="twitter:creator" content="@ettaajrentcars" />

  <!-- Business Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "ETTAAJ RENT CARS",
    "image": "https://www.ettaajrentcars.ma/pub_img/ettaaj-logo.png",
    "url": "https://www.ettaajrentcars.ma",
    "telephone": "+212772331080",
    "email": "contact@ettaajrentcars.ma",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "123 Boulevard Mohamed V",
      "addressLocality": "Casablanca",
      "addressRegion": "Grand Casablanca",
      "postalCode": "20000",
      "addressCountry": "MA"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": 33.5731,
      "longitude": -7.5898
    },
    "openingHoursSpecification": [
      {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
        "opens": "00:00",
        "closes": "23:59"
      }
    ],
    "priceRange": "MAD 300 - 3000",
    "sameAs": [
      "https://facebook.com/ettaajrentcars",
      "https://instagram.com/ettaajrentcars",
      "https://twitter.com/ettaajrentcars"
    ]
  }
  </script>

  <!-- CSS & Fonts -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { 
            gold: '#FFD700', 
            'gold-dark': '#E6C200'
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <!-- SMOOTH SCROLL -->
  <style>
    html { scroll-behavior: smooth; }
  </style>

  <!-- CSS VARIABLES -->
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
    }
    body { background-color: var(--bg); color: var(--primary); }
    .bg-card { background-color: var(--card); }
    .bg-card-dark { background-color: var(--card-dark); }
    .border-border { border-color: var(--border); }
    .text-primary { color: var(--primary); }
    .text-muted { color: var(--muted); }
    .text-gold { color: var(--gold); }
    .bg-gold { background-color: var(--gold); }
    .border-gold { border-color: var(--gold); }

    .spinner {
        width: 40px; height: 40px; border: 4px solid var(--bg-dark); border-top: 4px solid var(--gold);
        border-radius: 50%; animation: spin 1s linear infinite; margin: 40px auto;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .particle {
        position: absolute; width: 4px; height: 4px; background: var(--gold); border-radius: 50%;
        opacity: 0.6; animation: float 6s infinite ease-in-out;
    }
    .particle:nth-child(1) { top: 20%; left: 15%; animation-delay: 0s; }
    .particle:nth-child(2) { top: 50%; left: 70%; animation-delay: 1s; }
    .particle:nth-child(3) { top: 70%; left: 30%; animation-delay: 2s; }
    .particle:nth-child(4) { top: 30%; left: 80%; animation-delay: 3s; }
    .particle:nth-child(5) { top: 60%; left: 10%; animation-delay: 4s; }
    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }
  </style>
</head>
<body class="min-h-screen">

<?php include 'header.php'; ?>

<!-- HERO SECTION -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-[#1e293b] via-[#36454F] to-[#2C3A44]"
         style="background-image: url('https://images.unsplash.com/photo-1494905998402-395d579af36f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
                background-size: cover; background-position: center;"
         data-aos="fade" data-aos-duration="1500">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="absolute inset-0 pointer-events-none">
        <div class="particle"></div>
        <div class="particle delay-1"></div>
        <div class="particle delay-2"></div>
        <div class="particle delay-3"></div>
        <div class="particle delay-4"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div data-aos="fade-down" data-aos-delay="300" class="mb-6">
            <div class="flex justify-center items-center gap-3 flex-col sm:flex-row">
                <img src="pub_img/GoldCar.png" alt="ETTAAJ RENT CARS Logo" class="w-16 h-16 rounded-full ring-4 ring-gold/50 shadow-2xl">
                <div>
                    <h1 class="text-4xl md:text-6xl font-extrabold bg-gradient-to-r from-gold via-yellow-400 to-gold bg-clip-text text-transparent drop-shadow-2xl">
                        ETTAAJ RENT CARS
                    </h1>
                    <p class="text-gold text-lg font-semibold mt-1 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                        +212 772 331 080
                    </p>
                </div>
            </div>
        </div>
        <h2 data-aos="zoom-in" data-aos-delay="600" data-aos-duration="1000"
            class="text-4xl md:text-6xl lg:text-7xl font-bold text-primary mb-6 leading-tight">
            Rent Your <span class="text-gold animate-pulse">Dream Car</span>
        </h2>
        <p data-aos="fade-up" data-aos-delay="900" 
           class="text-lg md:text-xl text-muted mb-10 max-w-3xl mx-auto leading-relaxed">
            Premium fleet • Flexible plans • <span class="text-gold font-semibold">Golden service in Morocco</span>
        </p>
        <div data-aos="fade-up" data-aos-delay="1200" class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#cars" 
               class="inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-md border-2 border-gold/50 
                      text-gold hover:bg-gold/10 font-bold text-lg py-4 px-10 rounded-full shadow-xl 
                      transform transition-all duration-300 hover:scale-110 hover:shadow-gold/30">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                </svg>
                Book Now
            </a>
            <a href="tel:+212772331080" 
               class="inline-flex items-center justify-center gap-2 bg-gold/20 backdrop-blur-md border-2 border-gold 
                      text-gold hover:bg-gold/30 font-bold text-lg py-4 px-10 rounded-full shadow-xl 
                      transform transition-all duration-300 hover:scale-110">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                Call Now
            </a>
        </div>
        <div data-aos="fade-up" data-aos-delay="1500" class="mt-16">
            <div class="animate-bounce mx-auto w-10 h-14 border-2 border-gold/50 rounded-full flex justify-center">
                <div class="w-1 h-3 bg-gold rounded-full mt-3"></div>
            </div>
            <p class="text-xs text-muted mt-2">Scroll to explore</p>
        </div>
    </div>
</section>

<!-- Filters & Cars -->
<section id="cars" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-bg">
    <div data-aos="fade-up" data-aos-delay="200" data-aos-duration="800"
         class="bg-card-dark p-4 sm:p-6 rounded-xl shadow-lg mb-6 border border-border">
        <form id="filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="text" id="search" placeholder="Search car..."
                   value="<?= htmlspecialchars($search) ?>"
                   class="col-span-1 sm:col-span-2 lg:col-span-1 p-3 bg-card border border-border text-primary placeholder-muted rounded-lg focus:ring-2 focus:ring-gold focus:border-gold text-sm">
            <select id="gear" class="p-3 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
                <option value="" class="bg-card text-primary">All Gears</option>
                <option value="Manual" <?= $gear === 'Manual' ? 'selected' : '' ?>>Manual</option>
                <option value="Automatic" <?= $gear === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
            </select>
            <select id="fuel" class="p-3 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
                <option value="" class="bg-card text-primary">All Fuels</option>
                <option value="Diesel" <?= $fuel === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                <option value="Petrol" <?= $fuel === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
            </select>
            <select id="sort" class="p-3 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
                <option value="low" <?= $sort === 'low' ? 'selected' : '' ?>>Low to High</option>
                <option value="high" <?= $sort === 'high' ? 'selected' : '' ?>>High to Low</option>
            </select>
            <a href="index.php"
               class="col-span-1 sm:col-span-2 lg:col-span-1 bg-border hover:bg-muted/30 text-primary font-medium py-3 px-4 rounded-lg transition text-center text-sm flex items-center justify-center">
                Clear All
            </a>
        </form>
    </div>
    <p id="results-count" class="text-sm text-muted mb-4">
        <?= count($cars) ?> car<?= count($cars) !== 1 ? 's' : '' ?> found
    </p>
    <div id="cars-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($cars as $i => $c): ?>
            <?= renderCarCard($c, $i) ?>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'footer.php'; ?>

<!-- AOS + JS -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 800, easing: 'ease-out-quart' });

    const els = {
        search: document.getElementById('search'),
        gear: document.getElementById('gear'),
        fuel: document.getElementById('fuel'),
        sort: document.getElementById('sort')
    };
    const container = document.getElementById('cars-container');
    const countEl = document.getElementById('results-count');
    let debounceTimer = null;
    let isLoading = false;

    const fetchCars = () => {
        if (isLoading) return;
        isLoading = true;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const params = new URLSearchParams({
                search: els.search.value.trim(),
                gear: els.gear.value,
                fuel: els.fuel.value,
                sort: els.sort.value,
                ajax: 1
            });
            const fallbackHTML = container.innerHTML;
            container.innerHTML = '<div class="col-span-full flex justify-center"><div class="spinner"></div></div>';
            fetch(`index.php?${params}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => { if (!r.ok) throw new Error('Network error'); return r.json(); })
            .then(data => {
                container.innerHTML = data.html || '<p class="col-span-full text-center text-muted">No cars found.</p>';
                countEl.textContent = `${data.count} car${data.count !== 1 ? 's' : ''} found`;
                AOS.refreshHard();
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = fallbackHTML;
                countEl.textContent = `${container.querySelectorAll('[data-aos]').length} car${container.querySelectorAll('[data-aos]').length !== 1 ? 's' : ''} found`;
            })
            .finally(() => { isLoading = false; });
        }, 300);
    };

    els.search.addEventListener('input', fetchCars);
    els.gear.addEventListener('change', fetchCars);
    els.fuel.addEventListener('change', fetchCars);
    els.sort.addEventListener('change', fetchCars);

    document.addEventListener('DOMContentLoaded', () => {
        const hasFilters = ['search', 'gear', 'fuel', 'sort'].some(p => new URLSearchParams(window.location.search).has(p));
        if (hasFilters) {
            countEl.textContent = `${container.children.length} car${container.children.length !== 1 ? 's' : ''} found`;
        }
    });
</script>
</body>
</html>
