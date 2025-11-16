<?php
require 'config.php';

/* -------------------------------------------------
   1. Build Query
   ------------------------------------------------- */
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
if (!empty($where)) $sql .= " WHERE " . implode(' AND ', $where);
$sql  .= " ORDER BY $order";

/* -------------------------------------------------
   2. renderCarCard()
   ------------------------------------------------- */
function renderCarCard($car, $index = 0): string
{
    $baseImg = !empty($car['image'])
        ? 'uploads/' . basename($car['image'])
        : 'https://via.placeholder.com/600x338/36454F/FFFFFF?text=' . urlencode($car['name']);

    $cacheBuster = '';
    $fullPath    = $_SERVER['DOCUMENT_ROOT'] . '/' . $baseImg;
    if (file_exists($fullPath)) $cacheBuster = '?v=' . filemtime($fullPath);
    $imgUrl = $baseImg . $cacheBuster;

    $delay = 100 + ($index % 8) * 80;
    ob_start(); ?>
    <div data-aos="fade-up" data-aos-delay="<?= $delay ?>" data-aos-duration="700"
         class="group relative bg-card/90 backdrop-blur-md rounded-3xl overflow-hidden shadow-2xl hover:shadow-[var(--gold)]/20
                transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02]
                border border-border flex flex-col h-full">
        <!-- Image -->
        <div class="relative w-full pt-[56.25%] bg-card-dark overflow-hidden border-b border-border">
            <img src="<?= htmlspecialchars($imgUrl) ?>"
                 alt="<?= htmlspecialchars($car['name']) ?> - ETTAAJ RENT CARS"
                 class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
                 onerror="this.onerror=null;this.src='https://via.placeholder.com/600x338/36454F/FFFFFF?text=No+Image';this.classList.add('object-contain','p-8');">
        </div>

        <!-- Card Body -->
        <div class="px-5 pb-5 sm:px-6 sm:pb-6 flex-1 flex flex-col bg-card">
            <h3 class="text-xl sm:text-2xl font-extrabold text-primary mb-2 text-center line-clamp-1">
                <?= htmlspecialchars($car['name']) ?>
            </h3>

            <!-- Specs -->
            <div class="flex justify-center gap-6 sm:gap-8 text-muted mb-4 text-xs sm:text-sm">
                <div class="flex flex-col items-center">
                    <svg class="w-5 h-5 mb-1 text-gold" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                    </svg>
                    <span class="font-medium text-primary"><?= (int)$car['seats'] ?> Seats</span>
                </div>
                <div class="flex flex-col items-center">
                    <svg class="w-5 h-5 mb-1 text-gold" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                    </svg>
                    <span class="font-medium text-primary"><?= (int)$car['bags'] ?> Bags</span>
                </div>
            </div>

            <!-- Gear & Fuel -->
            <div class="flex justify-center gap-4 text-xs text-muted mb-5 font-medium">
                <span class="px-3 py-1 bg-card-dark rounded-full text-primary border border-border">
                    <?= htmlspecialchars($car['gear']) ?>
                </span>
                <span class="px-3 py-1 bg-card-dark rounded-full text-primary border border-border">
                    <?= htmlspecialchars($car['fuel']) ?>
                </span>
            </div>

            <!-- Price -->
            <div class="flex flex-col items-center mt-4 mb-3">
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl sm:text-5xl font-extrabold text-primary">
                        <?= number_format((float)$car['price_day']) ?>
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold text-primary bg-gradient-to-r from-gold to-yellow-500 rounded-full shadow-lg animate-pulse">
                        <span>MAD</span><span>/day</span>
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

            <!-- CTA -->
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

/* -------------------------------------------------
   3. AJAX Response
   ------------------------------------------------- */
if (isset($_GET['ajax']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $html = '';
    foreach ($cars as $i => $c) $html .= renderCarCard($c, $i);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['html' => $html, 'count' => count($cars)]);
    exit;
}

