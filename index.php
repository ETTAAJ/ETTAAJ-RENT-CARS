<?php
  require 'config.php';
  /* -------------------------------------------------
     1. Build Query - UNCHANGED
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
     2. renderCarCard() – ONLY IMAGE FIX (unchanged)
     ------------------------------------------------- */
  function renderCarCard($car, $index = 0): string
  {
      $imgUrl = 'https://via.placeholder.com/600x338/36454F/FFFFFF?text=' . urlencode($car['name']);

      if (!empty($car['image']) && is_string($car['image'])) {
          $filename   = basename($car['image']);
          $relative   = 'uploads/' . $filename;
          $fullPath   = $_SERVER['DOCUMENT_ROOT'] . '/' . $relative;

          if (file_exists($fullPath)) {
              $imgUrl = $relative . '?v=' . filemtime($fullPath);
          } else {
              $imgUrl = $relative;
          }
      }

      $delay = 100 + ($index % 8) * 80;
      ob_start(); ?>
      <div data-aos="fade-up" data-aos-delay="<?= $delay ?>" data-aos-duration="700"
           class="group relative bg-card/90 backdrop-blur-md rounded-3xl overflow-hidden shadow-2xl hover:shadow-gold/20
                  transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02]
                  border border-border flex flex-col h-full">
          <div class="relative w-full pt-[56.25%] bg-card-dark overflow-hidden border-b border-border">
              <img src="<?= htmlspecialchars($imgUrl, ENT_QUOTES) ?>"
                   alt="<?= htmlspecialchars($car['name']) ?> - Car Rental Marrakech Airport | ETTAAJ Rent Cars"
                   class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
                   onerror="this.onerror=null; this.src='https://via.placeholder.com/600x338/36454F/FFFFFF?text=No+Image';
                            this.classList.add('object-contain','p-8');">
          </div>
          <div class="px-5 pb-5 sm:px-6 sm:pb-6 flex-1 flex flex-col bg-card">
              <h3 class="text-xl sm:text-2xl font-extrabold text-primary mb-2 text-center line-clamp-1">
                  <?= htmlspecialchars($car['name']) ?>
              </h3>
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
              <div class="flex justify-center gap-4 text-xs text-muted mb-5 font-medium">
                  <span class="px-3 py-1 bg-card-dark rounded-full text-primary border border-border"><?= htmlspecialchars($car['gear']) ?></span>
                  <span class="px-3 py-1 bg-card-dark rounded-full text-primary border border-border"><?= htmlspecialchars($car['fuel']) ?></span>
              </div>
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
     3. AJAX Response - UNCHANGED
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
     4. Normal Page Load - UNCHANGED
     ------------------------------------------------- */
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
  <!-- Primary Meta Tags - FULLY OPTIMIZED FOR MARRAKECH -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Car Rental Marrakech Airport | ETTAAJ Rent Cars – No Deposit, Cheap & Luxury</title>
  <meta name="description" content="Best car rental in Marrakech Airport (RAK). Cheap rates from 250 MAD, no deposit, luxury cars, free airport delivery 24/7. Instant WhatsApp booking: +212 772 331 080" />
  <meta name="keywords" content="car rental in marrakech airport, cheap car rental in marrakech, best car rental in marrakech, car rental in marrakech without deposit, car rental marrakech no deposit, luxury car rental in marrakech, car rental marrakech gueliz, car rental companies in marrakech, car rental agency marrakech, car rental marrakech to fes, euro car rental in marrakech, car rental in marrakech reddit" />
  <meta name="author" content="ETTAAJ Rent Cars" />
  <meta name="robots" content="index, follow" />
  <meta name="language" content="en" />
  <meta name="geo.region" content="MA" />
  <meta name="geo.placename" content="Marrakech" />
  <meta name="geo.position" content="31.6069;-8.0363" />
  <meta name="ICBM" content="31.6069, -8.0363" />

  <!-- Canonical URL -->
  <link rel="canonical" href="https://www.ettaajrentcars.ma<?php echo $_SERVER['REQUEST_URI']; ?>" />

  <!-- FAVICON -->
  <link rel="icon" href="pub_img/GoldCar.png" type="image/png" sizes="512x512">
  <link rel="icon" href="pub_img/favicon.ico" type="image/x-icon">
  <link rel="apple-touch-icon" href="pub_img/GoldCar.png">

  <!-- Open Graph -->
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Car Rental Marrakech Airport | ETTAAJ Rent Cars – No Deposit & Luxury" />
  <meta property="og:description" content="Cheap car rental in Marrakech Airport from 250 MAD/day. No deposit, free delivery, 24/7 support. Book via WhatsApp +212 772 331 080" />
  <meta property="og:url" content="https://www.ettaajrentcars.ma<?php echo $_SERVER['REQUEST_URI']; ?>" />
  <meta property="og:site_name" content="ETTAAJ Rent Cars" />
  <meta property="og:image" content="https://www.ettaajrentcars.ma/pub_img/og-marrakech.jpg" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Best Car Rental in Marrakech Airport – ETTAAJ Rent Cars" />
  <meta name="twitter:description" content="No deposit, cheap & luxury cars. Free airport delivery. WhatsApp +212 772 331 080" />
  <meta name="twitter:image" content="https://www.ettaajrentcars.ma/pub_img/og-marrakech.jpg" />

  <!-- Marrakech Airport Business Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "CarRentalService",
    "name": "ETTAAJ Rent Cars - Car Rental Marrakech Airport",
    "image": "https://www.ettaajrentcars.ma/pub_img/ettaaj-logo.png",
    "url": "https://www.ettaajrentcars.ma",
    "telephone": "+212772331080",
    "priceRange": "MAD 250 - 5000",
    "description": "Best car rental in Marrakech Airport with no deposit, free delivery, cheap and luxury cars available 24/7",
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
      "name": "Marrakech, Gueliz, Menara Airport"
    }
  }
  </script>

  <!-- CSS & Fonts (unchanged) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: { colors: { gold: '#FFD700', 'gold-dark': '#E6C200' } } }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <style>
    html { scroll-behavior: smooth; }
    :root {
      --bg: #36454F; --bg-dark: #2C3A44; --card: #36454F; --card-dark: #2C3A44;
      --border: #4A5A66; --primary: #FFFFFF; --muted: #D1D5DB; --gold: #FFD700;
    }
    .light { --bg: #f8fafc; --bg-dark: #e2e8f0; --card: #ffffff; --card-dark: #f1f5f9;
      --border: #cbd5e1; --primary: #1e293b; --muted: #64748b; --gold: #d97706; }
    body { background-color: var(--bg); color: var(--primary); }
    .bg-card { background-color: var(--card); }
    .bg-card-dark { background-color: var(--card-dark); }
    .border-border { border-color: var(--border); }
    .text-primary { color: var(--primary); }
    .text-muted { color: var(--muted); }
    .text-gold { color: var(--gold); }
    .spinner { width: 40px; height: 40px; border: 4px solid var(--bg-dark); border-top: 4px solid var(--gold);
      border-radius: 50%; animation: spin 1s linear infinite; margin: 40px auto; }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>
<body class="min-h-screen">

<?php include 'header.php'; ?>

<!-- HERO SECTION - FULLY OPTIMIZED FOR MARRAKECH -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-[#1e293b] via-[#36454F] to-[#2C3A44]"
         style="background-image: url('https://images.unsplash.com/photo-1599641954754-624d7f14f7e4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-black/70"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div data-aos="fade-down" data-aos-delay="300" class="mb-6">
            <img src="pub_img/GoldCar.png" alt="ETTAAJ Rent Cars Marrakech" class="w-16 h-16 mx-auto rounded-full ring-4 ring-gold/50 shadow-2xl">
            <h1 class="text-4xl md:text-6xl font-extrabold bg-gradient-to-r from-gold via-yellow-400 to-gold bg-clip-text text-transparent drop-shadow-2xl">
                ETTAAJ RENT CARS MARRAKECH
            </h1>
            <p class="text-gold text-lg font-semibold mt-2">
                <a href="https://wa.me/212772331080?text=Hi%20ETTAAJ%2C%20I%20just%20landed%20at%20Marrakech%20Airport!" 
                   class="hover:underline">+212 772 331 080 (WhatsApp 24/7)</a>
            </p>
        </div>
        <h2 data-aos="zoom-in" data-aos-delay="600" class="text-4xl md:text-6xl lg:text-7xl font-bold text-primary mb-6">
            Car Rental Marrakech Airport<br>
            <span class="text-gold animate-pulse">No Deposit • From 250 MAD/day</span>
        </h2>
        <p data-aos="fade-up" data-aos-delay="900" class="text-lg md:text-xl text-muted mb-10 max-w-4xl mx-auto leading-relaxed">
            Best car rental in Marrakech Airport • Free delivery at Menara (RAK) • Cheap, luxury & no deposit options • Instant booking
        </p>
        <div data-aos="fade-up" data-aos-delay="1200" class="flex flex-col sm:flex-row gap-5 justify-center">
            <a href="https://wa.me/212772331080?text=Hello%20ETTAAJ%2C%20I%20need%20a%20car%20at%20Marrakech%20Airport!" 
               class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold text-lg py-5 px-12 rounded-full shadow-2xl transform hover:scale-110 transition">
                Book via WhatsApp Now
            </a>
            <a href="#cars" class="bg-white/10 backdrop-blur border-2 border-gold text-gold hover:bg-gold/20 font-bold text-lg py-5 px-12 rounded-full shadow-xl transform hover:scale-110 transition">
                View All Cars
            </a>
        </div>
    </div>
</section>

<!-- Filters & Cars Section (unchanged logic) -->
<section id="cars" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 bg-bg">
    <div class="text-center mb-12">
        <h2 class="text-4xl font-bold text-primary mb-4">Best Car Rental in Marrakech – Choose Your Vehicle</h2>
        <p class="text-xl text-muted">Cheap car rental in Marrakech • Luxury car rental • No deposit • Gueliz & airport delivery</p>
    </div>
    <!-- Your existing filter form (100% unchanged) -->
    <div data-aos="fade-up" class="bg-card-dark p-6 rounded-xl shadow-lg mb-8 border border-border">
        <form id="filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <input type="text" id="search" placeholder="Search car (e.g. Dacia, Range Rover)..." value="<?= htmlspecialchars($search) ?>" class="p-4 bg-card border border-border text-primary placeholder-muted rounded-lg focus:ring-2 focus:ring-gold text-sm">
            <select id="gear" class="p-4 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
                <option value="">All Transmission</option>
                <option value="Manual" <?= $gear === 'Manual' ? 'selected' : '' ?>>Manual</option>
                <option value="Automatic" <?= $gear === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
            </select>
            <select id="fuel" class="p-4 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
                <option value="">All Fuel</option>
                <option value="Diesel" <?= $fuel === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                <option value="Petrol" <?= $fuel === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
            </select>
            <select id="sort" class="p-4 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
                <option value="low" <?= $sort === 'low' ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="high" <?= $sort === 'high' ? 'selected' : '' ?>>Price: High to Low</option>
            </select>
            <a href="?" class="bg-gold/20 hover:bg-gold/30 text-gold font-bold py-4 px-6 rounded-lg text-center">Clear Filters</a>
        </form>
    </div>
    <p id="results-count" class="text-center text-muted text-lg mb-8"><?= count($cars) ?> vehicles available at Marrakech Airport</p>
    <div id="cars-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <?php foreach ($cars as $i => $c): ?>
            <?= renderCarCard($c, $i) ?>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'footer.php'; ?>

<!-- Scripts (100% unchanged) -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 800, easing: 'ease-out-quart' });
    // Your existing AJAX filter script (exactly the same)
    const els = { search: document.getElementById('search'), gear: document.getElementById('gear'), fuel: document.getElementById('fuel'), sort: document.getElementById('sort') };
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
            container.innerHTML = '<div class="col-span-full flex justify-center"><div class="spinner"></div></div>';
            fetch(`?${params}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(data => {
                container.innerHTML = data.html || '<p class="col-span-full text-center text-muted">No cars found.</p>';
                countEl.textContent = `${data.count} vehicles available at Marrakech Airport`;
                AOS.refreshHard();
            })
            .catch(() => container.innerHTML = '<p class="col-span-full text-center text-red-400">Error loading cars.</p>')
            .finally(() => isLoading = false);
        }, 300);
    };

    els.search.addEventListener('input', fetchCars);
    els.gear.addEventListener('change', fetchCars);
    els.fuel.addEventListener('change', fetchCars);
    els.sort.addEventListener('change', fetchCars);
</script>
</body>
</html>