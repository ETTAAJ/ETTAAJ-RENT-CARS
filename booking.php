<?php
require 'config.php';

// --- 1. Get Car ---
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    header("Location: index.php");
    exit;
}

$minDays = 3;
$pricePerDay = $car['price_day'];

/* -------------------------------------------------
   IMAGE HELPER – SAME AS EVERYWHERE
   ------------------------------------------------- */
function carImageUrl($filename) {
    if (empty($filename)) return '';
    $path = 'uploads/' . basename($filename);
    $full = $_SERVER['DOCUMENT_ROOT'] . '/' . $path;
    $v = file_exists($full) ? '?v=' . filemtime($full) : '';
    return $path . $v;
}
?>
<?php include 'header.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-12 bg-[#36454F]">
  <h1 class="text-3xl sm:text-4xl font-extrabold text-center mb-12 text-white" data-aos="fade-up">
    Complete Your Booking
  </h1>

  <div class="grid lg:grid-cols-2 gap-10 max-w-6xl mx-auto">
    <!-- Car Info Card -->
    <div data-aos="fade-right" data-aos-duration="800">
      <div class="bg-[#36454F]/90 backdrop-blur-md rounded-3xl shadow-2xl border border-[#4A5A66] p-6 h-full flex flex-col">
        <h3 class="text-xl sm:text-2xl font-extrabold text-white mb-4 text-center">
          <?= htmlspecialchars($car['name']) ?>
        </h3>

        <!-- Car Image (16:9) -->
        <?php
        $imgSrc = carImageUrl($car['image']);
        $placeholder = 'https://via.placeholder.com/800x450/36454F/FFFFFF?text=' . urlencode($car['name']);
        $src = $imgSrc ?: $placeholder;
        ?>
        <div class="relative w-full pt-[56.25%] bg-[#2C3A44] rounded-2xl overflow-hidden shadow-lg mb-5 border border-[#4A5A66]">
          <img src="<?= $src ?>"
               alt="<?= htmlspecialchars($car['name']) ?>"
               class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-700 hover:scale-105"
               onerror="this.onerror=null; this.src='https://via.placeholder.com/800x450/36454F/FFFFFF?text=No+Image'; 
                        this.classList.add('object-contain','p-8');">
        </div>

        <!-- Price + Min Days -->
        <div class="flex flex-col items-center mt-auto">
          <div class="flex items-baseline gap-2 mb-2">
            <span class="text-4xl sm:text-5xl font-extrabold text-white">
              <?= number_format($pricePerDay) ?>
            </span>
            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold text-white bg-gradient-to-r from-gold to-yellow-500 rounded-full shadow-md animate-pulse">
              <span>MAD</span>
              <span>/day</span>
            </span>
          </div>
          <p class="text-xs text-gray-300 font-medium">
            Minimum <strong class="text-gold"><?= $minDays ?> days</strong> required
          </p>
        </div>
      </div>
    </div>

    <!-- Booking Form Card -->
    <div data-aos="fade-left" data-aos-duration="800">
      <form id="booking-form" action="booking-process.php" method="POST" 
            class="bg-[#36454F]/90 backdrop-blur-md rounded-3xl shadow-2xl border border-[#4A5A66] p-6 sm:p-8 space-y-6">

        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">

        <!-- Pickup Date -->
        <div>
          <label class="block text-sm font-bold text-white mb-2">Pickup Date</label>
          <input type="date" name="pickup" id="pickup" required
                 class="w-full p-3 bg-[#2C3A44] border border-[#4A5A66] text-white rounded-xl focus:ring-2 focus:ring-gold focus:border-gold transition text-sm placeholder-gray-400">
        </div>

        <!-- Return Date -->
        <div>
          <label class="block text-sm font-bold text-white mb-2">Return Date</label>
          <input type="date" name="return" id="return" required
                 class="w-full p-3 bg-[#2C3A44] border border-[#4A5A66] text-white rounded-xl focus:ring-2 focus:ring-gold focus:border-gold transition text-sm placeholder-gray-400">
          <p id="date-error" class="text-red-400 text-xs mt-1 hidden">
            Return date must be at least <?= $minDays ?> days after pickup.
          </p>
        </div>

        <!-- Live Total Price -->
        <div class="bg-gradient-to-r from-gold/10 to-[#2C3A44] p-5 rounded-2xl border border-gold/30">
          <p class="text-sm font-semibold text-gray-300 mb-1">Total Price</p>
          <p id="total-price" class="text-3xl sm:text-4xl font-extrabold text-gold">MAD0</p>
          <p id="days-count" class="text-sm text-gray-300 mt-1"></p>
        </div>

        <!-- Customer Info -->
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-bold text-white mb-2">Full Name</label>
            <input type="text" name="name" required placeholder="John Doe"
                   class="w-full p-3 bg-[#2C3A44] border border-[#4A5A66] text-white rounded-xl focus:ring-2 focus:ring-gold placeholder-gray-400 text-sm">
          </div>
          <div>
            <label class="block text-sm font-bold text-white mb-2">Email</label>
            <input type="email" name="email" required placeholder="john@example.com"
                   class="w-full p-3 bg-[#2C3A44] border border-[#4A5A66] text-white rounded-xl focus:ring-2 focus:ring-gold placeholder-gray-400 text-sm">
          </div>
        </div>

        <div>
          <label class="block text-sm font-bold text-white mb-2">Phone</label>
          <input type="tel" name="phone" required placeholder="+212 6 00 00 00 00"
                 class="w-full p-3 bg-[#2C3A44] border border-[#4A5A66] text-white rounded-xl focus:ring-2 focus:ring-gold placeholder-gray-400 text-sm">
        </div>

        <!-- Submit Button -->
        <button type="submit"
                class="w-full bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-400 
                       text-white font-bold text-lg py-4 rounded-2xl shadow-xl transition-all duration-300 
                       transform hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                id="submit-btn" disabled>
          Confirm Booking
        </button>
      </form>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>

