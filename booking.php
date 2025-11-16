<?php
require 'config.php';

/* ---------- 1. Get Car ---------- */
$id   = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);  // Fixed: PDO::FETCH_ASSOC

if (!$car) {
    header("Location: index.php");
    exit;
}

$minDays     = 3;
$pricePerDay = $car['price_day'];

/* ---------- 2. Image helper ---------- */
function carImageUrl($filename): string
{
    if (empty($filename)) return '';
    $path = 'uploads/' . basename($filename);
    $full = $_SERVER['DOCUMENT_ROOT'] . '/' . $path;
    $v    = file_exists($full) ? '?v=' . filemtime($full) : '';
    return $path . $v;
}
?>
<?php include 'header.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-12 bg-[var(--bg)]">
  <div class="text-center mb-12" data-aos="fade-up">
    <h1 class="text-4xl sm:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gold to-yellow-500 mb-3">
      Complete Your Booking
    </h1>
    <p class="text-muted text-lg">Secure your premium ride in just a few steps</p>
  </div>

  <div class="grid lg:grid-cols-2 gap-10 max-w-6xl mx-auto">
    <!-- ========== LEFT: PREMIUM CAR CARD ========== -->
    <div data-aos="fade-right" data-aos-duration="900" class="group">
      <div class="relative bg-card/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-border p-6 h-full flex flex-col overflow-hidden
                  before:absolute before:inset-0 before:bg-gradient-to-br before:from-gold/5 before:to-transparent before:rounded-3xl before:-z-10
                  transition-all duration-500 hover:shadow-[0_20px_60px_rgba(255,215,0,0.15)] hover:-translate-y-1">
        
        <!-- Gold accent top bar -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gold to-yellow-500 rounded-t-3xl"></div>

        <h3 class="text-2xl sm:text-3xl font-extrabold text-primary mb-5 text-center relative z-10">
          <?= htmlspecialchars($car['name']) ?>
        </h3>

        <!-- Car Image with zoom & glow -->
        <?php
        $imgSrc = carImageUrl($car['image']);
        $placeholder = 'https://via.placeholder.com/800x450/36454F/FFFFFF?text=' . urlencode($car['name']);
        $src = $imgSrc ?: $placeholder;
        ?>
        <div class="relative w-full pt-[56.25%] bg-card-dark rounded-2xl overflow-hidden shadow-xl mb-6 border border-border group-hover:shadow-2xl transition-all">
          <img src="<?= $src ?>"
               alt="<?= htmlspecialchars($car['name']) ?>"
               class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-110"
               onerror="this.onerror=null;this.src='https://via.placeholder.com/800x450/36454F/FFFFFF?text=No+Image';this.classList.add('object-contain','p-8');">
          <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        </div>

        <!-- Price Section -->
        <div class="flex flex-col items-center mt-auto space-y-3">
          <div class="flex items-baseline gap-3">
            <span class="text-5xl sm:text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gold to-yellow-400">
              <?= number_format($pricePerDay) ?>
            </span>
            <span class="inline-flex items-center gap-1 px-4 py-1.5 text-sm font-bold text-primary 
                         bg-gradient-to-r from-gold/20 to-yellow-500/20 rounded-full border border-gold/30 backdrop-blur-sm">
              <span>MAD</span><span class="text-xs">/day</span>
            </span>
          </div>
          <p class="text-sm text-muted font-medium">
            Minimum <span class="text-gold font-bold"><?= $minDays ?> days</span> required
          </p>
        </div>
      </div>
    </div>

    <!-- ========== RIGHT: LUXURY BOOKING FORM ========== -->
    <div data-aos="fade-left" data-aos-duration="900">
      <form id="booking-form" action="booking-process.php" method="POST"
            class="bg-card/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-border p-6 sm:p-8 space-y-7 relative overflow-hidden
                   before:absolute before:inset-0 before:bg-gradient-to-br before:from-gold/5 before:to-transparent before:rounded-3xl before:-z-10">

        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">

        <!-- Form Fields with Floating Labels -->
        <div class="space-y-6">
          <!-- Pickup Date -->
          <div class="relative">
            <input type="date" name="pickup" id="pickup" required
                   class="peer w-full p-4 bg-card-dark/80 border border-border text-primary rounded-2xl focus:ring-2 focus:ring-gold focus:border-gold transition text-sm placeholder-transparent">
            <label for="pickup" class="absolute left-4 -top-2.5 bg-card-dark px-2 text-xs font-semibold text-gold transition-all 
                       peer-placeholder-shown:text-sm peer-placeholder-shown:text-muted peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-xs peer-focus:text-gold">
              Pickup Date
            </label>
            <svg class="absolute right-4 top-4 w-5 h-5 text-gold pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>

          <!-- Return Date -->
          <div class="relative">
            <input type="date" name="return" id="return" required
                   class="peer w-full p-4 bg-card-dark/80 border border-border text-primary rounded-2xl focus:ring-2 focus:ring-gold focus:border-gold transition text-sm placeholder-transparent">
            <label for="return" class="absolute left-4 -top-2.5 bg-card-dark px-2 text-xs font-semibold text-gold transition-all 
                       peer-placeholder-shown:text-sm peer-placeholder-shown:text-muted peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-xs peer-focus:text-gold">
              Return Date
            </label>
            <svg class="absolute right-4 top-4 w-5 h-5 text-gold pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p id="date-error" class="text-red-400 text-xs mt-2 hidden">
              Return date must be at least <?= $minDays ?> days after pickup.
            </p>
          </div>

          <!-- Progress Bar (Days) -->
          <div id="progress-container" class="hidden">
            <div class="flex justify-between text-xs text-muted mb-1">
              <span>Duration</span>
              <span id="days-label">0 days</span>
            </div>
            <div class="w-full bg-card-dark/50 rounded-full h-2 overflow-hidden">
              <div id="progress-bar" class="h-full bg-gradient-to-r from-gold to-yellow-500 rounded-full transition-all duration-500" style="width: 0%"></div>
            </div>
          </div>

          <!-- Animated Total Price -->
          <div class="bg-gradient-to-r from-gold/10 to-yellow-500/10 p-6 rounded-2xl border border-gold/20 backdrop-blur-sm">
            <p class="text-sm font-semibold text-muted mb-2">Total Price</p>
            <p id="total-price" class="text-4xl sm:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gold to-yellow-400">
              MAD0
            </p>
            <p id="days-count" class="text-sm text-muted mt-1"></p>
          </div>

          <!-- Customer Info -->
          <div class="grid sm:grid-cols-2 gap-5">
            <div class="relative">
              <input type="text" name="name" required placeholder=" "
                     class="peer w-full p-4 bg-card-dark/80 border border-border text-primary rounded-2xl focus:ring-2 focus:ring-gold focus:border-gold transition text-sm">
              <label class="absolute left-4 -top-2.5 bg-card-dark px-2 text-xs font-semibold text-gold transition-all 
                         peer-placeholder-shown:text-sm peer-placeholder-shown:text-muted peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-xs peer-focus:text-gold">
                Full Name
              </label>
            </div>
            <div class="relative">
              <input type="email" name="email" required placeholder=" "
                     class="peer w-full p-4 bg-card-dark/80 border border-border text-primary rounded-2xl focus:ring-2 focus:ring-gold focus:border-gold transition text-sm">
              <label class="absolute left-4 -top-2.5 bg-card-dark px-2 text-xs font-semibold text-gold transition-all 
                         peer-placeholder-shown:text-sm peer-placeholder-shown:text-muted peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-xs peer-focus:text-gold">
                Email Address
              </label>
            </div>
          </div>

          <div class="relative">
            <input type="tel" name="phone" required placeholder=" "
                   class="peer w-full p-4 bg-card-dark/80 border border-border text-primary rounded-2xl focus:ring-2 focus:ring-gold focus:border-gold transition text-sm">
            <label class="absolute left-4 -top-2.5 bg-card-dark px-2 text-xs font-semibold text-gold transition-all 
                       peer-placeholder-shown:text-sm peer-placeholder-shown:text-muted peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-xs peer-focus:text-gold">
              Phone Number
            </label>
          </div>

          <!-- Luxury Submit Button -->
          <button type="submit"
                  class="relative w-full bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-400 
                         text-primary font-bold text-lg py-5 rounded-2xl shadow-2xl transition-all duration-300 
                         transform hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed
                         overflow-hidden group"
                  id="submit-btn" disabled>
            <span class="relative z-10 flex items-center justify-center gap-2">
              <span>Confirm Booking</span>
              <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </span>
            <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
          </button>
        </div>
      </form>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>

