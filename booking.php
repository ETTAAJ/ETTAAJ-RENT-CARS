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

$minDays = 3;

// === DISCOUNT LOGIC ===
$discountPercent = (int)($car['discount'] ?? 0);
$originalPricePerDay = (float)$car['price_day'];
$discountedPricePerDay = $discountPercent > 0 
    ? $originalPricePerDay * (1 - $discountPercent / 100) 
    : $originalPricePerDay;
$hasDiscount = $discountPercent > 0;
?>

<?php include 'header.php'; ?>

<style>
  :root { --input-color: #000000; }
  .dark, [data-theme="dark"] { --input-color: #FFFFFF; }

  input { color: var(--input-color) !important; -webkit-text-fill-color: var(--input-color) !important; }
  input::placeholder { color: #666 !important; opacity: 0.7; }

  .whatsapp-btn {
    background: linear-gradient(135deg, #FFD700, #FFA500) !important;
    color: #000 !important;
    font-weight: bold !important;
  }
  .whatsapp-btn:hover { background: linear-gradient(135deg, #FFA500, #FF8C00) !important; transform: scale(1.05); }
  .whatsapp-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

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
    bottom: 0;
    left: 0;
    width: 50%;
    height: 5px;
    background: linear-gradient(90deg, #FFD700, #FFA500);
    border-radius: 3px;
    transition: transform 0.65s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.5s ease;
    transform: translateX(100%);
    box-shadow: 0 0 20px rgba(255, 215, 0, 0.7);
  }
  .tab-bar.active-details::before { transform: translateX(0%); }
  .tab-bar.active-booking::before { transform: translateX(100%); }

  .tab-item {
    flex: 1;
    padding: 18px 12px;
    text-align: center;
    font-weight: 700;
    font-size: 1.15rem;
    border-radius: 12px;
    transition: all 0.4s ease;
    position: relative;
    z-index: 10;
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

  .insurance-option, .extra-option {
    transition: all 0.4s ease;
    cursor: pointer;
    position: relative;
  }
  .insurance-option:hover, .extra-option:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(255, 215, 0, 0.2) !important;
  }
  .insurance-option input[type="radio"], .extra-option input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
  }
  .insurance-option input[type="radio"]:checked + label,
  .extra-option input[type="checkbox"]:checked + label {
    border: 3px solid #FFD700;
    background: linear-gradient(135deg, rgba(255,215,0,0.15), rgba(255,165 0,0.1));
    box-shadow: 0 0 30px rgba(255,215,0,0.4);
  }

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
  }
  @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }

  .extra-toggle {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
  }
  .extra-toggle input { opacity: 0; width: 0; height: 0; }
  .slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #374151;
    transition: .4s;
    border-radius: 34px;
  }
  .slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
  }
  input:checked + .slider {
    background-color: #10b981;
  }
  input:checked + .slider:before {
    transform: translateX(24px);
  }
</style>

<main class="max-w-7xl mx-auto px-4 py-12 bg-[var(--bg)] text-[var(--text-primary)]">

  <!-- TAB BAR -->
  <div class="max-w-3xl mx-auto mb-16">
    <div class="tab-bar active-booking" id="tab-bar">
      <div class="flex">
        <a href="car-detail.php?id=<?= $car['id'] ?>" id="details-tab-link" class="tab-item flex items-center justify-center">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h-4m-6 0H5"/>
          </svg>
          <span>Car Details</span>
        </a>
        <a href="booking.php?id=<?= $car['id'] ?>" class="tab-item flex items-center justify-center active">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <span>Booking Details</span>
        </a>
      </div>
    </div>
  </div>

  <div class="text-center mb-16" data-aos="fade-up">
    <h1 class="text-5xl sm:text-6xl md:text-7xl font-black tracking-tight text-transparent bg-clip-text 
               bg-gradient-to-r from-amber-400 via-yellow-500 to-orange-500 drop-shadow-2xl leading-tight">
      Complete Your Booking
    </h1>
    <p class="mt-6 text-xl sm:text-2xl font-medium text-amber-400 drop-shadow-lg tracking-wider">
      Premium Service • Instant Confirmation • 24/7 Support
    </p>
  </div>

  <div class="grid lg:grid-cols-2 gap-10 max-w-6xl mx-auto">

    <!-- LEFT: CAR CARD -->
    <div data-aos="fade-right" class="h-full">
      <div class="group relative bg-card/90 backdrop-blur-md rounded-3xl overflow-hidden shadow-2xl hover:shadow-gold/20 transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02] border border-border flex flex-col h-full">
        <div class="relative w-full pt-[56.25%] bg-card-dark overflow-hidden border-b border-border">
          <?php
          $imgUrl = !empty($car['image'])
              ? 'uploads/' . basename($car['image']) . '?v=' . (file_exists($_SERVER['DOCUMENT_ROOT'] . '/uploads/' . basename($car['image'])) ? filemtime($_SERVER['DOCUMENT_ROOT'] . '/uploads/' . basename($car['image'])) : '')
              : 'https://via.placeholder.com/800x450/36454F/FFFFFF?text=' . urlencode($car['name']);
          ?>
          <img src="<?= htmlspecialchars($imgUrl) ?>" 
               alt="<?= htmlspecialchars($car['name']) ?>" 
               class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
          <?php if ($hasDiscount): ?>
            <div class="discount-badge">-<?= $discountPercent ?>%</div>
          <?php endif; ?>
        </div>

        <div class="p-6 flex-1 flex flex-col">
          <h3 class="text-2xl font-extrabold text-center mb-4"><?= htmlspecialchars($car['name']) ?></h3>
          <div class="flex justify-center gap-8 text-sm mb-4">
            <div class="text-center">
              <i class="bi bi-person-fill w-6 h-6 mx-auto mb-1 text-gold"></i>
              <span><?= $car['seats'] ?> Seats</span>
            </div>
            <div class="text-center">
              <i class="bi bi-briefcase-fill w-6 h-6 mx-auto mb-1 text-gold"></i>
              <span><?= $car['bags'] ?> Bags</span>
            </div>
          </div>
          <div class="flex justify-center gap-4 mb-6">
            <span class="px-4 py-1 bg-card-dark rounded-full text-sm border border-border"><?= htmlspecialchars($car['gear']) ?></span>
            <span class="px-4 py-1 bg-card-dark rounded-full text-sm border border-border"><?= htmlspecialchars($car['fuel']) ?></span>
          </div>

          <div class="text-center mb-6">
            <div class="flex items-center justify-center gap-4 flex-wrap">
              <?php if ($hasDiscount): ?>
                <span class="text-3xl text-[var(--text-muted)] line-through opacity-70">
                  MAD <?= number_format($originalPricePerDay) ?>
                </span>
              <?php endif; ?>
              <div class="text-5xl font-black <?= $hasDiscount ? 'text-green-400' : '' ?>">
                <?= number_format($discountedPricePerDay) ?>
              </div>
            </div>
            <span class="inline-block px-4 py-2 bg-gradient-to-r from-gold to-yellow-500 text-black font-bold rounded-full text-sm mt-2">
              MAD/day
            </span>
          </div>

          <div class="text-center mt-auto pt-4 border-t border-border/40">
            <p class="text-[var(--text-muted)] text-sm">
              Minimum rental: <span class="text-gold font-bold"><?= $minDays ?> days</span>
            </p>
          </div>

          <div class="mt-6">
            <a href="car-detail.php?id=<?= $car['id'] ?>" 
               class="block text-center bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-400 text-black font-bold py-3 rounded-2xl transition transform hover:scale-105">
              View Full Details
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT: BOOKING FORM -->
    <div data-aos="fade-left">
      <form id="booking-form" class="bg-card/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-border p-8 space-y-7">
        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">

        <!-- DATES -->
        <div class="relative">
          <input type="date" name="pickup" id="pickup" required class="peer w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold focus:border-gold transition">
          <label class="absolute left-4 -top-2.5 bg-[var(--card)] px-3 text-xs font-bold text-gold peer-placeholder-shown:text-base peer-placeholder-shown:text-[var(--text-muted)] peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-xs transition-all pointer-events-none">Pickup Date</label>
        </div>

        <div class="relative">
          <input type="date" name="return" id="return" required class="peer w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold focus:border-gold transition">
          <label class="absolute left-4 -top-2.5 bg-[var(--card)] px-3 text-xs font-bold text-gold peer-placeholder-shown:text-base peer-placeholder-shown:text-[var(--text-muted)] peer-placeholder-shown:top-4 peer-focus:-top-2.5 peer-focus:text-xs transition-all pointer-events-none">Return Date</label>
          <p id="date-error" class="text-red-400 text-sm mt-2 hidden">Return date must be at least <?= $minDays ?> days after pickup.</p>
        </div>

        <!-- PROTECTION PLAN -->
        <div class="space-y-4">
          <h3 class="text-2xl font-bold text-gold text-center mb-6">Protection Plan</h3>
          <div class="grid gap-4">
            <div class="insurance-option">
              <input type="radio" name="insurance" id="basic" value="Basic Insurance - Included" checked>
              <label for="basic" class="block p-6 bg-card-dark/80 rounded-2xl border border-border hover:border-gold/50 transition-all cursor-pointer">
                <div class="flex justify-between items-start mb-3">
                  <div>
                    <h4 class="text-xl font-bold">Basic Insurance</h4>
                    <p class="text-sm text-green-400">Standard coverage included</p>
                  </div>
                  <span class="text-2xl font-black text-gold">FREE</span>
                </div>
                <p class="text-sm text-[var(--text-muted)] mb-3">Deposit: <strong>$588.00</strong></p>
              </label>
            </div>
            <div class="insurance-option">
              <input type="radio" name="insurance" id="smart" value="Smart Insurance - +$8.90/day">
              <label for="smart" class="block p-6 bg-card-dark/80 rounded-2xl border border-border hover:border-gold/50 transition-all cursor-pointer">
                <div class="flex justify-between items-start mb-3">
                  <div>
                    <h4 class="text-xl font-bold">Smart Insurance</h4>
                    <p class="text-sm text-amber-400">Reduced excess</p>
                  </div>
                  <span class="text-2xl font-black text-gold">+$8.90/day</span>
                </div>
                <p class="text-sm text-[var(--text-muted)] mb-3">Deposit: <strong>$294.00</strong></p>
              </label>
            </div>
            <div class="insurance-option">
              <input type="radio" name="insurance" id="premium" value="Premium Insurance - +$14.40/day">
              <label for="premium" class="block p-6 bg-card-dark/80 rounded-2xl border border-border hover:border-gold/50 transition-all cursor-pointer">
                <div class="flex justify-between items-start mb-3">
                  <div>
                    <h4 class="text-xl font-bold">Premium Insurance</h4>
                    <p class="text-sm text-amber-400">Zero excess</p>
                  </div>
                  <span class="text-2xl font-black text-gold">+$14.40/day</span>
                </div>
                <p class="text-sm text-[var(--text-muted)] mb-3">Deposit: <strong>$98.00</strong></p>
              </label>
            </div>
          </div>
        </div>

        <!-- TRAVEL ESSENTIALS (ONLY REMAINING ITEMS) -->
        <div class="space-y-4 pt-6 border-t border-border/50">
          <h3 class="text-2xl font-bold text-gold text-center mb-6">Travel Essentials</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div class="extra-option">
              <input type="checkbox" id="fuel" value="Premium Fuel Service - $110.00/rental">
              <label for="fuel" class="flex items-center justify-between p-5 bg-card-dark/80 rounded-2xl border border-border hover:border-gold/50 transition-all cursor-pointer">
                <div class="flex items-center gap-4">
                  <i class="bi bi-fuel-pump text-2xl text-gold"></i>
                  <div>
                    <div class="font-bold">Premium Fuel Service</div>
                    <div class="text-sm text-[var(--text-muted)]">$110.00/rental</div>
                    <div class="text-xs text-[var(--text-muted)]">Prepaid full tank</div>
                  </div>
                </div>
                <div class="extra-toggle">
                  <input type="checkbox">
                  <span class="slider"></span>
                </div>
              </label>
            </div>

            <div class="extra-option">
              <input type="checkbox" id="unlimited" value="Unlimited Kilometers - $10.50/day">
              <label for="unlimited" class="flex items-center justify-between p-5 bg-card-dark/80 rounded-2xl border border-border hover:border-gold/50 transition-all cursor-pointer">
                <div class="flex items-center gap-4">
                  <i class="bi bi-infinity text-2xl text-gold"></i>
                  <div>
                    <div class="font-bold">Unlimited Kilometers</div>
                    <div class="text-sm text-[var(--text-muted)]">$10.50/day</div>
                    <div class="text-xs text-[var(--text-muted)]">Drive without mileage restrictions</div>
                  </div>
                </div>
                <div class="extra-toggle">
                  <input type="checkbox">
                  <span class="slider"></span>
                </div>
              </label>
            </div>

            <div class="extra-option">
              <input type="checkbox" id="cancellation" value="Flexible Cancellation - $9.50/rental">
              <label for="cancellation" class="flex items-center justify-between p-5 bg-card-dark/80 rounded-2xl border border-border hover:border-gold/50 transition-all cursor-pointer">
                <div class="flex items-center gap-4">
                  <i class="bi bi-x-circle text-2xl text-gold"></i>
                  <div>
                    <div class="font-bold">Flexible Cancellation</div>
                    <div class="text-sm text-[var(--text-muted)]">$9.50/rental</div>
                    <div class="text-xs text-[var(--text-muted)]">Free cancellation until scheduled departure</div>
                  </div>
                </div>
                <div class="extra-toggle">
                  <input type="checkbox">
                  <span class="slider"></span>
                </div>
              </label>
            </div>

            <div class="extra-option sm:col-span-2">
              <input type="checkbox" id="drivers" value="Additional Drivers - $2.50/day">
              <label for="drivers" class="flex items-center justify-between p-5 bg-card-dark/80 rounded-2xl border border-border hover:border-gold/50 transition-all cursor-pointer">
                <div class="flex items-center gap-4">
                  <i class="bi bi-people text-2xl text-gold"></i>
                  <div>
                    <div class="font-bold">Additional Drivers</div>
                    <div class="text-sm text-[var(--text-muted)]">$2.50/day</div>
                    <div class="text-xs text-[var(--text-muted)]">Add up to 2 additional drivers</div>
                  </div>
                </div>
                <div class="extra-toggle">
                  <input type="checkbox">
                  <span class="slider"></span>
                </div>
              </label>
            </div>

          </div>
        </div>

        <!-- TOTAL PRICE -->
        <div class="bg-gradient-to-r from-gold/10 to-yellow-500/10 p-7 rounded-2xl border border-gold/30 text-center">
          <p class="text-gold font-bold mb-3 text-lg">Total Estimated Price</p>
          <p id="total-price" class="text-5xl font-black text-[var(--text-primary)]">MAD0</p>
          <p id="days-count" class="text-[var(--text-muted)] mt-2 text-lg"></p>
          <p id="extras-info" class="text-sm text-gold mt-3 font-medium"></p>
          <p id="insurance-info" class="text-sm text-gold mt-1 font-medium">Basic Insurance (included)</p>
          <?php if ($hasDiscount): ?>
            <p class="text-green-400 text-sm mt-2 font-bold">You save MAD<?= number_format(($originalPricePerDay - $discountedPricePerDay) * $minDays) ?> on minimum rental!</p>
          <?php endif; ?>
        </div>

        <!-- PERSONAL INFO -->
        <input type="text" name="name" required placeholder="Full Name" class="w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold">
        <input type="email" name="email" required placeholder="Email Address" class="w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold">
        <input type="tel" name="phone" required placeholder="Phone (WhatsApp)" class="w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold">

        <!-- SUBMIT -->
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script>
  AOS.init({ once: true, duration: 800 });

  const pickup = document.getElementById('pickup');
  const ret = document.getElementById('return');
  const totalEl = document.getElementById('total-price');
  const daysEl = document.getElementById('days-count');
  const extrasInfo = document.getElementById('extras-info');
  const insuranceInfo = document.getElementById('insurance-info');
  const error = document.getElementById('date-error');
  const btn = document.getElementById('submit-btn');
  const form = document.getElementById('booking-form');

  const pricePerDay = <?= json_encode($discountedPricePerDay) ?>;
  const minDays = <?= $minDays ?>;
  const insurancePrices = { 'basic': 0, 'smart': 8.90, 'premium': 14.40 };

  const extras = {
    fuel: { price: 110.00, perDay: false },
    unlimited: { price: 10.50, perDay: true },
    cancellation: { price: 9.50, perDay: false },
    drivers: { price: 2.50, perDay: true }
  };

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

    const insuranceCostPerDay = insurancePrices[document.querySelector('input[name="insurance"]:checked').id];
    let extrasCost = 0;
    let extrasText = [];

    Object.keys(extras).forEach(key => {
      const checkbox = document.getElementById(key);
      if (checkbox && checkbox.checked) {
        const cost = extras[key].perDay ? extras[key].price * days : extras[key].price;
        extrasCost += cost;
        const label = checkbox.value.split(' - ')[0];
        extrasText.push(`${label}: $${extras[key].price}${extras[key].perDay ? '/day' : '/rental'}`);
      }
    });

    const carTotal = days * pricePerDay;
    const insuranceTotal = days * insuranceCostPerDay;
    const grandTotal = carTotal + insuranceTotal + extrasCost;

    totalEl.textContent = 'MAD' + grandTotal.toLocaleString();
    daysEl.textContent = days + ' day' + (days > 1 ? 's' : '');
    extrasInfo.textContent = extrasText.length ? extrasText.join(' • ') : '';
    const insLabel = document.querySelector('input[name="insurance"]:checked').value;
    insuranceInfo.textContent = insLabel;
    btn.disabled = false;
  }

  // Sync toggle with hidden checkbox
  document.querySelectorAll('.extra-option input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', () => {
      cb.nextElementSibling.querySelector('input').checked = cb.checked;
      updateTotal();
    });
  });

  document.querySelectorAll('.extra-toggle input').forEach(toggle => {
    toggle.addEventListener('change', () => {
      toggle.closest('label').previousElementSibling.checked = toggle.checked;
      updateTotal();
    });
  });

  document.querySelectorAll('input[name="insurance"]').forEach(r => r.addEventListener('change', updateTotal));
  pickup.addEventListener('change', () => {
    const minReturn = new Date(pickup.value);
    minReturn.setDate(minReturn.getDate() + minDays);
    ret.min = minReturn.toISOString().split('T')[0];
    updateTotal();
  });
  ret.addEventListener('change', updateTotal);

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const days = Math.ceil((new Date(ret.value) - new Date(pickup.value)) / 86400000);
    const insuranceText = document.querySelector('input[name="insurance"]:checked').value;
    const selectedExtras = [];
    Object.keys(extras).forEach(key => {
      const el = document.getElementById(key);
      if (el && el.checked) selectedExtras.push(el.value);
    });

    const extrasText = selectedExtras.length ? '\nExtras:\n• ' + selectedExtras.join('\n• ') : '';

    const msg = `NEW BOOKING - ET TAAJ RENT CARS\n\n` +
                `Car: <?= htmlspecialchars($car['name']) ?>\n` +
                `Pickup: ${pickup.value}\n` +
                `Return: ${ret.value}\n` +
                `Duration: ${days} days\n` +
                `Price per day: MAD<?= number_format($discountedPricePerDay) ?><?php if($hasDiscount): ?> (-<?= $discountPercent ?>% discount)<?php endif; ?>\n` +
                `Insurance: ${insuranceText}\n${extrasText ? extrasText + '\n' : ''}` +
                `GRAND TOTAL: MAD${(days * pricePerDay + days * insurancePrices[document.querySelector('input[name="insurance"]:checked').id] + 
                  Object.keys(extras).reduce((sum, k) => sum + (document.getElementById(k)?.checked ? (extras[k].perDay ? extras[k].price * days : extras[k].price) : 0), 0)).toLocaleString()}\n\n` +
                `Name: ${form.name.value}\nEmail: ${form.email.value}\nPhone: ${form.phone.value}`;

    window.open(`https://wa.me/212772331080?text=${encodeURIComponent(msg)}`, '_blank');
    showSuccessAlert();
    form.reset();
    totalEl.textContent = 'MAD0';
    daysEl.textContent = '';
    extrasInfo.textContent = '';
    insuranceInfo.textContent = 'Basic Insurance (included)';
    btn.disabled = true;
  });

  function showSuccessAlert() {
    const overlay = document.createElement('div');
    overlay.style.cssText = `position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);backdrop-filter:blur(12px);z-index:9999;display:flex;align-items:center;justify-content:center;animation:fadeIn 0.6s ease-out;`;
    overlay.innerHTML = `<div style="text-align:center;padding:40px;max-width:90%;animation:zoomIn 0.8s ease-out;">
      <div style="width:130px;height:130px;margin:0 auto 30px;background:linear-gradient(135deg,#FFD700,#FFA500);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 0 80px rgba(255,215,0,0.7);">
        <svg style="width:80px;height:80px;color:black;" fill="none" stroke="currentColor" stroke-width="5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
      </div>
      <h2 style="font-size:3rem;font-weight:900;background:linear-gradient(to right,#FFD700,#FFA500);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin:20px 0;">Booking Sent!</h2>
      <p style="font-size:1.5rem;color:#eee;margin:15px 0;">Thank you for choosing ET TAAJ Rent Cars</p>
      <p style="font-size:1.2rem;color:#ccc;">We will contact you on WhatsApp within minutes</p>
      <button onclick="this.closest('div[style*=\'fixed\']').remove()" style="margin-top:40px;background:linear-gradient(135deg,#FFD700,#FFA500);color:black;font-weight:bold;padding:16px 50px;border:none;border-radius:50px;font-size:1.3rem;cursor:pointer;box-shadow:0 15px 40px rgba(255,215,0,0.4);transition:all 0.3s;">Got it, thanks!</button>
    </div>`;
    document.body.appendChild(overlay);
    setTimeout(() => overlay.style.animation = 'fadeOut 0.8s ease-in forwards' || overlay.remove(), 7000);
  }

  document.addEventListener('DOMContentLoaded', () => {
    pickup.min = new Date().toISOString().split('T')[0];
  });
</script>
</body>
</html>