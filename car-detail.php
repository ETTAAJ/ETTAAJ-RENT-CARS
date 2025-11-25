<?php
require_once 'init.php';
require_once 'config.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id != ? ORDER BY RAND() LIMIT 4");
$stmt->execute([$id]);
$similarCars = $stmt->fetchAll(PDO::FETCH_ASSOC);

function carImageUrl($image)
{
    if (empty($image)) return '';
    $file = 'uploads/' . basename($image);
    $full = $_SERVER['DOCUMENT_ROOT'] . '/' . $file;
    $v = file_exists($full) ? '?v=' . filemtime($full) : '';
    return $file . $v;
}

// Calculate discount
$discount = (int)($car['discount'] ?? 0);
$originalPrice = (float)$car['price_day'];
$discountedPrice = $discount > 0 ? $originalPrice * (1 - $discount / 100) : $originalPrice;
$hasDiscount = $discount > 0;
?>

<?php include 'header.php'; ?>

<!-- Bootstrap Icons (for correct bag icon) -->
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
  body { background-color: var(--bg); color: var(--primary); }
  .bg-card { background-color: var(--card); }
  .bg-card-dark { background-color: var(--card-dark); }
  .car-card-bg { background: var(--card-dark-gradient); }
  .border-border { border-color: var(--border); }
  .text-primary { color: var(--primary); }
  .text-muted { color: var(--muted); }
  .text-gold { color: var(--gold); }

  .tab-bar {
    position: relative;
    background: rgba(30, 30, 30, 0.4);
    backdrop-filter: blur(12px);
    border-radius: 16px;
    padding: 8px;
    border: 1px solid rgba(255, 215, 0, 0.2);
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    transition: box-shadow 0.5s ease;
  }
  .light .tab-bar {
    background: rgba(255, 255, 255, 0.6);
    border-color: rgba(217, 119, 6, 0.2);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
  }
  .tab-bar::before {
    content: '';
    position: absolute;
    bottom: 0; left: 0;
    width: 50%;
    height: 5px;
    background: linear-gradient(90deg, #FFD700, #FFA500);
    border-radius: 3px;
    transition: transform 0.65s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.5s ease;
    transform: translateX(0%);
    box-shadow: 0 0 20px rgba(255, 215, 0, 0.7);
  }
  .tab-bar.active-details::before { transform: translateX(0%); }
  .tab-bar.active-booking::before { transform: translateX(100%); }

  .tab-item {
    flex: 1; padding: 18px 12px; text-align: center; font-weight: 700; font-size: 1.15rem;
    border-radius: 12px; transition: all 0.4s ease; position: relative; z-index: 10;
  }
  .tab-item svg { width: 26px; height: 26px; margin-right: 10px; }
  html[dir="rtl"] .tab-item svg { margin-right: 0; margin-left: 10px; }
  .tab-item.active { color: #000; }
  .tab-item:not(.active) { color: rgba(255,255,255,0.75); }
  .light .tab-item:not(.active) { color: rgba(30, 41, 59, 0.75); }
  .tab-item:hover:not(.active) { color: #FFD700; }
  .light .tab-item:hover:not(.active) { color: #d97706; }

  @media (max-width: 640px) {
    .tab-item { padding: 14px 8px; font-size: 1rem; }
    .tab-item svg { width: 22px; height: 22px; }
    .tab-item span { display: block; margin-top: 6px; font-size: 0.8rem; }
  }

  /* Discount Badge */
  .discount-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    z-index: 10;
    background: #10b981;
    color: white;
    font-weight: 800;
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    animation: pulse 2s infinite;
    direction: ltr !important;
  }
  html[dir="rtl"] .discount-badge {
    right: auto;
    left: 16px;
  }
</style>

<!-- Green Book Button -->
<style>
  .new-book-btn {
    background: linear-gradient(to right, #10b981, #059669) !important;
  }
  .new-book-btn:hover {
    background: linear-gradient(to right, #059669, #047857) !important;
    transform: scale(1.05);
  }
</style>

<main class="max-w-7xl mx-auto px-4 py-12 bg-[var(--bg)] text-primary">

  <!-- LUXURY 2-TAB BAR -->
  <div class="max-w-3xl mx-auto mb-16">
    <div class="tab-bar active-details" id="tab-bar" style="direction: ltr;">
      <div class="flex" style="direction: ltr;">
        <a href="<?= langUrl('car-detail.php', ['id' => $car['id']]) ?>" class="tab-item flex items-center justify-center active">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h-4m-6 0H5"/>
          </svg>
          <span><?= t('car_details') ?></span>
        </a>
        <a href="<?= langUrl('booking.php', ['id' => $car['id']]) ?>" class="tab-item flex items-center justify-center">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <span><?= t('booking_details') ?></span>
        </a>
      </div>
    </div>
  </div>

  <!-- HERO TITLE -->
  <div class="text-center mb-16" data-aos="fade-up">
    <h1 class="text-5xl sm:text-6xl md:text-7xl font-black tracking-tight text-transparent bg-clip-text 
               bg-gradient-to-r from-amber-400 via-yellow-500 to-orange-500 drop-shadow-2xl leading-tight">
      <?= htmlspecialchars($car['name']) ?>
    </h1>
    <p class="mt-6 text-xl sm:text-2xl font-medium text-amber-400 drop-shadow-lg tracking-wider">
      <?= $text['luxury_performance'] ?>
    </p>
  </div>

  <div class="grid lg:grid-cols-2 gap-10 max-w-6xl mx-auto">

    <!-- LEFT: LUXURY CARD -->
    <div data-aos="fade-right" class="h-full">
      <div class="group relative car-card-bg backdrop-blur-md rounded-3xl overflow-hidden shadow-2xl hover:shadow-gold/20 transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02] border border-border flex flex-col h-full">

        <div class="relative w-full pt-[56.25%] car-card-bg overflow-hidden border-b border-border">
          <?php
          $imgUrl = !empty($car['image'])
              ? carImageUrl($car['image'])
              : 'https://via.placeholder.com/800x450/36454F/FFFFFF?text=' . urlencode($car['name']);
          ?>
          <img src="<?= htmlspecialchars($imgUrl) ?>" 
               alt="<?= htmlspecialchars($car['name']) ?>" 
               class="car-image-clear absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
               loading="eager"
               decoding="async">

          <!-- Discount Badge -->
          <?php if ($hasDiscount): ?>
            <div class="discount-badge" style="direction: ltr;">
              -<?= $discount ?>%
            </div>
          <?php endif; ?>
        </div>

        <div class="p-6 flex-1 flex flex-col">
          <h3 class="text-2xl font-extrabold text-white text-center mb-4"><?= htmlspecialchars($car['name']) ?></h3>

          <!-- Seats & Bags – Fixed Icons -->
          <div class="flex justify-center gap-8 text-sm mb-4" style="direction: ltr;">
            <div class="text-center">
              <i class="bi bi-person-fill w-6 h-6 mx-auto mb-1 text-gold"></i>
              <span class="text-white"><span dir="ltr"><?= formatNumber($car['seats']) ?></span> <?= $text['seats'] ?></span>
            </div>
            <div class="text-center">
              <i class="bi bi-briefcase-fill w-6 h-6 mx-auto mb-1 text-gold"></i>
              <span class="text-white"><span dir="ltr"><?= formatNumber($car['bags']) ?></span> <?= $text['bags'] ?></span>
            </div>
          </div>

          <div class="flex justify-center gap-4 mb-6" style="direction: ltr;">
            <span class="px-4 py-1 bg-card-dark rounded-full text-sm border border-border"><?= $car['gear'] === 'Manual' ? $text['manual'] : ($car['gear'] === 'Automatic' ? $text['automatic'] : htmlspecialchars($car['gear'])) ?></span>
            <span class="px-4 py-1 bg-card-dark rounded-full text-sm border border-border"><?= $car['fuel'] === 'Diesel' ? $text['diesel'] : ($car['fuel'] === 'Petrol' ? $text['petrol'] : htmlspecialchars($car['fuel'])) ?></span>
          </div>

          <!-- Price with Discount -->
          <div class="flex flex-col items-center mb-6">
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
            <span class="inline-block px-4 py-2 bg-gradient-to-r from-gold to-yellow-500 text-white font-bold rounded-full text-sm mt-2">
              <span dir="ltr">MAD</span>/<?= $text['day'] ?>
            </span>
          </div>

          <div class="text-center mt-auto pt-4 border-t border-border/40">
            <p class="text-muted text-sm">
              <?= $text['minimum_rental'] ?>: <span class="text-gold font-bold">3 <?= $text['days'] ?></span>
            </p>
          </div>

          <div class="mt-6 space-y-4">
            <a href="<?= langUrl('booking.php', ['id' => $car['id']]) ?>" 
               class="new-book-btn block text-center text-white font-bold py-4 rounded-2xl transition transform hover:scale-105 text-lg shadow-lg">
              <?= $text['book_this_car'] ?>
            </a>

            <a href="<?= langUrl('index.php') ?>" class="block text-center border border-gold/50 text-gold hover:bg-gold/10 py-3 rounded-2xl transition text-lg">
              <?= $text['back_to_fleet'] ?>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDE – UNCHANGED -->
    <div data-aos="fade-left" class="space-y-8">
      <div class="bg-card/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-border p-10">
        <h2 class="text-3xl font-bold text-gold mb-8 text-center"><?= $text['vehicle_specifications'] ?></h2>
        <div class="grid grid-cols-2 gap-6 text-lg">
          <div class="flex items-center gap-4"><i class="bi bi-person-fill w-8 h-8 text-gold"></i><div><strong><?= $text['seats'] ?>:</strong> <span dir="ltr"><?= formatNumber($car['seats']) ?></span></div></div>
          <div class="flex items-center gap-4"><i class="bi bi-briefcase-fill w-8 h-8 text-gold"></i><div><strong><?= $text['bags'] ?>:</strong> <span dir="ltr"><?= formatNumber($car['bags']) ?></span></div></div>
          <div class="flex items-center gap-4"><i class="bi bi-gear-fill w-8 h-8 text-gold"></i><div><strong><?= $text['gearbox'] ?>:</strong> <?= ucfirst($car['gear']) ?></div></div>
          <div class="flex items-center gap-4"><i class="bi bi-fuel-pump w-8 h-8 text-gold"></i><div><strong><?= $text['fuel_requirement'] ?>:</strong> <?= $car['fuel'] ?></div></div>
          <div class="flex items-center gap-4"><i class="bi bi-calendar3 w-8 h-8 text-gold"></i><div><strong><?= $text['year'] ?>:</strong> <span dir="ltr"><?= formatNumber($car['year'] ?? 2025) ?></span></div></div>
          <div class="flex items-center gap-4"><i class="bi bi-check-circle-fill w-8 h-8 text-green-400"></i><div><strong><?= $text['status'] ?>:</strong> <span class="text-green-400 font-bold"><?= $text['available'] ?></span></div></div>
        </div>
      </div>

      <a href="https://wa.me/212772331080?text=Hi!%20I'm%20interested%20in%20the%20<?= urlencode($car['name']) ?>%20-%20<?= $hasDiscount ? formatNumber($discountedPrice) : formatNumber($originalPrice) ?>%20MAD/day%20(<?= $hasDiscount ? "-{$discount}%" : '' ?>)" 
         class="block text-center bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold text-xl py-6 rounded-2xl shadow-2xl transition transform hover:scale-105 flex items-center justify-center gap-4">
        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.198-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.134.297-.347.446-.52.149-.174.198-.297.297-.446.099-.148.05-.273-.024-.385-.074-.112-.67-1.62-.92-2.22-.246-.594-.495-.59-.67-.599-.174-.008-.371-.008-.569-.008-.197 0-.52.074-.792.372-.273.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.558 5.745 8.623 3.05.297.149.595.223.893.298.297.074.595.05.893-.025.297-.074 1.255-.52 1.43-.966.173-.446.173-.82.124-.966-.05-.148-.198-.297-.446-.446zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
        </svg>
        <?= $text['contact_whatsapp'] ?>
      </a>
    </div>
  </div>

  <!-- SIMILAR CARS SECTION – Slider -->
  <?php if ($similarCars): ?>
  <section class="mt-32">
    <h2 class="text-4xl font-bold text-center mb-16 text-gold"><?= $text['you_might_also_like'] ?></h2>
    
    <style>
      .similar-cars-slider {
        position: relative;
        overflow: hidden;
        padding: 1rem 0;
      }
      .similar-cars-track {
        display: flex;
        gap: 2rem;
        transition: transform 0.5s ease;
      }
      .similar-car-card {
        flex: 0 0 calc(25% - 1.5rem);
        min-width: 280px;
      }
      .similar-cars-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 215, 0, 0.9);
        color: #000;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        transition: all 0.3s ease;
        font-size: 24px;
        font-weight: bold;
      }
      .similar-cars-nav:hover {
        background: rgba(255, 215, 0, 1);
        transform: translateY(-50%) scale(1.1);
      }
      .similar-cars-nav.prev {
        left: 10px;
      }
      .similar-cars-nav.next {
        right: 10px;
      }
      .similar-cars-nav:disabled {
        opacity: 0.5;
        cursor: not-allowed;
      }
      @media (max-width: 1024px) {
        .similar-car-card {
          flex: 0 0 calc(33.333% - 1.33rem);
          min-width: 260px;
        }
      }
      @media (max-width: 768px) {
        .similar-car-card {
          flex: 0 0 calc(50% - 1rem);
          min-width: 240px;
        }
        .similar-cars-nav {
          width: 40px;
          height: 40px;
          font-size: 20px;
        }
      }
      @media (max-width: 640px) {
        .similar-car-card {
          flex: 0 0 100%;
          min-width: 100%;
        }
      }
      .car-image-clear {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
        image-rendering: high-quality;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        transform: translateZ(0);
        will-change: transform;
      }
    </style>

    <div class="similar-cars-slider relative max-w-7xl mx-auto px-4">
      <button class="similar-cars-nav prev" onclick="slideSimilarCars('prev')" aria-label="Previous cars">‹</button>
      <button class="similar-cars-nav next" onclick="slideSimilarCars('next')" aria-label="Next cars">›</button>
      
      <div class="similar-cars-track" id="similar-cars-track">
        <?php foreach ($similarCars as $index => $similar): 
          $s_discount = (int)($similar['discount'] ?? 0);
          $s_original = (float)$similar['price_day'];
          $s_discounted = $s_discount > 0 ? $s_original * (1 - $s_discount / 100) : $s_original;
          $s_hasDiscount = $s_discount > 0;
        ?>
          <div class="similar-car-card">
            <div class="group relative car-card-bg backdrop-blur-md rounded-3xl overflow-hidden shadow-2xl hover:shadow-gold/20 transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02] border border-border flex flex-col h-full" style="direction: ltr;">
              <div class="relative w-full pt-[56.25%] car-card-bg overflow-hidden border-b border-border">
                <img src="<?= htmlspecialchars(carImageUrl($similar['image']) ?: 'https://via.placeholder.com/600x338/36454F/FFFFFF?text=' . urlencode($similar['name'])) ?>" 
                     alt="<?= htmlspecialchars($similar['name']) ?> - ETTAAJ Rent Cars Marrakech" 
                     class="car-image-clear absolute inset-0 w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
                     loading="lazy"
                     decoding="async"
                     onerror="this.onerror=null;this.src='https://via.placeholder.com/600x338/36454F/FFFFFF?text=No+Image';this.classList.add('object-contain','p-8');">
                <?php if ($s_hasDiscount): ?>
                  <div class="absolute top-3 right-3 z-10 bg-green-600 text-white font-bold text-xs px-3 py-1.5 rounded-full shadow-lg animate-pulse" style="direction: ltr;">
                    -<?= $s_discount ?>%
                  </div>
                <?php endif; ?>
              </div>
              <div class="px-5 pb-5 sm:px-6 sm:pb-6 flex-1 flex flex-col car-card-bg" style="direction: ltr;">
                <h3 class="text-xl sm:text-2xl font-extrabold text-center mb-2 text-white line-clamp-1">
                  <?= htmlspecialchars($similar['name']) ?>
                </h3>

                <!-- Seats & Bags (Fixed Icons) -->
                <div class="flex justify-center gap-6 sm:gap-8 text-muted mb-4 text-xs sm:text-sm" style="direction: ltr;">
                  <div class="flex flex-col items-center">
                    <i class="bi bi-person-fill w-5 h-5 mb-1 text-gold"></i>
                    <span class="font-medium text-white"><span dir="ltr"><?= formatNumber($similar['seats']) ?></span> <?= $text['seats'] ?></span>
                  </div>
                  <div class="flex flex-col items-center">
                    <i class="bi bi-briefcase-fill w-5 h-5 mb-1 text-gold"></i>
                    <span class="font-medium text-white"><span dir="ltr"><?= formatNumber($similar['bags']) ?></span> <?= $text['bags'] ?></span>
                  </div>
                </div>

                <!-- Gear & Fuel -->
                <div class="flex justify-center gap-4 text-xs text-muted mb-5 font-medium" style="direction: ltr;">
                  <span class="px-3 py-1 bg-card-dark rounded-full text-primary border border-border"><?= $similar['gear'] === 'Manual' ? $text['manual'] : ($similar['gear'] === 'Automatic' ? $text['automatic'] : htmlspecialchars($similar['gear'])) ?></span>
                  <span class="px-3 py-1 bg-card-dark rounded-full text-primary border border-border"><?= $similar['fuel'] === 'Diesel' ? $text['diesel'] : ($similar['fuel'] === 'Petrol' ? $text['petrol'] : htmlspecialchars($similar['fuel'])) ?></span>
                </div>

                <!-- Price Section -->
                <div class="flex flex-col items-center mt-4 mb-3">
                  <div class="flex items-center justify-center gap-3 flex-wrap">
                    <?php if ($s_hasDiscount): ?>
                      <span class="text-2xl text-muted line-through opacity-70" dir="ltr">
                        MAD <?= formatNumber($s_original) ?>
                      </span>
                    <?php endif; ?>

                    <div class="text-4xl sm:text-5xl font-extrabold <?= $s_hasDiscount ? 'text-green-400' : 'text-white' ?>" dir="ltr">
                      <?= formatNumber($s_discounted) ?>
                    </div>
                  </div>
                  <span class="inline-block px-4 py-2 bg-gradient-to-r from-gold to-yellow-500 text-black font-bold rounded-full text-sm mt-2">
                    <span dir="ltr">MAD</span>/<?= $text['day'] ?>
                  </span>

                  <div class="flex gap-3 mt-3 text-xs font-medium">
                    <span class="px-3 py-1 bg-card-dark rounded-full border border-border text-muted">
                      <?= $text['week'] ?>: <strong class="text-primary" dir="ltr">MAD<?= formatNumber((float)$similar['price_week']) ?></strong>
                    </span>
                    <span class="px-3 py-1 bg-card-dark rounded-full border border-border text-muted">
                      <?= $text['month'] ?>: <strong class="text-primary" dir="ltr">MAD<?= formatNumber((float)$similar['price_month']) ?></strong>
                    </span>
                  </div>
                </div>

                <!-- View Details Button -->
                <div class="mt-auto">
                  <a href="<?= langUrl('car-detail.php', ['id' => (int)$similar['id']]) ?>" 
                     class="block w-full text-center bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-400
                            text-black font-bold py-3 px-6 rounded-2xl shadow-lg transition-all duration-300
                            transform hover:scale-105 active:scale-95 opacity-100"
                     style="background: linear-gradient(to right, #FFD700, #FFC107) !important;">
                    <?= $text['view_details'] ?>
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <script>
      let similarCarsCurrentIndex = 0;
      const similarCarsPerView = () => {
        if (window.innerWidth >= 1024) return 4;
        if (window.innerWidth >= 768) return 3;
        if (window.innerWidth >= 640) return 2;
        return 1;
      };

      function slideSimilarCars(direction) {
        const track = document.getElementById('similar-cars-track');
        const cards = track.querySelectorAll('.similar-car-card');
        const perView = similarCarsPerView();
        const maxIndex = Math.max(0, cards.length - perView);

        if (direction === 'next') {
          similarCarsCurrentIndex = Math.min(similarCarsCurrentIndex + 1, maxIndex);
        } else {
          similarCarsCurrentIndex = Math.max(similarCarsCurrentIndex - 1, 0);
        }

        const cardWidth = cards[0]?.offsetWidth || 0;
        const gap = 32; // 2rem = 32px
        const translateX = -(similarCarsCurrentIndex * (cardWidth + gap));
        track.style.transform = `translateX(${translateX}px)`;

        // Update button states
        document.querySelector('.similar-cars-nav.prev').disabled = similarCarsCurrentIndex === 0;
        document.querySelector('.similar-cars-nav.next').disabled = similarCarsCurrentIndex >= maxIndex;
      }

      window.addEventListener('resize', () => {
        slideSimilarCars('prev'); // Reset to start
        slideSimilarCars('next'); // Recalculate
      });

      // Initialize button states
      document.addEventListener('DOMContentLoaded', () => {
        const maxIndex = Math.max(0, document.querySelectorAll('.similar-car-card').length - similarCarsPerView());
        document.querySelector('.similar-cars-nav.prev').disabled = true;
        document.querySelector('.similar-cars-nav.next').disabled = maxIndex === 0;
      });
    </script>

    <!-- View All Cars Button -->
    <div class="text-center mt-12">
      <a href="<?= langUrl('index.php#cars') ?>" 
         class="inline-flex items-center gap-3 bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-400
                text-black font-bold py-4 px-8 rounded-2xl shadow-lg transition-all duration-300
                transform hover:scale-105 active:scale-95"
         style="background: linear-gradient(to right, #FFD700, #FFC107) !important;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <?= $text['view_all_cars'] ?>
      </a>
    </div>
  </section>
  <?php endif; ?>

</main>

<?php include 'footer.php'; ?>

<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once: true, duration: 800 });

  document.querySelector('a[href*="booking.php"]')?.addEventListener('click', function(e) {
    e.preventDefault();
    const bar = document.getElementById('tab-bar');
    bar.classList.remove('active-details');
    bar.classList.add('active-booking');
    setTimeout(() => window.location = this.href, 600);
  });
</script>
</body>
</html>