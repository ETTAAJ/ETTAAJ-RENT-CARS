<?php
  require 'config.php';

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
  // 2. Render Car Card Function
  // ================================================
  function renderCarCard($car, $index = 0): string
  {
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
      ob_start(); ?>
      <div data-aos="fade-up" data-aos-delay="<?= $delay ?>" data-aos-duration="700"
           class="group relative bg-card/90 backdrop-blur-md rounded-3xl overflow-hidden shadow-2xl hover:shadow-gold/20
                  transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02]
                  border border-border flex flex-col h-full">
          <div class="relative w-full pt-[56.25%] bg-card-dark overflow-hidden border-b border-border">
              <img src="<?= htmlspecialchars($imgUrl, ENT_QUOTES) ?>"
                   alt="<?= htmlspecialchars($car['name']) ?> - ETTAAJ Rent Cars Marrakech"
                   class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
                   onerror="this.onerror=null;this.src='https://via.placeholder.com/600x338/36454F/FFFFFF?text=No+Image';this.classList.add('object-contain','p-8');">
          </div>

          <div class="px-5 pb-5 sm:px-6 sm:pb-6 flex-1 flex flex-col bg-card">
              <h3 class="text-xl sm:text-2xl font-extrabold text-primary mb-2 text-center line-clamp-1">
                  <?= htmlspecialchars($car['name']) ?>
              </h3>

              <div class="flex justify-center gap-6 sm:gap-8 text-muted mb-4 text-xs sm:text-sm">
                  <div class="flex flex-col items-center">
                      <svg class="w-5 h-5 mb-1 text-gold" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
                      <span class="font-medium text-primary"><?= (int)$car['seats'] ?> Seats</span>
                  </div>
                  <div class="flex flex-col items-center">
                      <svg class="w-5 h-5 mb-1 text-gold" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
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
                          MAD <span>/day</span>
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
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Car Rental Marrakech Airport | ETTAAJ Rent Cars – No Deposit, From 250 MAD/day</title>
  <meta name="description" content="Best car rental Marrakech Airport. No deposit, free delivery 24/7, luxury & cheap cars. WhatsApp +212 772 331 080">
  <link rel="canonical" href="https://www.ettaajrentcars.ma<?php echo $_SERVER['REQUEST_URI']; ?>">
  <link rel="icon" href="pub_img/ETTAAJ-RENT-CARS.jpg">

  <!-- Tailwind + Fonts + AOS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: { colors: { gold: '#FFD700' } } }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <style>
    :root {
      --bg: #36454F; --bg-dark: #2C3A44; --card: #36454F; --card-dark: #2C3A44;
      --border: #4A5A66; --primary: #FFFFFF; --muted: #D1D5DB; --gold: #FFD700;
    }
    body { background-color: var(--bg); color: var(--primary); font-family: 'Inter', sans-serif; }
    .bg-card { background-color: var(--card); }
    .bg-card-dark { background-color: var(--card-dark); }
    .border-border { border-color: var(--border); }
    .text-primary { color: var(--primary); }
    .text-muted { color: var(--muted); }
    .text-gold { color: var(--gold); }

    .spinner { width: 40px; height: 40px; border: 4px solid var(--bg-dark); border-top: 4px solid var(--gold); border-radius: 50%; animation: spin 1s linear infinite; margin: 40px auto; }
    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes spin-slow { from { transform: translate(-50%,-50%) rotate(0deg); } to { transform: translate(-50%,-50%) rotate(360deg); } }
    .animate-spin-slow { animation: spin-slow 30s linear infinite; }
  </style>
</head>
<body class="min-h-screen">

<?php include 'header.php'; ?>