/* -------------------------------------------------
   4. Normal Load
   ------------------------------------------------- */
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
  <!-- (all your meta, OG, schema … unchanged) -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ETTAAJ RENT CARS | Premium Car Rental in Casablanca, Morocco</title>
  <!-- … keep all meta tags you already have … -->

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: { colors: { gold: '#FFD700', 'gold-dark': '#E6C200', 'light-gold': '#d97706' } } }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <!-- ==== CSS VARIABLES (same as header.php) ==== -->
  <style>
    :root { --bg:#36454F; --bg-dark:#2C3A44; --card:#36454F; --card-dark:#2C3A44; --border:#4A5A66; --primary:#FFFFFF; --muted:#D1D5DB; --gold:#FFD700; --hover-bg:rgba(255,215,0,.1); }
    .light { --bg:#f8fafc; --bg-dark:#e2e8f0; --card:#ffffff; --card-dark:#f1f5f9; --border:#cbd5e1; --primary:#1e293b; --muted:#64748b; --gold:#d97706; --hover-bg:rgba(217,119,6,.1); }

    body { background:var(--bg); color:var(--primary); }
    .bg-card { background:var(--card); }
    .bg-card-dark { background:var(--card-dark); }
    .border-border { border-color:var(--border); }
    .text-primary { color:var(--primary); }
    .text-muted { color:var(--muted); }
    .text-gold { color:var(--gold) !important; }

    .spinner { width:40px;height:40px;border:4px solid var(--bg-dark);border-top:4px solid var(--gold);border-radius:50%;animation:spin 1s linear infinite;margin:40px auto; }
    @keyframes spin { to { transform:rotate(360deg); } }
  </style>
</head>
<body class="min-h-screen">

<?php include 'header.php'; ?>

<!-- ==== HERO ==== -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-[#1e293b] via-[#36454F] to-[#2C3A44]"
         style="background-image:url('https://images.unsplash.com/photo-1494905998402-395d579af36f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');background-size:cover;background-position:center;"
         data-aos="fade" data-aos-duration="1500">
  <!-- … keep your hero markup unchanged … -->
</section>

<!-- ==== FILTERS & CARS ==== -->
<section id="cars" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-[var(--bg)]">
  <div data-aos="fade-up" data-aos-delay="200" data-aos-duration="800"
       class="bg-card-dark p-4 sm:p-6 rounded-xl shadow-lg mb-6 border border-border">
    <form id="filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
      <input type="text" id="search" placeholder="Search car..." value="<?= htmlspecialchars($search) ?>"
             class="col-span-1 sm:col-span-2 lg:col-span-1 p-3 bg-card border border-border text-primary placeholder-muted rounded-lg focus:ring-2 focus:ring-gold focus:border-gold text-sm">
      <select id="gear" class="p-3 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
        <option value="">All Gears</option>
        <option value="Manual" <?= $gear==='Manual'?'selected':'' ?>>Manual</option>
        <option value="Automatic" <?= $gear==='Automatic'?'selected':'' ?>>Automatic</option>
      </select>
      <select id="fuel" class="p-3 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
        <option value="">All Fuels</option>
        <option value="Diesel" <?= $fuel==='Diesel'?'selected':'' ?>>Diesel</option>
        <option value="Petrol" <?= $fuel==='Petrol'?'selected':'' ?>>Petrol</option>
      </select>
      <select id="sort" class="p-3 bg-card border border-border text-primary rounded-lg focus:ring-2 focus:ring-gold text-sm">
        <option value="low" <?= $sort==='low'?'selected':'' ?>>Low to High</option>
        <option value="high" <?= $sort==='high'?'selected':'' ?>>High to Low</option>
      </select>
      <a href="index.php" class="col-span-1 sm:col-span-2 lg:col-span-1 bg-border hover:bg-[var(--muted)]/30 text-primary font-medium py-3 px-4 rounded-lg transition text-center text-sm flex items-center justify-center">
        Clear All
      </a>
    </form>
  </div>

  <p id="results-count" class="text-sm text-muted mb-4">
    <?= count($cars) ?> car<?= count($cars)!==1?'s':'' ?> found
  </p>

  <div id="cars-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach ($cars as $i => $c): ?>
      <?= renderCarCard($c, $i) ?>
    <?php endforeach; ?>
  </div>
</section>

<?php include 'footer.php'; ?>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once:true, duration:800, easing:'ease-out-quart' });

  /* ---- AJAX FILTERS (unchanged) ---- */
  const els = { search:document.getElementById('search'), gear:document.getElementById('gear'), fuel:document.getElementById('fuel'), sort:document.getElementById('sort') };
  const container = document.getElementById('cars-container');
  const countEl   = document.getElementById('results-count');
  let debounceTimer = null, isLoading = false;

  const fetchCars = () => {
    if (isLoading) return;
    isLoading = true;
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      const params = new URLSearchParams({ search:els.search.value.trim(), gear:els.gear.value, fuel:els.fuel.value, sort:els.sort.value, ajax:1 });
      const fallback = container.innerHTML;
      container.innerHTML = '<div class="col-span-full flex justify-center"><div class="spinner"></div></div>';
      fetch(`index.php?${params}`,{headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(r=>{if(!r.ok)throw new Error();return r.json();})
        .then(d=>{ container.innerHTML = d.html||'<p class="col-span-full text-center text-muted">No cars found.</p>'; countEl.textContent=`${d.count} car${d.count!==1?'s':''} found`; AOS.refreshHard(); })
        .catch(()=>{ container.innerHTML=fallback; })
        .finally(()=>{ isLoading=false; });
    },300);
  };
  els.search.addEventListener('input',fetchCars);
  els.gear.addEventListener('change',fetchCars);
  els.fuel.addEventListener('change',fetchCars);
  els.sort.addEventListener('change',fetchCars);
</script>
</body>
</html>
