<?php
require 'config.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    header("Location: index.php");
    exit;
}

$minDays     = 3;
$pricePerDay = (float)$car['price_day'];
?>

<?php include 'header.php'; ?>

<style>
  :root { --input-color: #000000; }
  .dark, [data-theme="dark"] { --input-color: #FFFFFF; }

  input {
    color: var(--input-color) !important;
    -webkit-text-fill-color: var(--input-color) !important;
  }
  input::placeholder { color: #666 !important; opacity: 0.7; }
  input::-webkit-datetime-edit,
  input::-webkit-datetime-edit-fields-wrapper,
  input::-webkit-datetime-edit-text,
  input::-webkit-datetime-edit-month-field,
  input::-webkit-datetime-edit-day-field,
  input::-webkit-datetime-edit-year-field {
    color: var(--input-color) !important;
  }

  .whatsapp-btn {
    background: linear-gradient(135deg, #FFD700, #FFA500) !important;
    color: #000 !important;
    font-weight: bold !important;
  }
  .whatsapp-btn:hover { background: linear-gradient(135deg, #FFA500, #FF8C00) !important; transform: scale(1.05); }
  .whatsapp-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

  @keyframes pulse-slow { 0%,100% { opacity: 1; } 50% { opacity: 0.95; } }
  .animate-pulse-slow { animation: pulse-slow 4s ease-in-out infinite; }

  @keyframes shine { 0% { background-position: -200% center; } 100% { background-position: 200% center; } }
  .animate-shine { background-position: 0% center; animation: shine 6s linear infinite; }
</style>

<main class="max-w-7xl mx-auto px-4 py-12 bg-[var(--bg)] text-[var(--text-primary)]">
  <div class="text-center mb-16" data-aos="fade-up">
    <!-- ULTRA LUXURY GOLD TITLE -->
    <h1 class="text-5xl sm:text-6xl md:text-7xl font-black tracking-tight text-transparent bg-clip-text 
               bg-gradient-to-r from-amber-400 via-yellow-500 to-orange-500 
               animate-pulse-slow drop-shadow-2xl leading-tight">
      Complete Your Booking
    </h1>
    <p class="mt-6 text-xl sm:text-2xl font-medium text-amber-400 drop-shadow-lg tracking-wider">
      <span class="inline-block animate-shine bg-gradient-to-r from-amber-300 via-yellow-400 to-amber-300 
                   bg-[length:200%_auto] bg-clip-text text-transparent">
        Premium Service • Instant Confirmation • 24/7 Support
      </span>
    </p>
  </div>

  <div class="grid lg:grid-cols-2 gap-10 max-w-6xl mx-auto">
    <!-- LEFT: CAR CARD -->
    <div data-aos="fade-right" class="group">
      <div class="bg-card/90 backdrop-blur-md rounded-3xl overflow-hidden shadow-2xl hover:shadow-gold/30 transition-all duration-500 hover:-translate-y-2 hover:scale-[1.02] border border-border">
        <?php
        $imgUrl = !empty($car['image'])
            ? 'uploads/' . basename($car['image']) . '?v=' . (file_exists($_SERVER['DOCUMENT_ROOT'] . '/uploads/' . basename($car['image'])) ? filemtime($_SERVER['DOCUMENT_ROOT'] . '/uploads/' . basename($car['image'])) : '')
            : 'https://via.placeholder.com/800x450/1a1a1a/ffffff?text=' . urlencode($car['name']);
        ?>
        <div class="relative w-full pt-[56.25%] overflow-hidden border-b border-border">
          <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($car['name']) ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
        </div>
        <div class="p-8 text-center">
          <h3 class="text-3xl font-extrabold mb-6"><?= htmlspecialchars($car['name']) ?></h3>
          <div class="grid grid-cols-2 gap-8 mb-8 text-[var(--text-primary)]">
            <div><svg class="w-10 h-10 mx-auto mb-2 text-gold" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg><p class="font-bold"><?= $car['seats'] ?> Seats</p></div>
            <div><svg class="w-10 h-10 mx-auto mb-2 text-gold" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg><p class="font-bold"><?= $car['bags'] ?> Bags</p></div>
          </div>
          <div class="flex justify-center gap-6 mb-8">
            <span class="px-6 py-2 bg-card-dark rounded-full font-bold border border-border"><?= $car['gear'] ?></span>
            <span class="px-6 py-2 bg-card-dark rounded-full font-bold border border-border"><?= $car['fuel'] ?></span>
          </div>
          <div class="space-y-4">
            <div class="flex items-center justify-center gap-4">
              <span class="text-6xl font-black"><?= number_format($car['price_day']) ?></span>
              <span class="px-6 py-3 bg-gradient-to-r from-gold to-yellow-500 text-black font-bold rounded-full shadow-lg animate-pulse">MAD/day</span>
            </div>
            <p class="text-[var(--text-muted)] pt-4 border-t border-border/50">
              Minimum rental: <span class="text-gold font-bold"><?= $minDays ?> days</span>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT: BOOKING FORM -->
    <div data-aos="fade-left">
      <form id="booking-form" class="bg-card/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-border p-8 space-y-7">
        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">

        <div class="relative">
          <input type="date" name="pickup" id="pickup" required class="peer w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold focus:border-gold transition">
          <label class="absolute left-4 -top-2.5 bg-[var(--card)] px-3 text-xs font-bold text-gold peer-placeholder-shown:text-base peer-placeholder-shown:text-[var(--text-muted)] peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-xs transition-all pointer-events-none">
            Pickup Date
          </label>
        </div>

        <div class="relative">
          <input type="date" name="return" id="return" required class="peer w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold focus:border-gold transition">
          <label class="absolute left-4 -top-2.5 bg-[var(--card)] px-3 text-xs font-bold text-gold peer-placeholder-shown:text-base peer-placeholder-shown:text-[var(--text-muted)] peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-xs transition-all pointer-events-none">
            Return Date
          </label>
          <p id="date-error" class="text-red-400 text-sm mt-2 hidden">Return date must be at least <?= $minDays ?> days after pickup.</p>
        </div>

        <div class="bg-gradient-to-r from-gold/10 to-yellow-500/10 p-7 rounded-2xl border border-gold/30 text-center">
          <p class="text-gold font-bold mb-3 text-lg">Total Estimated Price</p>
          <p id="total-price" class="text-5xl font-black text-[var(--text-primary)]">MAD0</p>
          <p id="days-count" class="text-[var(--text-muted)] mt-2 text-lg"></p>
        </div>

        <input type="text" name="name" required placeholder="Full Name" class="w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold">
        <input type="email" name="email" required placeholder="Email Address" class="w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold">
        <input type="tel" name="phone" required placeholder="Phone (WhatsApp)" class="w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold">

        <button type="submit" id="submit-btn" disabled class="whatsapp-btn w-full py-6 rounded-2xl shadow-2xl transition-all duration-300 flex items-center justify-center gap-4 text-xl font-bold">
          <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.198-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.134.297-.347.446-.52.149-.174.198-.297.297-.446.099-.148.05-.273-.024-.385-.074-.112-.67-1.62-.92-2.22-.246-.594-.495-.59-.67-.599-.174-.008-.371-.008-.569-.008-.197 0-.52.074-.792.372-.273.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.558 5.745 8.623 8.05.297.149.595.223.893.298.297.074.595.05.893-.025.297-.074 1.255-.52 1.43-.966.173-.446.173-.82.124-.966-.05-.148-.198-.297-.446-.446zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
          Send Booking via WhatsApp
        </button>
      </form>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>

<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once: true, duration: 800 });

  // Auto color switch
  function updateColors() {
    const isDark = document.documentElement.classList.contains('dark') || document.body.getAttribute('data-theme') === 'dark';
    document.documentElement.style.setProperty('--input-color', isDark ? '#FFFFFF' : '#000000');
  }
  updateColors();
  new MutationObserver(updateColors).observe(document.documentElement, { attributes: true });

  // Elements
  const pickup = document.getElementById('pickup');
  const ret = document.getElementById('return');
  const totalEl = document.getElementById('total-price');
  const daysEl = document.getElementById('days-count');
  const error = document.getElementById('date-error');
  const btn = document.getElementById('submit-btn');
  const form = document.getElementById('booking-form');
  const pricePerDay = <?= $pricePerDay ?>;
  const minDays = <?= $minDays ?>;

  function updateTotal() {
    if (!pickup.value || !ret.value) { btn.disabled = true; return; }
    const days = Math.ceil((new Date(ret.value) - new Date(pickup.value)) / 86400000);
    if (days < minDays || days <= 0) {
      error.classList.remove('hidden');
      btn.disabled = true;
      totalEl.textContent = 'MAD0';
      daysEl.textContent = '';
      return;
    }
    error.classList.add('hidden');
    const total = days * pricePerDay;
    totalEl.textContent = 'MAD' + total.toLocaleString();
    daysEl.textContent = days + ' day' + (days > 1 ? 's' : '');
    btn.disabled = false;
  }

  pickup.addEventListener('change', () => {
    const minReturn = new Date(pickup.value);
    minReturn.setDate(minReturn.getDate() + minDays);
    ret.min = minReturn.toISOString().split('T')[0];
    updateTotal();
  });
  ret.addEventListener('change', updateTotal);

  // SUBMIT + CLEAR FORM AFTER SENDING
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const days = Math.ceil((new Date(ret.value) - new Date(pickup.value)) / 86400000);
    const total = days * pricePerDay;
    const msg = `NEW BOOKING - ETTAAJ RENT CARS\n\nCar: <?= htmlspecialchars($car['name']) ?>\nPickup: ${pickup.value}\nReturn: ${ret.value}\nDuration: ${days} days\nTotal: MAD${total.toLocaleString()}\n\nName: ${form.name.value}\nEmail: ${form.email.value}\nPhone: ${form.phone.value}\n\nPlease confirm availability!`;

    window.open(`https://wa.me/212772331080?text=${encodeURIComponent(msg)}`, '_blank');

    // SUCCESS MESSAGE
    alert('Thank you! Your booking has been sent via WhatsApp. We will contact you immediately!');

    // CLEAR ALL FIELDS
    form.reset();
    totalEl.textContent = 'MAD0';
    daysEl.textContent = '';
    btn.disabled = true;
    error.classList.add('hidden');
  });

  document.addEventListener('DOMContentLoaded', () => {
    pickup.min = new Date().toISOString().split('T')[0];
  });
</script>
</body>
</html>