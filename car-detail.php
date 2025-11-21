<?php
require 'config.php';

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

// Fixed: Removed non-existent 'available' column
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
?>

<?php include 'header.php'; ?>

<style>
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
  .tab-item.active { color: #000; }
  .tab-item:not(.active) { color: rgba(255,255,255,0.75); }
  .tab-item:hover:not(.active) { color: #FFD700; }

  @media (max-width: 640px) {
    .tab-item { padding: 14px 8px; font-size: 1rem; }
    .tab-item svg { width: 22px; height: 22px; }
    .tab-item span { display: block; margin-top: 6px; font-size: 0.8rem; }
  }
</style>

<main class="max-w-7xl mx-auto px-4 py-12 bg-[var(--bg)] text-[var(--text-primary)]">

  <!-- LUXURY 2-TAB BAR -->
  <div class="max-w-3xl mx-auto mb-16">
    <div class="tab-bar active-details" id="tab-bar">
      <div class="flex">
        <a href="car-detail.php?id=<?= $car['id'] ?>" class="tab-item flex items-center justify-center active">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h-4m-6 0H5"/>
          </svg>
          <span>Car Details</span>
        </a>
        <a href="booking.php?id=<?= $car['id'] ?>" class="tab-item flex items-center justify-center">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <span>Booking Details</span>
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
      Luxury • Performance • Unforgettable Experience
    </p>
  </div>

  <div class="grid lg:grid-cols-2 gap-10 max-w-6xl mx-auto">

    <!-- LEFT: LUXURY CARD (100% SAME AS BOOKING PAGE) -->
    <div data-aos="fade-right" class="h-full">
      <div class="group relative bg-card/90 backdrop-blur-md rounded-3xl overflow-hidden shadow-2xl hover:shadow-gold/20 transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02] border border-border flex flex-col h-full">

        <div class="relative w-full pt-[56.25%] bg-card-dark overflow-hidden border-b border-border">
          <?php
          $imgUrl = !empty($car['image'])
              ? carImageUrl($car['image'])
              : 'https://via.placeholder.com/800x450/36454F/FFFFFF?text=' . urlencode($car['name']);
          ?>
          <img src="<?= htmlspecialchars($imgUrl) ?>" 
               alt="<?= htmlspecialchars($car['name']) ?>" 
               class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
        </div>

        <div class="p-6 flex-1 flex flex-col">
          <h3 class="text-2xl font-extrabold text-center mb-4"><?= htmlspecialchars($car['name']) ?></h3>

          <div class="flex justify-center gap-8 text-sm mb-4">
            <div class="text-center">
              <svg class="w-6 h-6 mx-auto mb-1 text-gold" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
              </svg>
              <span><?= $car['seats'] ?> Seats</span>
            </div>
            <div class="text-center">
              <svg class="w-6 h-6 mx-auto mb-1 text-gold" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 3h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
              </svg>
              <span><?= $car['bags'] ?> Bags</span>
            </div>
          </div>

          <div class="flex justify-center gap-4 mb-6">
            <span class="px-4 py-1 bg-card-dark rounded-full text-sm border border-border"><?= htmlspecialchars($car['gear']) ?></span>
            <span class="px-4 py-1 bg-card-dark rounded-full text-sm border border-border"><?= htmlspecialchars($car['fuel']) ?></span>
          </div>

          <div class="text-center mb-6">
            <div class="text-5xl font-black"><?= number_format($car['price_day']) ?></div>
            <span class="inline-block px-4 py-2 bg-gradient-to-r from-gold to-yellow-500 text-black font-bold rounded-full text-sm mt-2">
              MAD/day
            </span>
          </div>

          <div class="text-center mt-auto pt-4 border-t border-border/40">
            <p class="text-[var(--text-muted)] text-sm">
              Minimum rental: <span class="text-gold font-bold">3 days</span>
            </p>
          </div>

          <div class="mt-6 space-y-4">
            <a href="booking.php?id=<?= $car['id'] ?>" 
               class="block text-center bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-400 text-black font-bold py-4 rounded-2xl transition transform hover:scale-105 text-lg">
              Book This Car Now
            </a>
            <a href="index.php" class="block text-center border border-gold/50 text-gold hover:bg-gold/10 py-3 rounded-2xl transition text-lg">
              Back to Fleet
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT: SPECIFICATIONS + WHATSAPP -->
    <div data-aos="fade-left" class="space-y-8">
      <div class="bg-card/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-border p-10">
        <h2 class="text-3xl font-bold text-gold mb-8 text-center">Vehicle Specifications</h2>
        <div class="grid grid-cols-2 gap-6 text-lg">
          <div class="flex items-center gap-4"><svg class="w-8 h-8 text-gold" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg><div><strong>Seats:</strong> <?= $car['seats'] ?></div></div>
          <div class="flex items-center gap-4"><svg class="w-8 h-8 text-gold" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg><div><strong>Bags:</strong> <?= $car['bags'] ?></div></div>
          <div class="flex items-center gap-4"><svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg><div><strong>Gearbox:</strong> <?= ucfirst($car['gear']) ?></div></div>
          <div class="flex items-center gap-4"><svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg><div><strong>Fuel:</strong> <?= $car['fuel'] ?></div></div>
          <div class="flex items-center gap-4"><svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg><div><strong>Year:</strong> <?= $car['year'] ?? '2025' ?></div></div>
          <div class="flex items-center gap-4"><svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><div><strong>Status:</strong> <span class="text-green-400 font-bold">Available</span></div></div>
        </div>
      </div>

      <a href="https://wa.me/212772331080?text=Hi!%20I'm%20interested%20in%20the%20<?= urlencode($car['name']) ?>%20-%20<?= number_format($car['price_day']) ?>%20MAD/day" 
         class="block text-center bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold text-xl py-6 rounded-2xl shadow-2xl transition transform hover:scale-105 flex items-center justify-center gap-4">
        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.198-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.134.297-.347.446-.52.149-.174.198-.297.297-.446.099-.148.05-.273-.024-.385-.074-.112-.67-1.62-.92-2.22-.246-.594-.495-.59-.67-.599-.174-.008-.371-.008-.569-.008-.197 0-.52.074-.792.372-.273.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.558 5.745 8.623 8.05.297.149.595.223.893.298.297.074.595.05.893-.025.297-.074 1.255-.52 1.43-.966.173-.446.173-.82.124-.966-.05-.148-.198-.297-.446-.446zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
        </svg>
        Contact via WhatsApp Now
      </a>
    </div>
  </div>

  <!-- SIMILAR CARS SECTION -->
  <?php if ($similarCars): ?>
  <section class="mt-32">
    <h2 class="text-4xl font-bold text-center mb-16 text-gold">You Might Also Like</h2>
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
      <?php foreach ($similarCars as $index => $similar): ?>
        <div data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
          <div class="group relative bg-card/90 backdrop-blur-md rounded-3xl overflow-hidden shadow-2xl hover:shadow-gold/20 transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02] border border-border flex flex-col h-full">
            <div class="relative w-full pt-[56.25%] bg-card-dark overflow-hidden border-b border-border">
              <img src="<?= htmlspecialchars(carImageUrl($similar['image']) ?: 'https://via.placeholder.com/600x338/36454F/FFFFFF?text=' . urlencode($similar['name'])) ?>" 
                   alt="<?= htmlspecialchars($similar['name']) ?>" 
                   class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>
            <div class="p-6 flex-1 flex flex-col">
              <h3 class="text-xl font-extrabold text-center mb-3"><?= htmlspecialchars($similar['name']) ?></h3>
              <div class="text-center mb-4">
                <div class="text-4xl font-black"><?= number_format($similar['price_day']) ?></div>
                <span class="inline-block px-3 py-1 bg-gradient-to-r from-gold to-yellow-500 text-black font-bold rounded-full text-xs">MAD/day</span>
              </div>
              <div class="mt-auto">
                <a href="car-detail.php?id=<?= $similar['id'] ?>" 
                   class="block text-center bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-400 text-black font-bold py-3 rounded-2xl transition transform hover:scale-105">
                  View Details
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
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