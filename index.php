<?php
  require_once 'init.php';
  require_once 'config.php';

  // ================================================
  // 1. Build Query
  // ================================================
  $search = trim($_GET['search'] ?? '');
  $gear   = $_GET['gear'] ?? '';
  $fuel   = $_GET['fuel'] ?? '';
  $sort   = $_GET['sort'] ?? 'low';

  $where  = [];
  $params = [];

  if ($search !== '') {
      $where[]  = "name LIKE ?";
      $params[] = "%$search%";
  }
  // Map translated gear/fuel values to database values
  $gearMap = [$text['manual'] => 'Manual', $text['automatic'] => 'Automatic'];
  $fuelMap = [$text['diesel'] => 'Diesel', $text['petrol'] => 'Petrol'];
  
  if ($gear !== '' && in_array($gear, ['Manual', 'Automatic'])) {
      $where[]  = "gear = ?";
      $params[] = $gear;
  }
  if ($fuel !== '' && in_array($fuel, ['Diesel', 'Petrol'])) {
      $where[]  = "fuel = ?";
      $params[] = $fuel;
  }

  $order = ($sort === 'high') ? 'price_day DESC' : 'price_day ASC';
  $sql   = "SELECT * FROM cars";
  if (!empty($where)) {
      $sql .= " WHERE " . implode(' AND ', $where);
  }
  $sql .= " ORDER BY $order";

  // ================================================
  // 2. Render Car Card Function — UPDATED
  // ================================================
  function renderCarCard($car, $index = 0): string
  {
      global $text;
      $imgUrl = 'https://via.placeholder.com/600x338/36454F/FFFFFF?text=' . urlencode($car['name']);

      if (!empty($car['image']) && is_string($car['image'])) {
          $filename = basename($car['image']);
          $relative = 'uploads/' . $filename;
          $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $relative;

          if (file_exists($fullPath)) {
              $imgUrl = $relative . '?v=' . filemtime($fullPath);
          } else {
              $imgUrl = $relative;
          }
      }

      $delay = 100 + ($index % 8) * 80;
      $discount = (int)($car['discount'] ?? 0);
      $originalPrice = (float)$car['price_day'];
      $discountedPrice = $discount > 0 ? $originalPrice * (1 - $discount / 100) : $originalPrice;
      $hasDiscount = $discount > 0;

      ob_start(); ?>
      <div data-aos="fade-up" data-aos-delay="<?= $delay ?>" data-aos-duration="700"
           class="group relative car-card-bg backdrop-blur-md rounded-3xl overflow-hidden shadow-2xl hover:shadow-gold/20
                  transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02]
                  border border-border flex flex-col h-full opacity-100" style="direction: ltr; visibility: visible;">

          <!-- Car Image -->
          <div class="relative w-full pt-[56.25%] car-card-bg overflow-hidden border-b border-border">
              <img src="<?= htmlspecialchars($imgUrl, ENT_QUOTES) ?>"
                   alt="<?= htmlspecialchars($car['name']) ?> - ETTAAJ Rent Cars Marrakech"
                   class="car-image-clear absolute inset-0 w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
                   loading="lazy"
                   decoding="async"
                   style="opacity: 1; visibility: visible; display: block;"
                   onerror="this.onerror=null;this.src='https://via.placeholder.com/600x338/36454F/FFFFFF?text=No+Image';this.classList.add('object-contain','p-8');this.style.opacity='1';this.style.visibility='visible';this.style.display='block';">

              <!-- Discount Badge (Top Right) -->
              <?php if ($hasDiscount): ?>
              <div class="absolute top-3 right-3 z-10 bg-green-600 text-white font-bold text-xs px-3 py-1.5 rounded-full shadow-lg animate-pulse" style="direction: ltr;">
                -<?= $discount ?>%
              </div>
              <?php endif; ?>
          </div>

          <div class="px-5 pb-5 sm:px-6 sm:pb-6 flex-1 flex flex-col car-card-bg">
              <h3 class="text-xl sm:text-2xl font-extrabold text-white mb-2 text-center line-clamp-1">
                  <?= htmlspecialchars($car['name']) ?>
              </h3>

              <!-- Seats & Bags (Fixed Icons) -->
              <div class="flex justify-center gap-6 sm:gap-8 text-muted mb-4 text-xs sm:text-sm">
                  <div class="flex flex-col items-center">
                      <i class="bi bi-person-fill w-5 h-5 mb-1 text-gold"></i>
                      <span class="font-medium text-white"><?= (int)$car['seats'] ?> <?= $text['seats'] ?></span>
                  </div>
                  <div class="flex flex-col items-center">
                      <i class="bi bi-briefcase-fill w-5 h-5 mb-1 text-gold"></i>
                      <span class="font-medium text-white"><?= (int)$car['bags'] ?> <?= $text['bags'] ?></span>
                  </div>
              </div>

              <!-- Gear & Fuel -->
              <div class="flex justify-center gap-4 text-xs text-muted mb-5 font-medium" style="direction: ltr;">
                  <span class="px-3 py-1 bg-card-dark rounded-full text-primary border border-border"><?= $car['gear'] === 'Manual' ? $text['manual'] : ($car['gear'] === 'Automatic' ? $text['automatic'] : htmlspecialchars($car['gear'])) ?></span>
                  <span class="px-3 py-1 bg-card-dark rounded-full text-primary border border-border"><?= $car['fuel'] === 'Diesel' ? $text['diesel'] : ($car['fuel'] === 'Petrol' ? $text['petrol'] : htmlspecialchars($car['fuel'])) ?></span>
              </div>

              <!-- Price Section -->
              <div class="flex flex-col items-center mt-4 mb-3">
                  <div class="flex items-center justify-center gap-3 flex-wrap">
                      <?php if ($hasDiscount): ?>
                        <span class="text-2xl text-muted line-through opacity-70" dir="ltr">
                          MAD <?= formatNumber($originalPrice) ?>
                        </span>
                      <?php endif; ?>

                      <div class="text-4xl sm:text-5xl font-extrabold <?= $hasDiscount ? 'text-green-400' : 'text-white' ?>" dir="ltr">
                        <?= formatNumber($discountedPrice) ?>
                      </div>
                  </div>
                  <span class="inline-block px-4 py-2 bg-gradient-to-r from-gold to-yellow-500 text-black font-bold rounded-full text-sm mt-2">
                    <span dir="ltr">MAD</span>/<?= $text['day'] ?>
                  </span>

                  <div class="flex gap-3 mt-3 text-xs font-medium">
                      <span class="px-3 py-1 bg-card-dark rounded-full border border-border text-muted">
                          <?= $text['week'] ?>: <strong class="text-primary" dir="ltr">MAD<?= formatNumber((float)$car['price_week']) ?></strong>
                      </span>
                      <span class="px-3 py-1 bg-card-dark rounded-full border border-border text-muted">
                          <?= $text['month'] ?>: <strong class="text-primary" dir="ltr">MAD<?= formatNumber((float)$car['price_month']) ?></strong>
                      </span>
                  </div>
              </div>

              <!-- View Details Button -->
              <div class="mt-auto">
                  <a href="<?= langUrl('car-detail.php', ['id' => (int)$car['id']]) ?>"
                     class="block w-full text-center bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-400
                            text-black font-bold py-3 px-6 rounded-2xl shadow-lg transition-all duration-300
                            transform hover:scale-105 active:scale-95">
                      <?= $text['view_details'] ?>
                  </a>
              </div>
          </div>
      </div>
      <?php
      return ob_get_clean();
  }

  // ================================================
  // 3. AJAX Response
  // ================================================
  if (isset($_GET['ajax']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
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
  }

  // ================================================
  // 4. Normal Page Load
  // ================================================
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" class="scroll-smooth" dir="<?= getDir() ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Car Rental Marrakech Airport | ETTAAJ Rent Cars – No Deposit, From 250 MAD/day</title>
  <meta name="description" content="Best car rental Marrakech Airport. No deposit, free delivery 24/7, luxury & cheap cars. WhatsApp +212 772 331 080">
  <link rel="canonical" href="https://www.ettaajrentcars.ma<?php echo $_SERVER['REQUEST_URI']; ?>">
  <link rel="icon" href="pub_img/ETTAAJ-RENT-CARS.jpg">

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

    .spinner { width: 40px; height: 40px; border: 4px solid var(--bg-dark); border-top: 4px solid var(--gold); border-radius: 50%; animation: spin 1s linear infinite; margin: 40px auto; }
    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes spin-slow { from { transform: translate(-50%,-50%) rotate(0deg); } to { transform: translate(-50%,-50%) rotate(360deg); } }
    .animate-spin-slow { animation: spin-slow 30s linear infinite; }

    /* Image Clarity */
    .car-image-clear {
      image-rendering: -webkit-optimize-contrast;
      image-rendering: crisp-edges;
      image-rendering: high-quality;
      -webkit-backface-visibility: visible;
      backface-visibility: visible;
      transform: translateZ(0);
      will-change: transform;
      filter: brightness(1.05) contrast(1.05);
      opacity: 1 !important;
      visibility: visible !important;
      display: block !important;
    }
    .car-image-clear:hover {
      filter: brightness(1.1) contrast(1.1);
      opacity: 1 !important;
    }

    /* Brand Logos Slider */
    .brand-slider-container {
      position: relative;
      overflow: hidden;
      mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
      -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
    }
    .brand-slider {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      padding: 2rem 0;
      animation: scroll 40s linear infinite;
      width: fit-content;
    }
    .brand-slider:hover {
      animation-play-state: paused;
    }
    @keyframes scroll {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }
    .brand-slider-wrapper {
      display: flex;
      align-items: center;
      gap: 3rem;
      flex-shrink: 0;
    }
    .brand-logo {
      flex-shrink: 0;
      opacity: 0.25;
      transition: opacity 0.3s ease, transform 0.3s ease;
      filter: grayscale(100%) brightness(0.8);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .brand-logo:hover {
      opacity: 1;
      transform: scale(1.15);
      filter: grayscale(0%) brightness(1);
    }
    .brand-logo.active {
      opacity: 1;
      filter: grayscale(0%) brightness(1);
    }
    .brand-logo img {
      width: 140px;
      height: 90px;
      object-fit: contain;
      border-radius: 8px;
      display: block;
    }
    @media (max-width: 1024px) {
      .brand-slider-wrapper { gap: 2.5rem; }
      .brand-logo img { width: 120px; height: 75px; }
    }
    @media (max-width: 768px) {
      .brand-slider-wrapper { gap: 2rem; }
      .brand-logo img { width: 100px; height: 65px; }
    }
    @media (max-width: 640px) {
      .brand-slider-wrapper { gap: 1.5rem; }
      .brand-logo img { width: 90px; height: 60px; }
    }
  </style>
</head>
<body class="min-h-screen">

<?php include 'header.php'; ?>

<!-- HERO SECTION -->
<section class="relative overflow-hidden bg-[#36454F] isolate">
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-gold/10 via-[#36454F] to-yellow-600/5"></div>
    <div class="absolute inset-0 bg-gradient-to-tl from-[#2C3A44]/80 via-transparent to-gold/5"></div>
    <div id="particles-js" class="absolute inset-0"></div>
  </div>

  <div class="absolute top-0 left-0 w-96 h-96 bg-gold/20 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
  <div class="absolute bottom-0 right-0 w-96 h-96 bg-yellow-500/10 rounded-full blur-3xl translate-x-1/3 translate-y-1/3 animate-ping"></div>
  <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-gradient-to-r from-gold/30 to-transparent rounded-full blur-3xl animate-spin-slow"></div>

  <div class="relative max-w-7xl mx-auto px-6 py-24 sm:py-32 lg:py-40 text-center">
    <div data-aos="fade-up">
      <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold text-primary mb-6 leading-tight">
        <?= $text['hero_title'] ?>
      </h1>
      <p class="text-xl sm:text-2xl text-muted max-w-4xl mx-auto mb-10">
        <?= $text['hero_subtitle'] ?>
      </p>

      <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
        <a href="https://wa.me/212772331080?text=Hi, I want to rent a car at Marrakech Airport!"
           target="_blank"
           class="group inline-flex items-center gap-4 bg-green-600 hover:bg-green-700 text-white font-bold text-lg px-10 py-5 rounded-2xl shadow-2xl transform hover:scale-105 transition-all duration-300">
          <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-1.174-2.294-.174-.338-.434-.327-.672-.327-.227 0-.482.074-.735.174-.67.267-1.25.85-1.25 2.076 0 1.226.89 2.407 1.013 2.567.124.16 1.772 2.708 4.293 3.796 1.52.654 2.158.75 2.92.625.76-.124 2.03-.83 2.317-1.632.287-.802.287-1.49.2-1.632-.087-.15-.346-.25-.644-.3z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.132.547 4.135 1.507 5.987L0 24l6.2-1.625C8.002 23.227 9.973 23.773 12 23.773c6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
          <?= $text['book_on_whatsapp'] ?>
        </a>

        <a href="#cars" class="inline-flex items-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur border border-gold/30 text-primary font-bold text-lg px-10 py-5 rounded-2xl shadow-xl transform hover:scale-105 transition-all">
          <?= $text['view_all_cars'] ?>
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
        </a>
      </div>

      <div class="mt-12 flex flex-wrap justify-center gap-8 text-muted">
        <div class="flex items-center gap-2"><span class="text-gold">✓</span> <?= $text['no_hidden_fees'] ?></div>
        <div class="flex items-center gap-2"><span class="text-gold">✓</span> <?= $text['free_cancellation'] ?></div>
        <div class="flex items-center gap-2"><span class="text-gold">✓</span> <?= $text['support_24_7'] ?></div>
        <div class="flex items-center gap-2"><span class="text-gold">✓</span> <?= $text['fully_insured'] ?></div>
      </div>
    </div>
  </div>

  <!-- BRAND LOGOS SLIDER -->
  <div class="relative border-t border-white/10 pt-8 pb-8">
    <div class="max-w-7xl mx-auto px-6">
      <h2 class="text-center text-2xl sm:text-3xl font-bold text-primary mb-8"><?= $text['big_brands'] ?></h2>
      <div class="brand-slider-container">
        <div class="brand-slider">
          <div class="brand-slider-wrapper">
            <!-- Audi -->
            <div class="brand-logo">
              <img src="uploads/Audi Q8 S-Line.jpg" alt="Audi" loading="lazy">
            </div>
            <!-- BMW -->
            <div class="brand-logo">
              <img src="uploads/BMW 420d Pack M Convertible.jpg" alt="BMW" loading="lazy">
            </div>
            <!-- Mercedes-Benz - Active -->
            <div class="brand-logo active">
              <img src="uploads/Mercedes-Benz S-Class (1).jpg" alt="Mercedes-Benz" loading="lazy">
            </div>
            <!-- Range Rover -->
            <div class="brand-logo">
              <img src="uploads/Range Rover Sport.jpg" alt="Range Rover" loading="lazy">
            </div>
            <!-- Porsche -->
            <div class="brand-logo">
              <img src="uploads/Porsche Cayenne Coup.jpg" alt="Porsche" loading="lazy">
            </div>
            <!-- Cupra/SEAT - Active -->
            <div class="brand-logo active">
              <img src="uploads/Cupra Leon.jpg" alt="Cupra" loading="lazy">
            </div>
            <!-- Volkswagen -->
            <div class="brand-logo">
              <img src="uploads/Volkswagen Golf 8.jpg" alt="Volkswagen" loading="lazy">
            </div>
            <!-- Renault -->
            <div class="brand-logo">
              <img src="uploads/Renault Clio 5.jpg" alt="Renault" loading="lazy">
            </div>
            <!-- Dacia -->
            <div class="brand-logo">
              <img src="uploads/Dacia Duster (1).jpg" alt="Dacia" loading="lazy">
            </div>
            <!-- Fiat -->
            <div class="brand-logo">
              <img src="uploads/Fiat 500 Convertible.jpg" alt="Fiat" loading="lazy">
            </div>
          </div>
          <!-- Duplicate for seamless loop -->
          <div class="brand-slider-wrapper">
            <!-- Audi -->
            <div class="brand-logo">
              <img src="uploads/Audi Q8 S-Line.jpg" alt="Audi" loading="lazy">
            </div>
            <!-- BMW -->
            <div class="brand-logo">
              <img src="uploads/BMW 420d Pack M Convertible.jpg" alt="BMW" loading="lazy">
            </div>
            <!-- Mercedes-Benz - Active -->
            <div class="brand-logo active">
              <img src="uploads/Mercedes-Benz S-Class (1).jpg" alt="Mercedes-Benz" loading="lazy">
            </div>
            <!-- Range Rover -->
            <div class="brand-logo">
              <img src="uploads/Range Rover Sport.jpg" alt="Range Rover" loading="lazy">
            </div>
            <!-- Porsche -->
            <div class="brand-logo">
              <img src="uploads/Porsche Cayenne Coup.jpg" alt="Porsche" loading="lazy">
            </div>
            <!-- Cupra/SEAT - Active -->
            <div class="brand-logo active">
              <img src="uploads/Cupra Leon.jpg" alt="Cupra" loading="lazy">
            </div>
            <!-- Volkswagen -->
            <div class="brand-logo">
              <img src="uploads/Volkswagen Golf 8.jpg" alt="Volkswagen" loading="lazy">
            </div>
            <!-- Renault -->
            <div class="brand-logo">
              <img src="uploads/Renault Clio 5.jpg" alt="Renault" loading="lazy">
            </div>
            <!-- Dacia -->
            <div class="brand-logo">
              <img src="uploads/Dacia Duster (1).jpg" alt="Dacia" loading="lazy">
            </div>
            <!-- Fiat -->
            <div class="brand-logo">
              <img src="uploads/Fiat 500 Convertible.jpg" alt="Fiat" loading="lazy">
            </div>
          </div>
        </div>
      </div>
      <div class="mt-6 border-b border-white/10"></div>
    </div>
  </div>
</section>

<!-- CARS SECTION -->
<section id="cars" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <div data-aos="fade-up" class="bg-card-dark p-6 rounded-xl shadow-lg mb-8 border border-border">
    <form id="filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <input type="text" id="search" placeholder="<?= $text['search_car'] ?>" value="<?= htmlspecialchars($search) ?>" class="p-4 bg-card border border-border text-primary placeholder-muted rounded-lg focus:ring-2 focus:ring-gold text-sm">
      <select id="gear" class="p-4 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
        <option value=""><?= $text['all_transmission'] ?></option>
        <option value="Manual" <?= $gear==='Manual'?'selected':'' ?>><?= $text['manual'] ?></option>
        <option value="Automatic" <?= $gear==='Automatic'?'selected':'' ?>><?= $text['automatic'] ?></option>
      </select>
      <select id="fuel" class="p-4 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
        <option value=""><?= $text['all_fuel'] ?></option>
        <option value="Diesel" <?= $fuel==='Diesel'?'selected':'' ?>><?= $text['diesel'] ?></option>
        <option value="Petrol" <?= $fuel==='Petrol'?'selected':'' ?>><?= $text['petrol'] ?></option>
      </select>
      <select id="sort" class="p-4 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
        <option value="low" <?= $sort==='low'?'selected':'' ?>><?= $text['low_to_high'] ?></option>
        <option value="high" <?= $sort==='high'?'selected':'' ?>><?= $text['high_to_low'] ?></option>
      </select>
      <a href="?" class="bg-gold/20 hover:bg-gold/30 text-gold font-bold py-4 rounded-lg text-center"><?= $text['clear'] ?></a>
    </form>
  </div>

  <p id="results-count" class="text-center text-muted text-lg mb-8"><span dir="ltr"><?= formatNumber(count($cars)) ?></span> <?= $text['vehicles_available'] ?></p>

  <div id="cars-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
    <?php foreach ($cars as $i => $c): ?>
      <?= renderCarCard($c, $i) ?>
    <?php endforeach; ?>
  </div>
</section>


<?php include 'footer.php'; ?>

<!-- SCRIPTS -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script>
  AOS.init({ once: true, duration: 800 });

  particlesJS("particles-js", {
    particles: {
      number: { value: 80, density: { enable: true, value_area: 800 } },
      color: { value: "#FFD700" },
      shape: { type: "circle" },
      opacity: { value: 0.15, random: true },
      size: { value: 4, random: true },
      move: { enable: true, speed: 1, random: true, out_mode: "out" }
    },
    interactivity: { detect_on: "canvas", events: { onhover: { enable: true, mode: "repulse" } } },
    retina_detect: true
  });

  const els = { search: document.getElementById('search'), gear: document.getElementById('gear'), fuel: document.getElementById('fuel'), sort: document.getElementById('sort') };
  const container = document.getElementById('cars-container');
  const countEl = document.getElementById('results-count');
  let isLoading = false;

  const fetchCars = () => {
    if (isLoading) return;
    isLoading = true;

    const params = new URLSearchParams({
      search: els.search.value.trim(),
      gear: els.gear.value,
      fuel: els.fuel.value,
      sort: els.sort.value,
      ajax: 1
    });

    container.innerHTML = '<div class="col-span-full flex justify-center"><div class="spinner"></div></div>';

    fetch(`?${params}`)
      .then(r => r.json())
      .then(data => {
        container.innerHTML = data.html || '<p class="col-span-full text-center text-muted"><?= $text['no_cars_found'] ?></p>';
        countEl.innerHTML = `<span dir="ltr">${data.count}</span> <?= $text['vehicles_available'] ?>`;
        AOS.refreshHard();
      })
      .catch(() => container.innerHTML = '<p class="col-span-full text-center text-red-400">Error.</p>')
      .finally(() => isLoading = false);
  };

  els.search.addEventListener('input', () => { clearTimeout(window.debounce); window.debounce = setTimeout(fetchCars, 300); });
  els.gear.addEventListener('change', fetchCars);
  els.fuel.addEventListener('change', fetchCars);
  els.sort.addEventListener('change', fetchCars);
</script>
</body>
</html>