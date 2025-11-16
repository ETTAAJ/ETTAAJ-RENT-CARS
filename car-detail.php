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

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id != ? ORDER BY RAND() LIMIT 3");
$stmt->execute([$id]);
$similarCars = $stmt->fetchAll(PDO::FETCH_ASSOC);

function carImageUrl($filename, $size = 'large') {
    if (empty($filename)) return '';
    $path = 'uploads/' . basename($filename);
    $full = $_SERVER['DOCUMENT_ROOT'] . '/' . $path;
    $v = file_exists($full) ? '?v=' . filemtime($full) : '';
    return $path . $v;
}

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
    $delay  = 100 + ($index % 8) * 80;

    ob_start(); ?>
    <div data-aos="fade-up" data-aos-delay="<?= $delay ?>" data-aos-duration="700"
         class="group relative bg-[#36454F]/90 backdrop-blur-md rounded-3xl overflow-hidden shadow-2xl hover:shadow-gold/20 
                transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02] 
                border border-[#4A5A66] flex flex-col h-full">

        <div class="relative w-full pt-[56.25%] bg-[#2C3A44] overflow-hidden border-b border-[#4A5A66]">
            <img src="<?= htmlspecialchars($imgUrl) ?>"
                 alt="<?= htmlspecialchars($car['name']) ?>"
                 class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
                 onerror="this.onerror=null; this.src='https://via.placeholder.com/600x338/36454F/FFFFFF?text=No+Image'; 
                          this.classList.add('object-contain','p-8');">
        </div>

        <div class="px-5 pb-5 sm:px-6 sm:pb-6 flex-1 flex flex-col bg-[#36454F]">
            <h3 class="text-xl sm:text-2xl font-extrabold text-white mb-2 text-center line-clamp-1">
                <?= htmlspecialchars($car['name']) ?>
            </h3>

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
?>
<?php include 'header.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-12 bg-[#36454F]">
  <div class="grid md:grid-cols-2 gap-10 mb-16">
    <div data-aos="fade-right" data-aos-duration="800">
      <?php
      $imgSrc = carImageUrl($car['image']);
      $placeholder = 'https://via.placeholder.com/800x450/36454F/FFFFFF?text=' . urlencode($car['name']);
      $src = $imgSrc ?: $placeholder;
      ?>
      <div class="relative w-full pt-[56.25%] bg-[#2C3A44] rounded-3xl overflow-hidden shadow-2xl border border-[#4A5A66]">
        <img src="<?= $src ?>" 
             alt="<?= htmlspecialchars($car['name']) ?>"
             class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-700 hover:scale-105"
             onerror="this.onerror=null; this.src='https://via.placeholder.com/800x450/36454F/FFFFFF?text=No+Image'; 
                      this.classList.add('object-contain','p-8');">
      </div>
    </div>

    <div data-aos="fade-left" data-aos-duration="800" class="flex flex-col justify-center">
      <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-4"><?= htmlspecialchars($car['name']) ?></h1>
      
      <div class="grid grid-cols-2 gap-4 mb-8 text-gray-300">
        <div class="flex items-center">
          <svg class="w-6 h-6 mr-2 text-gold" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
          </svg>
          <span class="font-medium"><?= $car['seats'] ?> Seats</span>
        </div>
        <div class="flex items-center">
          <svg class="w-6 h-6 mr-2 text-gold" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5 3h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
          </svg>
          <span class="font-medium"><?= $car['bags'] ?> Bags</span>
        </div>
        <div class="px-3 py-1 bg-[#2C3A44] rounded-full text-center font-medium text-white border border-[#4A5A66]">
          <?= htmlspecialchars($car['gear']) ?>
        </div>
        <div class="px-3 py-1 bg-[#2C3A44] rounded-full text-center font-medium text-white border border-[#4A5A66]">
          <?= htmlspecialchars($car['fuel']) ?>
        </div>
      </div>

      <!-- LUXURY PRICE SECTION – DARK GLASS -->
      <div class="bg-[#36454F]/90 backdrop-blur-md p-6 rounded-3xl shadow-lg border border-[#4A5A66] mb-8">
        <h3 class="font-bold text-xl text-white mb-4 text-center">Rental Prices</h3>
        <div class="flex flex-col items-center">
          <div class="flex items-baseline gap-2 mb-4">
            <span class="text-5xl sm:text-6xl font-extrabold text-white">
              <?= number_format($car['price_day']) ?>
            </span>
            <span class="inline-flex items-center gap-1 px-4 py-1.5 text-sm font-bold text-white bg-gradient-to-r from-gold to-yellow-500 rounded-full shadow-md animate-pulse">
              <span>MAD</span>
              <span>/day</span>
            </span>
          </div>
          <div class="flex gap-4 text-sm text-gray-300 font-medium">
            <span class="px-4 py-1.5 bg-[#2C3A44] rounded-full border border-[#4A5A66]">
              Week: <strong class="text-white">MAD<?= number_format($car['price_week']) ?></strong>
            </span>
            <span class="px-4 py-1.5 bg-[#2C3A44] rounded-full border border-[#4A5A66]">
              Month: <strong class="text-white">MAD<?= number_format($car['price_month']) ?></strong>
            </span>
          </div>
        </div>
      </div>

      <a href="booking.php?id=<?= $car['id'] ?>" 
         class="block w-full text-center bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-400 
                text-white font-bold text-lg py-4 rounded-2xl shadow-xl transition-all duration-300 
                transform hover:scale-105 active:scale-95">
        Book Now
      </a>
    </div>
  </div>

  <!-- Other Cars You Might Like – HORIZONTAL CAROUSEL -->
  <?php if (!empty($similarCars)): ?>
  <div class="mt-20" data-aos="fade-up" data-aos-delay="200">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
      Other Cars You Might Like
    </h2>

    <div class="overflow-x-auto pb-4 -mx-4 px-4 scrollbar-hide">
      <div class="flex gap-6 min-w-max items-stretch">
        <?php foreach ($similarCars as $i => $similar): ?>
          <div class="w-80 flex-shrink-0">
            <?= renderCarCard($similar, $i) ?>
          </div>
        <?php endforeach; ?>

        <div class="w-80 flex-shrink-0">
          <a href="index.php" class="group block h-full">
            <div class="bg-gradient-to-br from-gold/20 to-[#2C3A44] backdrop-blur-md rounded-3xl shadow-lg 
                        hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 
                        border border-[#4A5A66] flex flex-col justify-center items-center p-8 h-full text-center">
              <div class="w-20 h-20 mb-4 rounded-full bg-gold/30 flex items-center justify-center 
                          group-hover:bg-gold/40 transition">
                <svg class="w-10 h-10 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M9 5l7 7-7 7" />
                </svg>
              </div>
              <h3 class="text-xl font-bold text-white mb-2">Browse All Cars</h3>
              <p class="text-sm text-gray-300">Explore our full premium fleet</p>
            </div>
          </a>
        </div>
      </div>
    </div>

    <div class="flex justify-center gap-2 mt-6 md:hidden">
      <?php foreach ($similarCars as $i => $s): ?>
        <div class="w-2 h-2 rounded-full bg-[#4A5A66] <?= $i === 0 ? 'bg-gold' : '' ?>"></div>
      <?php endforeach; ?>
      <div class="w-2 h-2 rounded-full bg-[#4A5A66]"></div>
    </div>
  </div>
  <?php endif; ?>
</main>

<?php include 'footer.php'; ?>

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
  body, main, .bg-white {
      background-color: var(--dark-bg) !important;
      color: white !important;
  }
  .bg-gray-100, .bg-gray-200 {
      background-color: var(--darker-bg) !important;
  }
  .border-gray-200, .border-white\/30 {
      border-color: var(--border) !important;
  }
  .text-gray-600, .text-gray-700, .text-gray-800, .text-gray-900 {
      color: white !important;
  }
  .text-gray-500 {
      color: #D1D5DB !important;
  }
  .scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }
  .scrollbar-hide::-webkit-scrollbar { display: none; }
  @media (max-width: 768px) {
    .overflow-x-auto { scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; }
    .overflow-x-auto > div > div { scroll-snap-align: start; }
  }
  .hover\:shadow-gold\/20:hover {
      box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
  }
</style>

<script>
  AOS.init({ once: true, duration: 800, easing: 'ease-out-quart' });
</script>
</body>
</html>