<!-- AOS + JS Logic -->
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<style>
  :root {
      --dark-bg: #36454F;
      --darker-bg: #2C3A44;
      --border: #4A5A66;
  }
  body, main {
      background-color: var(--dark-bg) !important;
      color: white !important;
  }
  .bg-white\/80, .bg-white {
      background-color: var(--dark-bg) !important;
  }
  .border-white\/30, .border-gray-300 {
      border-color: var(--border) !important;
  }
  .text-gray-900, .text-gray-800, .text-gray-700 {
      color: white !important;
  }
  .text-gray-600, .text-gray-500 {
      color: #D1D5DB !important;
  }
  input, textarea {
      background-color: var(--darker-bg) !important;
      color: white !important;
  }
  input::placeholder {
      color: #9CA3AF !important;
  }
</style>

<script>
  AOS.init({ once: true, duration: 800, easing: 'ease-out-quart' });

  const pickupInput = document.getElementById('pickup');
  const returnInput = document.getElementById('return');
  const totalPriceEl = document.getElementById('total-price');
  const daysCountEl = document.getElementById('days-count');
  const errorEl = document.getElementById('date-error');
  const submitBtn = document.getElementById('submit-btn');

  const pricePerDay = <?= $pricePerDay ?>;
  const minDays = <?= $minDays ?>;

  function validateDates() {
    const pickup = new Date(pickupInput.value);
    const ret = new Date(returnInput.value);

    if (!pickupInput.value || !returnInput.value) {
      submitBtn.disabled = true;
      return;
    }

    const diffTime = ret - pickup;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < minDays || diffDays < 0) {
      errorEl.classList.remove('hidden');
      submitBtn.disabled = true;
      totalPriceEl.textContent = 'MAD0';
      daysCountEl.textContent = '';
      return;
    }

    errorEl.classList.add('hidden');
    const total = diffDays * pricePerDay;
    totalPriceEl.textContent = `MAD${total.toLocaleString()}`;
    daysCountEl.textContent = `${diffDays} day${diffDays > 1 ? 's' : ''}`;
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

  // Initialize
  document.addEventListener('DOMContentLoaded', () => {
    const today = new Date().toISOString().split('T')[0];
    pickupInput.min = today;
    validateDates();
  });
</script>
</body>
</html>