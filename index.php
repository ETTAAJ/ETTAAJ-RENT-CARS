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
           class="group relative bg-[#36454F]/90 backdrop-blur-md rounded-3xl overflow-hidden shadow-2xl hover:shadow-gold/20
                  transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02]
                  border border-[#4A5A66] flex flex-col h-full">
          <!-- RECTANGULAR RESPONSIVE IMAGE (16:9) -->
          <div class="relative w-full pt-[56.25%] bg-[#2C3A44] overflow-hidden border-b border-[#4A5A66]">
              <img src="<?= htmlspecialchars($imgUrl) ?>"
                   alt="<?= htmlspecialchars($car['name']) ?>"
                   class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
                   onerror="this.onerror=null; this.src='https://via.placeholder.com/600x338/36454F/FFFFFF?text=No+Image';
                            this.classList.add('object-contain','p-8');">
          </div>
          <!-- Card Body -->
          <div class="px-5 pb-5 sm:px-6 sm:pb-6 flex-1 flex flex-col bg-[#36454F]">
              <h3 class="text-xl sm:text-2xl font-extrabold text-white mb-2 text-center line-clamp-1">
                  <?= htmlspecialchars($car['name']) ?>
              </h3>
              <!-- Specs Icons -->
              <div class="flex justify-center gap-6 sm:gap-8 text-gray-300 mb-4 text-xs sm:text-sm">
                  <div class="flex flex-col items-center">
                      <svg class="w-5 h-5 mb-1 text-gold" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                      </svg>
                      <span class="font-medium text-white"><?= (int)$car['seats'] ?> Seats</span>
                  </div>
                  <div class="flex flex-col items-center">
                      <svg class="w-5 h-5 mb-1 text-gold" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M5 3h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                      </svg>
                      <span class="font-medium text-white"><?= (int)$car['bags'] ?> Bags</span>
                  </div>
              </div>
              <!-- Transmission & Fuel -->
              <div class="flex justify-center gap-4 text-xs text-gray-300 mb-5 font-medium">
                  <span class="px-3 py-1 bg-[#2C3A44] rounded-full text-white border border-[#4A5A66]"><?= htmlspecialchars($car['gear']) ?></span>
                  <span class="px-3 py-1 bg-[#2C3A44] rounded-full text-white border border-[#4A5A66]"><?= htmlspecialchars($car['fuel']) ?></span>
              </div>
              <!-- LUXURY PRICE – MAD BESIDE /day -->
              <div class="flex flex-col items-center mt-4 mb-3">
                  <div class="flex items-baseline gap-2">
                      <span class="text-4xl sm:text-5xl font-extrabold text-white">
                          <?= number_format((float)$car['price_day']) ?>
                      </span>
                      <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold text-white bg-gradient-to-r from-gold to-yellow-500 rounded-full shadow-lg animate-pulse">
                          <span>MAD</span>
                          <span>/day</span>
                      </span>
                  </div>
                  <div class="flex gap-3 mt-3 text-xs font-medium">
                      <span class="px-3 py-1 bg-[#2C3A44] rounded-full border border-[#4A5A66] text-gray-300">
                          Week: <strong class="text-white">MAD<?= number_format((float)$car['price_week']) ?></strong>
                      </span>
                      <span class="px-3 py-1 bg-[#2C3A44] rounded-full border border-[#4A5A66] text-gray-300">
                          Month: <strong class="text-white">MAD<?= number_format((float)$car['price_month']) ?></strong>
                      </span>
                  </div>
              </div>
              <!-- CTA Button -->
              <div class="mt-auto">
                  <a href="car-detail.php?id=<?= (int)$car['id'] ?>"
                     class="block w-full text-center bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-400
                            text-white font-bold py-3 px-6 rounded-2xl shadow-lg transition-all duration-300
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
  include 'header.php';
  ?>
<!-- HERO SECTION – ANIMATED LUXURY -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-[#1e293b] via-[#36454F] to-[#2C3A44]"
         style="background-image: url('https://images.unsplash.com/photo-1494905998402-395d579af36f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
                background-size: cover; background-position: center;"
         data-aos="fade" data-aos-duration="1500">

    <!-- Dark Overlay -->
    <div class="absolute inset-0 bg-black/60"></div>

    <!-- Floating Gold Particles -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="particle"></div>
        <div class="particle delay-1"></div>
        <div class="particle delay-2"></div>
        <div class="particle delay-3"></div>
        <div class="particle delay-4"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <!-- Logo + Name -->
        <div data-aos="fade-down" data-aos-delay="300" class="mb-6">
            <div class="flex justify-center items-center gap-3">
                <img src="pub_img/GoldCar.png" alt="Gold Cars Logo" class="w-16 h-16 rounded-full ring-4 ring-gold/50 shadow-2xl">
                <h1 class="text-5xl md:text-7xl font-extrabold bg-gradient-to-r from-gold via-yellow-400 to-gold bg-clip-text text-transparent drop-shadow-2xl">
                    Gold Cars
                </h1>
            </div>
        </div>

        <!-- Main Title -->
        <h2 data-aos="zoom-in" data-aos-delay="600" data-aos-duration="1000"
            class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
            Rent Your <span class="text-gold animate-pulse">Dream Car</span>
        </h2>

        <!-- Subtitle -->
        <p data-aos="fade-up" data-aos-delay="900" 
           class="text-lg md:text-xl text-gray-300 mb-10 max-w-3xl mx-auto leading-relaxed">
            Premium fleet • Flexible plans • <span class="text-gold font-semibold">Golden service in Morocco</span>
        </p>

        <!-- CTA Buttons -->
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
        </div>

        <!-- Scroll Indicator -->
        <div data-aos="fade-up" data-aos-delay="1500" class="mt-16">
            <div class="animate-bounce mx-auto w-10 h-14 border-2 border-gold/50 rounded-full flex justify-center">
                <div class="w-1 h-3 bg-gold rounded-full mt-3"></div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Scroll to explore</p>
        </div>
    </div>
</section>
  <!-- Filters & Cars -->
  <section id="cars" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-[#36454F]">
      <!-- RESPONSIVE FILTER SECTION -->
      <div data-aos="fade-up" data-aos-delay="200" data-aos-duration="800"
           class="bg-[#2C3A44] p-4 sm:p-6 rounded-xl shadow-lg mb-6 border border-[#4A5A66]">
          <form id="filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
              <!-- Search -->
              <input type="text" id="search" placeholder="Search car..."
                     value="<?= htmlspecialchars($search) ?>"
                     class="col-span-1 sm:col-span-2 lg:col-span-1 p-3 bg-[#36454F] border border-[#4A5A66] text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-gold focus:border-gold text-sm">
              <!-- Gear -->
              <select id="gear" class="p-3 bg-[#36454F] border border-[#4A5A66] text-white rounded-lg focus:ring-2 focus:ring-gold text-sm">
                  <option value="" class="text-white bg-[#36454F]">All Gears</option>
                  <option value="Manual" <?= $gear === 'Manual' ? 'selected' : '' ?>>Manual</option>
                  <option value="Automatic" <?= $gear === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
              </select>
              <!-- Fuel -->
              <select id="fuel" class="p-3 bg-[#36454F] border border-[#4A5A66] text-white rounded-lg focus:ring-2 focus:ring-gold text-sm">
                  <option value="" class="text-white bg-[#36454F]">All Fuels</option>
                  <option value="Diesel" <?= $fuel === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                  <option value="Petrol" <?= $fuel === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
              </select>
              <!-- Sort -->
              <select id="sort" class="p-3 bg-[#36454F] border border-[#4A5A66] text-white rounded-lg focus:ring-2 focus:ring-gold text-sm">
                  <option value="low" <?= $sort === 'low' ? 'selected' : '' ?>>Low to High</option>
                  <option value="high" <?= $sort === 'high' ? 'selected' : '' ?>>High to Low</option>
              </select>
              <!-- Clear Button -->
              <a href="index.php"
                 class="col-span-1 sm:col-span-2 lg:col-span-1 bg-[#4A5A66] hover:bg-[#5A6B77] text-white font-medium py-3 px-4 rounded-lg transition text-center text-sm flex items-center justify-center">
                  Clear All
              </a>
          </form>
      </div>
      <!-- Results Count -->
      <p id="results-count" class="text-sm text-gray-300 mb-4">
          <?= count($cars) ?> car<?= count($cars) !== 1 ? 's' : '' ?> found
      </p>
      <!-- Cars Grid -->
      <div id="cars-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <?php foreach ($cars as $i => $c): ?>
              <?= renderCarCard($c, $i) ?>
          <?php endforeach; ?>
      </div>
  </section>
  <?php include 'footer.php'; ?>
  <!-- AOS + CSS + JS -->
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <style>
      :root {
          --dark-bg: #36454F;
          --darker-bg: #2C3A44;
          --border: #4A5A66;
          --text: #FFFFFF;
          --text-muted: #D1D5DB;
      }
      * {
          color: var(--text) !important;
      }
      body, .bg-white {
          background-color: var(--dark-bg) !important;
          color: white !important;
      }
      .bg-gray-100, .bg-gray-200 {
          background-color: var(--darker-bg) !important;
      }
      .border-gray-300, .border-gray-200 {
          border-color: var(--border) !important;
      }
      input, select, textarea {
          background-color: var(--dark-bg) !important;
          color: white !important;
      }
      input::placeholder, select option {
          color: #9CA3AF !important;
      }
      .text-gray-600, .text-gray-500, .text-gray-700 {
          color: #D1D5DB !important;
      }
      .text-gray-800, .text-gray-900 {
          color: white !important;
      }
      .spinner {
          width: 40px;
          height: 40px;
          border: 4px solid #36454F;
          border-top: 4px solid #FFD700;
          border-radius: 50%;
          animation: spin 1s linear infinite;
          margin: 40px auto;
      }
      @keyframes spin {
          to { transform: rotate(360deg); }
      }
      #filter-form {
          display: grid;
          gap: 0.75rem;
      }
      @media (max-width: 640px) {
          #filter-form > * {
              width: 100%;
          }
      }
      /* Gold glow on hover */
      .hover\:shadow-gold\/20:hover {
          box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
      }
  </style>
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
              fetch(`index.php?${params}`, {
                  headers: { 'X-Requested-With': 'XMLHttpRequest' }
              })
              .then(r => {
                  if (!r.ok) throw new Error('Network error');
                  return r.json();
              })
              .then(data => {
                  container.innerHTML = data.html || '<p class="col-span-full text-center text-gray-400">No cars found.</p>';
                  countEl.textContent = `${data.count} car${data.count !== 1 ? 's' : ''} found`;
                  AOS.refreshHard();
              })
              .catch(err => {
                  console.error(err);
                  container.innerHTML = fallbackHTML;
                  countEl.textContent = `${container.querySelectorAll('[data-aos]').length} car${container.querySelectorAll('[data-aos]').length !== 1 ? 's' : ''} found`;
              })
              .finally(() => {
                  isLoading = false;
              });
          }, 300);
      };
      els.search.addEventListener('input', fetchCars);
      els.gear.addEventListener('change', fetchCars);
      els.fuel.addEventListener('change', fetchCars);
      els.sort.addEventListener('change', fetchCars);
      document.addEventListener('DOMContentLoaded', () => {
          const hasFilters = ['search', 'gear', 'fuel', 'sort'].some(p =>
              new URLSearchParams(window.location.search).has(p)
          );
          if (hasFilters) {
              countEl.textContent = `${container.children.length} car${container.children.length !== 1 ? 's' : ''} found`;
          }
      });
  </script>
  </body>
  </html>