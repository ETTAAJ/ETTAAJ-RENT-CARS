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

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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
    transform: translateX(100%);
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
  }
  @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }

  /* Input Styling */
  :root { --input-color: #000000; }
  .dark, [data-theme="dark"] { --input-color: #FFFFFF; }
  input { color: var(--input-color) !important; -webkit-text-fill-color: var(--input-color) !important; }
  input::placeholder { color: #666 !important; opacity: 0.7; }

  /* Insurance Cards */
  .insurance-option {
    transition: all 0.4s ease;
    position: relative;
  }
  .insurance-option:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(255, 215, 0, 0.15) !important;
  }
  .insurance-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
  }
  .insurance-option input[type="radio"]:checked + label {
    border: 2px solid #FFD700;
    background: linear-gradient(135deg, rgba(255,215,0,0.1), rgba(255,165,0,0.05));
    box-shadow: 0 0 25px rgba(255,215,0,0.3);
  }
  .insurance-option label {
    transition: all 0.4s ease;
    cursor: pointer;
  }

  /* Travel Essentials */
  .extra-item {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,215,0,0.15);
    border-radius: 16px;
    padding: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.3s ease;
    flex-wrap: wrap;
    gap: 1rem;
  }
  .extra-item:hover {
    background: rgba(255,215,0,0.06);
    border-color: rgba(255,215,0,0.3);
  }
  .extra-item label { flex: 1; min-width: 200px; cursor: pointer; }
  .extra-item svg {
    width: 24px;
    height: 24px;
    color: #FFD700;
  }
  .extra-price {
    font-weight: bold;
    color: #FFD700;
    font-size: 1rem;
  }

  /* Toggle Switch */
  .toggle-switch {
    position: relative;
    display: inline-block;
    width: 56px;
    height: 32px;
    cursor: pointer;
  }
  .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }
  .slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #333;
    transition: all 0.4s ease;
    border-radius: 34px;
    box-shadow: inset 0 2px 6px rgba(0,0,0,0.3);
  }
  .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: all 0.4s ease;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
  }
  input:checked + .slider {
    background: linear-gradient(135deg, #FFD700, #FFA500);
  }
  input:checked + .slider:before {
    transform: translateX(24px);
  }

  /* WhatsApp Button */
  .whatsapp-btn {
    background: linear-gradient(135deg, #FFD700, #FFA500) !important;
    color: #000 !important;
    font-weight: bold !important;
  }
  .whatsapp-btn:hover { 
    background: linear-gradient(135deg, #FFA500, #FF8C00) !important; 
    transform: scale(1.02); 
  }
  .whatsapp-btn:disabled { 
    opacity: 0.6; 
    cursor: not-allowed; 
    transform: none; 
  }

  /* Responsive */
  @media (max-width: 768px) {
    .extra-item { flex-direction: column; align-items: flex-start; }
    .extra-item label { min-width: 0; }
    .extra-item .toggle-switch { margin-left: auto; }
  }
</style>

<main class="max-w-7xl mx-auto px-4 py-12 bg-[var(--bg)] text-[var(--text-primary)]">

  <!-- LUXURY 2-TAB BAR -->
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

  <!-- HERO TITLE -->
  <div class="text-center mb-16" data-aos="fade-up">
    <h1 class="text-5xl sm:text-6xl md:text-7xl font-black tracking-tight text-transparent bg-clip-text 
               bg-gradient-to-r from-amber-400 via-yellow-500 to-orange-500 drop-shadow-2xl leading-tight">
      Complete Your Booking
    </h1>
    <p class="mt-6 text-xl sm:text-2xl font-medium text-amber-400 drop-shadow-lg tracking-wider">
      Premium Service • Instant Confirmation • 24/7 Support
    </p>
  </div>

  <!-- CAR NAME -->
  <div class="text-center mb-8" data-aos="fade-up">
    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gold mb-2">
      <?= htmlspecialchars($car['name']) ?>
    </h2>
    <div class="flex items-center justify-center gap-4 text-sm text-[var(--text-muted)]">
      <span class="flex items-center gap-2">
        <i class="bi bi-person-fill text-gold"></i>
        <?= $car['seats'] ?> Seats
      </span>
      <span class="flex items-center gap-2">
        <i class="bi bi-briefcase-fill text-gold"></i>
        <?= $car['bags'] ?> Bags
      </span>
      <span class="px-3 py-1 bg-card-dark rounded-full border border-border"><?= htmlspecialchars($car['gear']) ?></span>
      <span class="px-3 py-1 bg-card-dark rounded-full border border-border"><?= htmlspecialchars($car['fuel']) ?></span>
    </div>
    <div class="mt-4">
      <span class="text-2xl font-black text-gold">
        MAD <?= number_format($discountedPricePerDay) ?>
      </span>
      <span class="text-lg text-[var(--text-muted)]">/day</span>
      <?php if ($hasDiscount): ?>
        <span class="ml-2 text-lg text-green-400 line-through opacity-70">
          MAD <?= number_format($originalPricePerDay) ?>
        </span>
        <span class="ml-2 px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-bold">
          -<?= $discountPercent ?>% OFF
        </span>
      <?php endif; ?>
    </div>
  </div>

  <!-- CAR IMAGE -->
  <div class="max-w-4xl mx-auto mb-8" data-aos="fade-up">
    <div class="relative w-full rounded-3xl overflow-hidden shadow-2xl border border-border group">
      <div class="relative w-full pt-[56.25%] bg-card-dark overflow-hidden">
        <?php
        $imgUrl = !empty($car['image'])
            ? carImageUrl($car['image'])
            : 'https://via.placeholder.com/800x450/36454F/FFFFFF?text=' . urlencode($car['name']);
        ?>
        <img src="<?= htmlspecialchars($imgUrl) ?>" 
             alt="<?= htmlspecialchars($car['name']) ?>" 
             class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
        
        <?php if ($hasDiscount): ?>
          <div class="discount-badge">
            -<?= $discountPercent ?>%
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="max-w-4xl mx-auto">
    <!-- BOOKING FORM -->
    <div data-aos="fade-up" class="space-y-8">
      <form id="booking-form" class="bg-card/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-border p-10 space-y-8">
        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">

        <!-- DATES -->
        <div>
          <h2 class="text-2xl font-bold text-gold mb-4">Trip Dates</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="relative">
              <input type="date" name="pickup" id="pickup" required 
                     class="w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold focus:border-gold transition">
              <label class="absolute left-4 -top-2.5 bg-[var(--card)] px-3 text-xs font-bold text-gold">Pickup Date</label>
            </div>
            <div class="relative">
              <input type="date" name="return" id="return" required 
                     class="w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold focus:border-gold transition">
              <label class="absolute left-4 -top-2.5 bg-[var(--card)] px-3 text-xs font-bold text-gold">Return Date</label>
            </div>
          </div>
          <p id="date-error" class="text-red-400 text-sm mt-2 hidden">Return date must be at least <?= $minDays ?> days after pickup.</p>
        </div>

        <!-- PROTECTION PLAN -->
        <div>
          <h2 class="text-2xl font-bold text-gold mb-4">Protection Plan</h2>
          <div class="space-y-4">

            <div class="insurance-option">
              <input type="radio" name="insurance" id="basic" value="Basic Insurance - Included" checked>
              <label for="basic" class="block p-6 bg-card-dark/80 rounded-2xl border border-border hover:border-gold/50 transition-all">
                <div class="flex justify-between items-start mb-3">
                  <div>
                    <h4 class="text-xl font-bold">Basic Insurance</h4>
                    <p class="text-sm text-green-400">Standard coverage included</p>
                  </div>
                  <span class="text-2xl font-black text-gold">FREE</span>
                </div>
                <p class="text-sm text-[var(--text-muted)] mb-3">Deposit: <strong>$588.00</strong></p>
                <ul class="text-sm space-y-1 text-[var(--text-muted)]">
                  <li>• Third-party liability</li>
                  <li>• Basic collision damage waiver</li>
                  <li>• Standard theft protection</li>
                </ul>
              </label>
            </div>

            <div class="insurance-option">
              <input type="radio" name="insurance" id="smart" value="Smart Insurance - +$8.90/day">
              <label for="smart" class="block p-6 bg-card-dark/80 rounded-2xl border border-border hover:border-gold/50 transition-all">
                <div class="flex justify-between items-start mb-3">
                  <div>
                    <h4 class="text-xl font-bold">Smart Insurance</h4>
                    <p class="text-sm text-amber-400">CITADINE Rate</p>
                  </div>
                  <span class="text-2xl font-black text-gold">+$8.90/day</span>
                </div>
                <p class="text-sm text-[var(--text-muted)] mb-3">Deposit: <strong>$294.00</strong></p>
                <ul class="text-sm space-y-1 text-[var(--text-muted)]">
                  <li>• All Basic coverage</li>
                  <li>• Reduced excess by 50%</li>
                  <li>• Window and tire coverage</li>
                  <li>• Personal accident insurance</li>
                </ul>
              </label>
            </div>

            <div class="insurance-option">
              <input type="radio" name="insurance" id="premium" value="Premium Insurance - +$14.40/day">
              <label for="premium" class="block p-6 bg-card-dark/80 rounded-2xl border border-border hover:border-gold/50 transition-all">
                <div class="flex justify-between items-start mb-3">
                  <div>
                    <h4 class="text-xl font-bold">Premium Insurance</h4>
                    <p class="text-sm text-amber-400">CITADINE Rate</p>
                  </div>
                  <span class="text-2xl font-black text-gold">+$14.40/day</span>
                </div>
                <p class="text-sm text-[var(--text-muted)] mb-3">Deposit: <strong>$98.00</strong></p>
                <ul class="text-sm space-y-1 text-[var(--text-muted)]">
                  <li>• All Smart coverage</li>
                  <li>• Zero excess</li>
                  <li>• 24/7 premium roadside assistance</li>
                  <li>• Personal effects coverage</li>
                  <li>• Extended liability protection</li>
                </ul>
              </label>
            </div>
          </div>
          <p class="text-xs text-center text-[var(--text-muted)] mt-4">
            All plans include mandatory third-party liability. Premium protections can be modified until pickup time.
          </p>
        </div>

        <!-- TRAVEL ESSENTIALS -->
        <div>
          <h2 class="text-2xl font-bold text-gold mb-4">Travel Essentials</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div class="extra-item">
              <label for="fuel" class="flex-1 flex items-center gap-3">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h1m4-4v12m4-8h6m-3-3v6"/></svg>
                <div>
                  <div class="font-semibold">Premium Fuel Service</div>
                  <div class="text-xs text-[var(--text-muted)]">Prepaid full tank</div>
                </div>
              </label>
              <div class="flex items-center gap-3">
                <span class="extra-price">$110.00/rental</span>
                <div class="toggle-switch">
                  <input type="checkbox" id="fuel" name="extras[]" value="Premium Fuel Service - $110.00/rental">
                  <span class="slider"></span>
                </div>
              </div>
            </div>

            <div class="extra-item">
              <label for="unlimitedkm" class="flex-1 flex items-center gap-3">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <div>
                  <div class="font-semibold">Unlimited Kilometers</div>
                  <div class="text-xs text-[var(--text-muted)]">Drive without mileage restrictions</div>
                </div>
              </label>
              <div class="flex items-center gap-3">
                <span class="extra-price">$10.50/day</span>
                <div class="toggle-switch">
                  <input type="checkbox" id="unlimitedkm" name="extras[]" value="Unlimited Kilometers - $10.50/day">
                  <span class="slider"></span>
                </div>
              </div>
            </div>

            <div class="extra-item">
              <label for="flexcancel" class="flex-1 flex items-center gap-3">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                  <div class="font-semibold">Flexible Cancellation</div>
                  <div class="text-xs text-[var(--text-muted)]">Free cancellation until scheduled departure</div>
                </div>
              </label>
              <div class="flex items-center gap-3">
                <span class="extra-price">$9.50/rental</span>
                <div class="toggle-switch">
                  <input type="checkbox" id="flexcancel" name="extras[]" value="Flexible Cancellation - $9.50/rental">
                  <span class="slider"></span>
                </div>
              </div>
            </div>

            <div class="extra-item">
              <label for="extradriver" class="flex-1 flex items-center gap-3">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H9v-1c-4 0-6-4-6-4v-3h12v3c0 0-2 4-6 4z"/></svg>
                <div>
                  <div class="font-semibold">Additional Drivers</div>
                  <div class="text-xs text-[var(--text-muted)]">Add up to 2 additional drivers</div>
                </div>
              </label>
              <div class="flex items-center gap-3">
                <span class="extra-price">$2.50/day</span>
                <div class="toggle-switch">
                  <input type="checkbox" id="extradriver" name="extras[]" value="Additional Drivers - $2.50/day">
                  <span class="slider"></span>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- TOTAL PRICE -->
        <div class="bg-gradient-to-r from-gold/10 to-yellow-500/10 p-7 rounded-2xl border border-gold/30 text-center">
          <p class="text-gold font-bold mb-3 text-lg">Total Estimated Price</p>
          <p id="total-price" class="text-5xl font-black text-[var(--text-primary)]" role="status" aria-live="polite">MAD0</p>
          <p id="days-count" class="text-[var(--text-muted)] mt-2 text-lg" aria-live="polite"></p>
          <p id="insurance-info" class="text-sm text-gold mt-3 font-medium">Basic Insurance (included)</p>
          <p id="extras-info" class="text-sm text-amber-300 mt-2"></p>
          <?php if ($hasDiscount): ?>
            <p class="text-green-400 text-sm mt-2 font-bold">You save MAD<?= number_format(($originalPricePerDay - $discountedPricePerDay) * $minDays) ?> on minimum rental!</p>
          <?php endif; ?>
        </div>

        <!-- PERSONAL INFO -->
        <div>
          <h2 class="text-2xl font-bold text-gold mb-4">Personal Details</h2>
          <div class="space-y-4">
            <input type="text" name="name" required placeholder="Full Name" 
                   class="w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold">
            <input type="email" name="email" required placeholder="Email Address" 
                   class="w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold">
            <input type="tel" name="phone" required placeholder="Phone (WhatsApp)" 
                   class="w-full p-4 bg-white/10 border border-border rounded-2xl focus:ring-2 focus:ring-gold">
          </div>
        </div>

        <!-- SUBMIT -->
        <button type="submit" id="submit-btn" disabled 
                class="whatsapp-btn w-full py-6 rounded-2xl shadow-2xl transition-all duration-300 flex items-center justify-center gap-4 text-xl font-bold">
          <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.198-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.134.297-.347.446-.52.149-.174.198-.297.297-.446.099-.148.05-.273-.024-.385-.074-.112-.67-1.62-.92-2.22-.246-.594-.495-.59-.67-.599-.174-.008-.371-.008-.569-.008-.197 0-.52.074-.792.372-.273.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.558 5.745 8.623 8.05.297.149.595.223.893.298.297.074.595.05.893-.025.297-.074 1.255-.52 1.43-.966.173-.446.173-.82.124-.966-.05-.148-.198-.297-.446-.446zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
          </svg>
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

  const pickup = document.getElementById('pickup');
  const ret = document.getElementById('return');
  const totalEl = document.getElementById('total-price');
  const daysEl = document.getElementById('days-count');
  const insuranceInfo = document.getElementById('insurance-info');
  const extrasInfo = document.getElementById('extras-info');
  const error = document.getElementById('date-error');
  const btn = document.getElementById('submit-btn');
  const form = document.getElementById('booking-form');

  const pricePerDay = <?= json_encode($discountedPricePerDay) ?>;
  const hasDiscount = <?= $hasDiscount ? 'true' : 'false' ?>;
  const discountPercent = <?= $discountPercent ?>;
  const minDays = <?= $minDays ?>;
  const insurancePrices = { 'basic': 0, 'smart': 8.90, 'premium': 14.40 };

  const extrasPrices = {
    fuel: { price: 110.00, perDay: false },
    unlimitedkm: { price: 10.50, perDay: true },
    flexcancel: { price: 9.50, perDay: false },
    extradriver: { price: 2.50, perDay: true }
  };

  let updateFrame = null;
  const scheduleUpdate = () => {
    if (updateFrame) cancelAnimationFrame(updateFrame);
    updateFrame = requestAnimationFrame(() => {
      updateTotal();
      updateFrame = null;
    });
  };

  const sliderActiveBackground = 'linear-gradient(135deg, #FFD700, #FFA500)';
  const setSliderColor = (checkbox) => {
    const slider = checkbox.closest('.toggle-switch')?.querySelector('.slider');
    if (!slider) { return; }
    slider.style.background = checkbox.checked ? sliderActiveBackground : '#333';
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

    const selectedInsurance = document.querySelector('input[name="insurance"]:checked').id;
    const insuranceCostPerDay = insurancePrices[selectedInsurance];

    let extrasTotal = 0;
    const selectedExtras = [];
    document.querySelectorAll('input[name="extras[]"]:checked').forEach(cb => {
      const id = cb.id;
      const item = extrasPrices[id];
      const cost = item.perDay ? item.price * days : item.price;
      extrasTotal += cost;
      selectedExtras.push(cb.value);
    });

    const carTotal = days * pricePerDay;
    const insuranceTotal = days * insuranceCostPerDay;
    const grandTotal = carTotal + insuranceTotal + extrasTotal;

    totalEl.textContent = 'MAD' + grandTotal.toLocaleString();
    daysEl.textContent = days + ' day' + (days > 1 ? 's' : '');
    const labels = { basic: "Basic Insurance (included)", smart: "Smart Insurance (+$8.90/day)", premium: "Premium Insurance (+$14.40/day)" };
    insuranceInfo.textContent = labels[selectedInsurance];

    if (selectedExtras.length > 0) {
      extrasInfo.textContent = selectedExtras.join(' • ');
      extrasInfo.style.display = 'block';
    } else {
      extrasInfo.textContent = '';
      extrasInfo.style.display = 'none';
    }

    btn.disabled = false;
  }

  // Sync toggle color on change & page load
  document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', () => {
      setSliderColor(cb);
      scheduleUpdate();
    });
    setSliderColor(cb);
  });

  // Allow clicking the slider element itself to toggle the checkbox
  document.querySelectorAll('.toggle-switch .slider').forEach(slider => {
    slider.addEventListener('click', () => {
      const checkbox = slider.previousElementSibling;
      if (!checkbox) { return; }
      checkbox.checked = !checkbox.checked;
      checkbox.dispatchEvent(new Event('change', { bubbles: true }));
    });
  });

  document.querySelectorAll('input[name="insurance"], input[name="extras[]"]').forEach(el => {
    el.addEventListener('change', scheduleUpdate);
  });

  pickup.addEventListener('change', () => {
    const minReturn = new Date(pickup.value);
    minReturn.setDate(minReturn.getDate() + minDays);
    ret.min = minReturn.toISOString().split('T')[0];
    scheduleUpdate();
  });
  ret.addEventListener('change', scheduleUpdate);

  document.getElementById('details-tab-link')?.addEventListener('click', function(e) {
    e.preventDefault();
    const tabBar = document.getElementById('tab-bar');
    tabBar.classList.remove('active-booking');
    tabBar.classList.add('active-details');
    tabBar.style.boxShadow = '0 8px 50px rgba(255, 215, 0, 0.5)';
    setTimeout(() => window.location.href = this.getAttribute('href'), 500);
  });

  form.addEventListener('submit', function(e) {
    e.preventDefault();

    const days = Math.ceil((new Date(ret.value) - new Date(pickup.value)) / 86400000);
    const selectedInsurance = document.querySelector('input[name="insurance"]:checked');
    const insuranceText = selectedInsurance.value;

    let extrasTotal = 0;
    const extrasList = [];
    document.querySelectorAll('input[name="extras[]"]:checked').forEach(cb => {
      const id = cb.id;
      const item = extrasPrices[id];
      const cost = item.perDay ? item.price * days : item.price;
      extrasTotal += cost;
      extrasList.push(cb.value);
    });

    const carTotal = days * pricePerDay;
    const grandTotal = carTotal + (insurancePrices[selectedInsurance.id] * days) + extrasTotal;

    const discountText = hasDiscount ? ` (-${discountPercent}% discount applied)` : '';
    const extrasText = extrasList.length > 0 ? "\nExtras:\n" + extrasList.map(e => "• " + e).join("\n") : "";

    const msg = `NEW BOOKING - ET TAAJ RENT CARS\n\n` +
                `Car: <?= htmlspecialchars($car['name']) ?>\n` +
                `Pickup: ${pickup.value}\n` +
                `Return: ${ret.value}\n` +
                `Duration: ${days} days\n` +
                `Price per day: MAD<?= number_format($discountedPricePerDay) ?>${discountText}\n` +
                `Car Total: MAD${carTotal.toLocaleString()}\n` +
                `Insurance: ${insuranceText}\n` +
                `${extrasText ? extrasText + "\n" : ""}` +
                `GRAND TOTAL: MAD${grandTotal.toLocaleString()}\n\n` +
                `Name: ${form.name.value}\n` +
                `Email: ${form.email.value}\n` +
                `Phone: ${form.phone.value}\n\n` +
                `Please confirm availability & send payment link!`;

    window.open(`https://wa.me/212772331080?text=${encodeURIComponent(msg)}`, '_blank');

    form.reset();
    document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(cb => setSliderColor(cb));
    totalEl.textContent = 'MAD0';
    daysEl.textContent = '';
    insuranceInfo.textContent = 'Basic Insurance (included)';
    extrasInfo.textContent = '';
    btn.disabled = true;
  });

  document.addEventListener('DOMContentLoaded', () => {
    pickup.min = new Date().toISOString().split('T')[0];
    scheduleUpdate();
  });
</script>
</body>
</html>