<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<script>
  // === AOS + Theme Sync ===
  AOS.init({ once: true, duration: 800, easing: 'ease-out-quart' });
  new MutationObserver(() => AOS.refreshHard()).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

  // === Booking Logic ===
  const pickupInput   = document.getElementById('pickup');
  const returnInput   = document.getElementById('return');
  const totalPriceEl  = document.getElementById('total-price');
  const daysCountEl   = document.getElementById('days-count');
  const daysLabelEl   = document.getElementById('days-label');
  const progressBar   = document.getElementById('progress-bar');
  const progressCont  = document.getElementById('progress-container');
  const errorEl       = document.getElementById('date-error');
  const submitBtn     = document.getElementById('submit-btn');

  const pricePerDay = <?= $pricePerDay ?>;
  const minDays     = <?= $minDays ?>;

  let currentTotal = 0;

  function animateValue(obj, start, end, duration) {
    let startTime = null;
    const step = (timestamp) => {
      if (!startTime) startTime = timestamp;
      const progress = Math.min((timestamp - startTime) / duration, 1);
      const value = Math.floor(progress * (end - start) + start);
      obj.textContent = `MAD${value.toLocaleString()}`;
      if (progress < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  }

  function validateDates() {
    const pickup = new Date(pickupInput.value);
    const ret    = new Date(returnInput.value);

    if (!pickupInput.value || !returnInput.value) {
      submitBtn.disabled = true;
      progressCont.classList.add('hidden');
      return;
    }

    const diffTime = ret - pickup;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < minDays || diffDays < 0) {
      errorEl.classList.remove('hidden');
      submitBtn.disabled = true;
      totalPriceEl.textContent = 'MAD0';
      daysCountEl.textContent = '';
      progressCont.classList.add('hidden');
      return;
    }

    errorEl.classList.add('hidden');
    const total = diffDays * pricePerDay;

    // Animate total
    if (total !== currentTotal) {
      animateValue(totalPriceEl, currentTotal, total, 600);
      currentTotal = total;
    }

    // Update days
    daysCountEl.textContent = `${diffDays} day${diffDays > 1 ? 's' : ''}`;
    daysLabelEl.textContent = `${diffDays} day${diffDays > 1 ? 's' : ''}`;

    // Progress bar
    const progress = Math.min((diffDays / 30) * 100, 100);
    progressBar.style.width = `${progress}%`;
    progressCont.classList.remove('hidden');

    submitBtn.disabled = false;
  }

  // Set min return date
  pickupInput.addEventListener('change', () => {
    const minReturn = new Date(pickupInput.value);
    minReturn.setDate(minReturn.getDate() + minDays);
    returnInput.min = minReturn.toISOString().split('T')[0];
    validateDates();
  });

  returnInput.addEventListener('change', validateDates);

  // Init
  document.addEventListener('DOMContentLoaded', () => {
    const today = new Date().toISOString().split('T')[0];
    pickupInput.min = today;
    validateDates();
  });
</script>

<style>
  input::placeholder { color: transparent; }
  input:focus::placeholder { color: #9CA3AF; }
  .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
  .scrollbar-hide::-webkit-scrollbar { display: none; }
</style>
</body>
</html>