<!-- ======================== HERO SECTION WITH ANIMATION ======================== -->
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
      <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold text-white mb-6 leading-tight">
        Car Rental Marrakech Airport
      </h1>
      <p class="text-xl sm:text-2xl text-gray-200 max-w-4xl mx-auto mb-10">
        No Deposit • Free Airport Delivery 24/7 • Luxury & Economy Cars • Instant Booking
      </p>

      <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
        <a href="https://wa.me/212772331080?text=Hi, I want to rent a car at Marrakech Airport!"
           target="_blank"
           class="group inline-flex items-center gap-4 bg-green-600 hover:bg-green-700 text-white font-bold text-lg px-10 py-5 rounded-2xl shadow-2xl transform hover:scale-105 transition-all duration-300">
          <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-1.174-2.294-.174-.338-.434-.327-.672-.327-.227 0-.482.074-.735.174-.67.267-1.25.85-1.25 2.076 0 1.226.89 2.407 1.013 2.567.124.16 1.772 2.708 4.293 3.796 1.52.654 2.158.75 2.92.625.76-.124 2.03-.83 2.317-1.632.287-.802.287-1.49.2-1.632-.087-.15-.346-.25-.644-.3z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.132.547 4.135 1.507 5.987L0 24l6.2-1.625C8.002 23.227 9.973 23.773 12 23.773c6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
          Book on WhatsApp
          <span class="group-hover:translate-x-2 transition-transform">→</span>
        </a>

        <a href="#cars" class="inline-flex items-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur border border-gold/30 text-white font-bold text-lg px-10 py-5 rounded-2xl shadow-xl transform hover:scale-105 transition-all">
          View All Cars
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
        </a>
      </div>

      <div class="mt-12 flex flex-wrap justify-center gap-8 text-gray-300">
        <div class="flex items-center gap-2"><span class="text-gold">Check</span> No Hidden Fees</div>
        <div class="flex items-center gap-2"><span class="text-gold">Check</span> Free Cancellation</div>
        <div class="flex items-center gap-2"><span class="text-gold">Check</span> 24/7 Support</div>
        <div class="flex items-center gap-2"><span class="text-gold">Check</span> Fully Insured</div>
      </div>
    </div>
  </div>
</section>

<!-- ======================== CARS SECTION ======================== -->
<section id="cars" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <div data-aos="fade-up" class="bg-card-dark p-6 rounded-xl shadow-lg mb-8 border border-border">
    <form id="filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <input type="text" id="search" placeholder="Search car..." value="<?= htmlspecialchars($search) ?>" class="p-4 bg-card border border-border text-primary placeholder-muted rounded-lg focus:ring-2 focus:ring-gold text-sm">
      <select id="gear" class="p-4 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
        <option value="">All Transmission</option>
        <option value="Manual" <?= $gear==='Manual'?'selected':'' ?>>Manual</option>
        <option value="Automatic" <?= $gear==='Automatic'?'selected':'' ?>>Automatic</option>
      </select>
      <select id="fuel" class="p-4 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
        <option value="">All Fuel</option>
        <option value="Diesel" <?= $fuel==='Diesel'?'selected':'' ?>>Diesel</option>
        <option value="Petrol" <?= $fuel==='Petrol'?'selected':'' ?>>Petrol</option>
      </select>
      <select id="sort" class="p-4 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
        <option value="low" <?= $sort==='low'?'selected':'' ?>>Low to High</option>
        <option value="high" <?= $sort==='high'?'selected':'' ?>>High to Low</option>
      </select>
      <a href="?" class="bg-gold/20 hover:bg-gold/30 text-gold font-bold py-4 rounded-lg text-center">Clear</a>
    </form>
  </div>

  <p id="results-count" class="text-center text-muted text-lg mb-8"><?= count($cars) ?> vehicles available</p>

  <div id="cars-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
    <?php foreach ($cars as $i => $c): ?>
      <?= renderCarCard($c, $i) ?>
    <?php endforeach; ?>
  </div>
</section>

<?php include 'footer.php'; ?>

<!-- ======================== SCRIPTS ======================== -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script>
  AOS.init({ once: true, duration: 800 });

  // Particles animation in hero
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

  // AJAX Filters
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
        container.innerHTML = data.html || '<p class="col-span-full text-center text-muted">No cars found.</p>';
        countEl.textContent = `${data.count} vehicles available`;
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