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
                          <?= formatPrice($originalPrice) ?>
                        </span>
                      <?php endif; ?>

                      <div class="text-4xl sm:text-5xl font-extrabold <?= $hasDiscount ? 'text-green-400' : 'text-white' ?>" dir="ltr">
                        <?= formatPrice($discountedPrice) ?>
                      </div>
                  </div>
                  <span class="inline-block px-4 py-2 bg-gradient-to-r from-gold to-yellow-500 text-black font-bold rounded-full text-sm mt-2">
                    <span dir="ltr"><?= formatPrice($discountedPrice, 0) ?></span>/<?= $text['day'] ?>
                  </span>

                  <div class="flex gap-3 mt-3 text-xs font-medium">
                      <span class="px-3 py-1 bg-card-dark rounded-full border border-border text-muted">
                          <?= $text['week'] ?>: <strong class="text-primary" dir="ltr"><?= formatPrice((float)$car['price_week']) ?></strong>
                      </span>
                      <span class="px-3 py-1 bg-card-dark rounded-full border border-border text-muted">
                          <?= $text['month'] ?>: <strong class="text-primary" dir="ltr"><?= formatPrice((float)$car['price_month']) ?></strong>
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

      // Get counts for filter options based on current search
      $countWhere = [];
      $countParams = [];
      if ($search !== '') {
          $countWhere[] = "name LIKE ?";
          $countParams[] = "%$search%";
      }
      $countWhereClause = !empty($countWhere) ? " WHERE " . implode(' AND ', $countWhere) : "";
      
      $gearCountStmt = $pdo->prepare("SELECT gear, COUNT(*) as count FROM cars $countWhereClause GROUP BY gear");
      $gearCountStmt->execute($countParams);
      $gearCounts = [];
      while ($row = $gearCountStmt->fetch(PDO::FETCH_ASSOC)) {
          $gearCounts[$row['gear']] = (int)$row['count'];
      }
      
      $fuelCountStmt = $pdo->prepare("SELECT fuel, COUNT(*) as count FROM cars $countWhereClause GROUP BY fuel");
      $fuelCountStmt->execute($countParams);
      $fuelCounts = [];
      while ($row = $fuelCountStmt->fetch(PDO::FETCH_ASSOC)) {
          $fuelCounts[$row['fuel']] = (int)$row['count'];
      }
      
      $totalCountStmt = $pdo->prepare("SELECT COUNT(*) as total FROM cars $countWhereClause");
      $totalCountStmt->execute($countParams);
      $totalCount = (int)$totalCountStmt->fetch(PDO::FETCH_ASSOC)['total'];

      header('Content-Type: application/json; charset=utf-8');
      echo json_encode([
          'html' => $html, 
          'count' => count($cars),
          'options' => [
              'total' => $totalCount,
              'gear' => $gearCounts,
              'fuel' => $fuelCounts
          ]
      ]);
      exit;
  }

  // ================================================
  // 5. Normal Page Load
  // ================================================
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  // Get all cars for hero slider (limit to 10 for performance)
  $sliderStmt = $pdo->prepare("SELECT * FROM cars ORDER BY RAND() LIMIT 10");
  $sliderStmt->execute();
  $sliderCars = $sliderStmt->fetchAll(PDO::FETCH_ASSOC);
  
  // Get all cars for dropdown list
  $allCarsStmt = $pdo->prepare("SELECT id, name FROM cars ORDER BY name ASC");
  $allCarsStmt->execute();
  $allCars = $allCarsStmt->fetchAll(PDO::FETCH_ASSOC);
  
  // Get counts per option for filter dropdowns
  $countWhere = [];
  $countParams = [];
  if ($search !== '') {
    $countWhere[] = "name LIKE ?";
    $countParams[] = "%$search%";
  }
  $countWhereClause = !empty($countWhere) ? " WHERE " . implode(' AND ', $countWhere) : "";
  
  // Count by gear type
  $gearCountStmt = $pdo->prepare("SELECT gear, COUNT(*) as count FROM cars $countWhereClause GROUP BY gear");
  $gearCountStmt->execute($countParams);
  $gearCounts = [];
  while ($row = $gearCountStmt->fetch(PDO::FETCH_ASSOC)) {
    $gearCounts[$row['gear']] = (int)$row['count'];
  }
  
  // Count by fuel type
  $fuelCountStmt = $pdo->prepare("SELECT fuel, COUNT(*) as count FROM cars $countWhereClause GROUP BY fuel");
  $fuelCountStmt->execute($countParams);
  $fuelCounts = [];
  while ($row = $fuelCountStmt->fetch(PDO::FETCH_ASSOC)) {
    $fuelCounts[$row['fuel']] = (int)$row['count'];
  }
  
  // Total count
  $totalCountStmt = $pdo->prepare("SELECT COUNT(*) as total FROM cars $countWhereClause");
  $totalCountStmt->execute($countParams);
  $totalCount = (int)$totalCountStmt->fetch(PDO::FETCH_ASSOC)['total'];
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
      theme: { extend: { colors: { gold: '#FFB22C' } } }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root {
      --primary-color: #FFB22C;
      --secondary-color: #000000;
      --therde: #854836;
      --text-color: #F7F7F7;
      --light-bg: #353333;
      --shadow: 0 5px 15px rgba(246, 176, 0, 0.496);
      --bg: var(--light-bg); --bg-dark: var(--light-bg); --card: var(--light-bg); --card-dark: var(--light-bg);
      --border: #4A5A66; --primary: var(--text-color); --muted: #D1D5DB; --gold: var(--primary-color);
      --text-primary: var(--text-color); --text-muted: var(--muted);
      --card-dark-gradient: linear-gradient(135deg, #0B0B0C 0%, #121212 55%, var(--therde) 120%);
    }
    .light {
      --bg: #f8fafc; --bg-dark: #e2e8f0; --card: #EFECE3; --card-dark: #EFECE3;
      --border: #cbd5e1; --primary: #1e293b; --muted: #64748b; --gold: #d97706;
      --text-primary: var(--primary); --text-muted: var(--muted);
    }
    body { background-color: var(--bg); color: var(--primary); font-family: 'Inter', sans-serif; }
    .bg-card { background-color: var(--card); }
    .bg-card-dark { background-color: var(--card-dark); }
    .car-card-bg { background: #000000 !important; }
    .light .car-card-bg { background: #EFECE3 !important; }
    .light .car-card-bg .text-white { color: #000000 !important; }
    .light .car-card-bg .text-primary { color: #000000 !important; }
    .light .car-card-bg .text-muted { color: #000000 !important; }
    .light .car-card-bg h3 { color: #000000 !important; }
    .light .car-card-bg .bg-card-dark .text-primary { color: #000000 !important; }
    .border-border { border-color: var(--border); }
    .text-primary { color: var(--primary); }
    .text-muted { color: var(--muted); }
    .text-gold { color: var(--gold); }

    /* Search Dropdown Styles */
    #search {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 0.75rem center;
      background-size: 12px;
    }
    #search option {
      background: white;
      color: #333;
      padding: 8px;
    }

    /* Responsive Filter Bar */
    #filter-form {
      display: grid;
      grid-template-columns: 1fr;
      gap: 0.75rem;
    }
    @media (min-width: 640px) {
      #filter-form {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
      }
      #filter-form > div:first-child {
        grid-column: span 2;
      }
    }
    @media (min-width: 1024px) {
      #filter-form {
        grid-template-columns: repeat(5, 1fr);
      }
      #filter-form > div:first-child {
        grid-column: span 1;
      }
    }

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

    /* Infinite Car Slider Styles */
    .car-slider-container {
      position: relative;
      overflow: hidden;
      mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
      -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
    }
    .car-slider-track {
      display: flex;
      gap: 1.5rem;
      animation: slideCars 30s linear infinite;
      width: fit-content;
    }
    .car-slider-track:hover {
      animation-play-state: paused;
    }
    .car-slide-item {
      flex: 0 0 280px;
      min-width: 280px;
    }
    @keyframes slideCars {
      0% {
        transform: translateX(0);
      }
      100% {
        transform: translateX(calc(-280px * 10 - 1.5rem * 10));
      }
    }
    @media (max-width: 768px) {
      .car-slide-item {
        flex: 0 0 240px;
        min-width: 240px;
      }
      @keyframes slideCars {
        0% { transform: translateX(0); }
        100% { transform: translateX(calc(-240px * 10 - 1.5rem * 10)); }
      }
    }
    @media (max-width: 640px) {
      .car-slide-item {
        flex: 0 0 200px;
        min-width: 200px;
      }
      @keyframes slideCars {
        0% { transform: translateX(0); }
        100% { transform: translateX(calc(-200px * 10 - 1.5rem * 10)); }
      }
    }

    /* 3D Logo Animation */
    .logo-3d-container {
      perspective: 1000px;
      perspective-origin: center center;
      display: inline-block;
    }
    .logo-3d {
      transform-style: preserve-3d;
      animation: logo3dFloat 6s ease-in-out infinite;
      transition: transform 0.3s ease;
    }
    .logo-3d:hover {
      animation-play-state: paused;
      transform: rotateY(15deg) rotateX(5deg) scale(1.05);
    }
    @keyframes logo3dFloat {
      0%, 100% {
        transform: rotateY(0deg) rotateX(0deg) translateZ(0px);
      }
      25% {
        transform: rotateY(5deg) rotateX(-3deg) translateZ(10px);
      }
      50% {
        transform: rotateY(0deg) rotateX(0deg) translateZ(20px);
      }
      75% {
        transform: rotateY(-5deg) rotateX(3deg) translateZ(10px);
      }
    }
    .logo-3d img {
      transform-style: preserve-3d;
      filter: drop-shadow(0 10px 30px rgba(255, 178, 44, 0.3));
      transition: filter 0.3s ease;
    }
    .logo-3d:hover img {
      filter: drop-shadow(0 15px 40px rgba(255, 178, 44, 0.5));
    }
  </style>
</head>
<body class="min-h-screen">

<?php include 'header.php'; ?>

<!-- HERO SECTION WITH LOGO AND CAR SLIDER -->
<section class="relative overflow-hidden py-16 lg:py-24">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-card-dark rounded-xl shadow-lg border border-border p-6 sm:p-8 lg:p-12">
    <!-- Logo Section -->
    <div class="text-center mb-12" data-aos="fade-down">
      <div class="logo-3d-container mb-8">
        <div class="logo-3d">
          <img src="pub_img/ETTAAJ-RENT-CARS.jpg" 
               alt="ETTAAJ Rent Cars Logo" 
               class="max-w-xs sm:max-w-sm md:max-w-md mx-auto rounded-2xl shadow-2xl border-4 border-[var(--primary-color)]"
               style="box-shadow: var(--shadow);">
        </div>
      </div>
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[var(--primary-color)] via-yellow-400 to-[var(--primary-color)] mb-4">
        <?= $text['hero_title'] ?? 'Premium Car Rental' ?>
      </h1>
      <p class="text-xl sm:text-2xl text-[var(--text-color)]/80 max-w-3xl mx-auto">
        <?= $text['hero_subtitle'] ?? 'Your trusted partner for luxury car rental in Marrakech' ?>
      </p>
    </div>

    <!-- Infinite Car Images Slider -->
    <div class="relative" data-aos="fade-up">
      <div class="car-slider-container overflow-hidden py-8">
        <div class="car-slider-track">
          <?php 
          // Duplicate cars for seamless loop
          $sliderCarsDuplicated = array_merge($sliderCars, $sliderCars, $sliderCars);
          foreach ($sliderCarsDuplicated as $car): 
            $carImg = !empty($car['image']) 
              ? 'uploads/' . basename($car['image']) 
              : 'https://via.placeholder.com/300x200/000000/FFFFFF?text=' . urlencode($car['name']);
          ?>
            <div class="car-slide-item">
              <div class="relative group">
                <img src="<?= htmlspecialchars($carImg) ?>" 
                     alt="<?= htmlspecialchars($car['name']) ?>"
                     class="w-full h-48 object-cover rounded-xl border-2 border-[var(--primary-color)]/30 group-hover:border-[var(--primary-color)] transition-all duration-300"
                     onerror="this.src='https://via.placeholder.com/300x200/000000/FFFFFF?text=<?= urlencode($car['name']) ?>'">
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-3 rounded-b-xl">
                  <p class="text-white text-sm font-bold text-center"><?= htmlspecialchars($car['name']) ?></p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    </div>
  </div>
</section>

<!-- CARS SECTION -->
<section id="cars" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <div data-aos="fade-up" class="bg-card-dark p-4 sm:p-6 rounded-xl shadow-lg mb-8 border border-border">
    <form id="filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
      <!-- Car Dropdown - Full width on mobile, spans 2 columns on md -->
      <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 sm:col-span-2 lg:col-span-1">
        <label for="search" class="text-muted text-sm font-medium whitespace-nowrap sm:min-w-[50px]">Car:</label>
        <select id="search" class="w-full flex-1 p-3 bg-white border border-gray-300 text-gray-900 rounded-md focus:ring-2 focus:ring-[var(--primary-color)] focus:border-[var(--primary-color)] text-sm cursor-pointer" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 12px; padding-right: 2.5rem;">
          <option value="">All</option>
          <?php foreach ($allCars as $carOption): ?>
            <option value="<?= htmlspecialchars($carOption['name']) ?>" <?= $search === $carOption['name'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($carOption['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <!-- Gear Select -->
      <select id="gear" class="w-full p-3 sm:p-4 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
        <option value=""><?= $text['all_transmission'] ?> (<?= formatNumber($totalCount) ?>)</option>
        <option value="Manual" <?= $gear==='Manual'?'selected':'' ?>><?= $text['manual'] ?> (<?= formatNumber($gearCounts['Manual'] ?? 0) ?>)</option>
        <option value="Automatic" <?= $gear==='Automatic'?'selected':'' ?>><?= $text['automatic'] ?> (<?= formatNumber($gearCounts['Automatic'] ?? 0) ?>)</option>
      </select>
      
      <!-- Fuel Select -->
      <select id="fuel" class="w-full p-3 sm:p-4 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
        <option value=""><?= $text['all_fuel'] ?> (<?= formatNumber($totalCount) ?>)</option>
        <option value="Diesel" <?= $fuel==='Diesel'?'selected':'' ?>><?= $text['diesel'] ?> (<?= formatNumber($fuelCounts['Diesel'] ?? 0) ?>)</option>
        <option value="Petrol" <?= $fuel==='Petrol'?'selected':'' ?>><?= $text['petrol'] ?> (<?= formatNumber($fuelCounts['Petrol'] ?? 0) ?>)</option>
      </select>
      
      <!-- Sort Select -->
      <select id="sort" class="w-full p-3 sm:p-4 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
        <option value="low" <?= $sort==='low'?'selected':'' ?>><?= $text['low_to_high'] ?></option>
        <option value="high" <?= $sort==='high'?'selected':'' ?>><?= $text['high_to_low'] ?></option>
      </select>
      
      <!-- Clear Button -->
      <a href="?" class="w-full bg-gold/20 hover:bg-gold/30 text-gold font-bold py-3 sm:py-4 rounded-lg text-center transition-colors"><?= $text['clear'] ?></a>
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
<script>
  AOS.init({ once: true, duration: 800 });

  const els = { search: document.getElementById('search'), gear: document.getElementById('gear'), fuel: document.getElementById('fuel'), sort: document.getElementById('sort') };
  const container = document.getElementById('cars-container');
  const countEl = document.getElementById('results-count');
  let isLoading = false;

  const fetchCars = () => {
    if (isLoading) return;
    isLoading = true;

    // Get current currency from URL or default to MAD
    const urlParams = new URLSearchParams(window.location.search);
    const currentCurrency = urlParams.get('currency') || 'MAD';

    const params = new URLSearchParams({
      search: els.search.value || '',
      gear: els.gear.value,
      fuel: els.fuel.value,
      sort: els.sort.value,
      currency: currentCurrency,
      ajax: 1
    });

    container.innerHTML = '<div class="col-span-full flex justify-center"><div class="spinner"></div></div>';

    fetch(`?${params}`)
      .then(r => r.json())
      .then(data => {
        container.innerHTML = data.html || '<p class="col-span-full text-center text-muted"><?= $text['no_cars_found'] ?></p>';
        countEl.innerHTML = `<span dir="ltr">${data.count}</span> <?= $text['vehicles_available'] ?>`;
        
        // Update filter option counts
        if (data.options) {
          const gearSelect = els.gear;
          const fuelSelect = els.fuel;
          
          // Update gear options
          Array.from(gearSelect.options).forEach(opt => {
            if (opt.value === '') {
              opt.text = '<?= $text['all_transmission'] ?> (' + data.options.total + ')';
            } else if (opt.value === 'Manual') {
              opt.text = '<?= $text['manual'] ?> (' + (data.options.gear.Manual || 0) + ')';
            } else if (opt.value === 'Automatic') {
              opt.text = '<?= $text['automatic'] ?> (' + (data.options.gear.Automatic || 0) + ')';
            }
          });
          
          // Update fuel options
          Array.from(fuelSelect.options).forEach(opt => {
            if (opt.value === '') {
              opt.text = '<?= $text['all_fuel'] ?> (' + data.options.total + ')';
            } else if (opt.value === 'Diesel') {
              opt.text = '<?= $text['diesel'] ?> (' + (data.options.fuel.Diesel || 0) + ')';
            } else if (opt.value === 'Petrol') {
              opt.text = '<?= $text['petrol'] ?> (' + (data.options.fuel.Petrol || 0) + ')';
            }
          });
        }
        
        AOS.refreshHard();
      })
      .catch(() => container.innerHTML = '<p class="col-span-full text-center text-red-400">Error.</p>')
      .finally(() => isLoading = false);
  };

  // Search dropdown change event
  els.search.addEventListener('change', fetchCars);
  els.gear.addEventListener('change', fetchCars);
  els.fuel.addEventListener('change', fetchCars);
  els.sort.addEventListener('change', fetchCars);

  // Reload page when currency changes (currency switcher uses links, so this ensures refresh)
  // Also refresh cards if currency parameter changes in URL
  let lastCurrency = new URLSearchParams(window.location.search).get('currency') || 'MAD';
  const checkCurrencyChange = () => {
    const currentCurrency = new URLSearchParams(window.location.search).get('currency') || 'MAD';
    if (currentCurrency !== lastCurrency) {
      lastCurrency = currentCurrency;
      // Currency changed, reload cards to update prices
      fetchCars();
    }
  };
  
  // Check for currency changes periodically (in case URL changes without page reload)
  setInterval(checkCurrencyChange, 500);
  
  // Also check on popstate (back/forward button)
  window.addEventListener('popstate', () => {
    setTimeout(checkCurrencyChange, 100);
  });
</script>
</body>
</html>