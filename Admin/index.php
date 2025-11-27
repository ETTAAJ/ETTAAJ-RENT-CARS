<?php
require_once 'config.php';

/* -------------------------------------------------
   1. SESSION PROTECTION
   ------------------------------------------------- */
if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

/* -------------------------------------------------
   2. REGENERATE SESSION ID
   ------------------------------------------------- */
if (empty($_SESSION['last_regen']) || time() - $_SESSION['last_regen'] > 600) {
    session_regenerate_id(true);
    $_SESSION['last_regen'] = time();
}

/* -------------------------------------------------
   3. CSRF TOKEN
   ------------------------------------------------- */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

/* -------------------------------------------------
   4. ALERT LOGIC
   ------------------------------------------------- */
$alert = null;
if (isset($_GET['success'])) {
    $alert = ['type' => 'success', 'msg' => 'Car saved successfully!'];
} elseif (isset($_GET['deleted'])) {
    $alert = ['type' => 'danger', 'msg' => 'Car deleted permanently.'];
} elseif (isset($_GET['error'])) {
    $alert = ['type' => 'warning', 'msg' => 'An error occurred.'];
}

/* -------------------------------------------------
   5. FILTER & SORT LOGIC (AJAX + Normal Load)
   ------------------------------------------------- */
$search = trim($_GET['search'] ?? '');
$gear   = $_GET['gear'] ?? '';
$fuel   = $_GET['fuel'] ?? '';
$sort   = $_GET['sort'] ?? 'low';

$where  = [];
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
$sql = "SELECT *, COALESCE(discount, 0) AS discount FROM cars";
if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY $order";

// Get all cars for dropdown list
$allCarsStmt = $pdo->prepare("SELECT id, name FROM cars ORDER BY name ASC");
$allCarsStmt->execute();
$allCars = $allCarsStmt->fetchAll(PDO::FETCH_ASSOC);

/* -------------------------------------------------
   6. RENDER CAR CARD (Same as public site)
   ------------------------------------------------- */
function renderAdminCarCard($car, $index = 0): string
{
    $img = !empty($car['image']) ? '../uploads/' . basename($car['image']) . '?v=' . @filemtime(__DIR__ . '/../uploads/' . basename($car['image'])) : '';
    $placeholder = 'https://via.placeholder.com/600x338/36454F/FFFFFF?text=' . urlencode($car['name']);
    $src = $img && file_exists(__DIR__ . '/../uploads/' . basename($car['image'])) ? $img : $placeholder;

    $delay = 100 + ($index % 8) * 80;
    $discount = (int)($car['discount'] ?? 0);
    $originalPrice = (float)$car['price_day'];
    $finalPrice = $discount > 0 ? $originalPrice * (1 - $discount / 100) : $originalPrice;

    ob_start(); ?>
    <div data-aos="fade-up" data-aos-delay="<?= $delay ?>" data-aos-duration="700"
         class="group relative bg-[#2C3A44]/90 backdrop-blur-md rounded-3xl overflow-hidden shadow-2xl hover:shadow-yellow-500/20
                transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02]
                border border-[#4A5A66] flex flex-col h-full">

        <?php if ($discount > 0): ?>
        <div class="absolute top-3 right-3 z-10 bg-green-600 text-white font-bold text-xs px-3 py-1.5 rounded-full shadow-lg animate-pulse">
            -<?= $discount ?>%
        </div>
        <?php endif; ?>

        <div class="relative w-full pt-[56.25%] bg-[#36454F] overflow-hidden">
            <img src="<?= htmlspecialchars($src, ENT_QUOTES) ?>"
                 alt="<?= htmlspecialchars($car['name']) ?>"
                 class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
                 onerror="this.onerror=null;this.src='https://via.placeholder.com/600x338/36454F/FFFFFF?text=No+Image';this.classList.add('object-contain','p-8');">
        </div>

        <div class="px-5 pb-5 sm:px-6 sm:pb-6 flex-1 flex flex-col bg-[#36454F]">
            <h3 class="text-xl sm:text-2xl font-extrabold text-white mb-2 text-center line-clamp-1">
                <?= htmlspecialchars($car['name']) ?>
            </h3>

            <div class="flex justify-center gap-6 sm:gap-8 text-gray-400 mb-4 text-xs sm:text-sm">
                <div class="flex flex-col items-center">
                    <i class="bi bi-person-fill w-5 h-5 mb-1 text-yellow-500"></i>
                    <span class="font-medium text-white"><?= (int)$car['seats'] ?> Seats</span>
                </div>
                <div class="flex flex-col items-center">
                    <i class="bi bi-briefcase-fill w-5 h-5 mb-1 text-yellow-500"></i>
                    <span class="font-medium text-white"><?= (int)$car['bags'] ?> Bags</span>
                </div>
            </div>

            <div class="flex justify-center gap-4 text-xs text-gray-400 mb-5 font-medium">
                <span class="px-3 py-1 bg-[#2C3A44] rounded-full text-white border border-[#4A5A66]"><?= htmlspecialchars($car['gear']) ?></span>
                <span class="px-3 py-1 bg-[#2C3A44] rounded-full text-white border border-[#4A5A66]"><?= htmlspecialchars($car['fuel']) ?></span>
            </div>

            <div class="flex flex-col items-center mt-4 mb-3">
                <div class="flex items-center gap-3">
                    <?php if ($discount > 0): ?>
                    <span class="text-2xl text-gray-500 line-through opacity-70">
                        MAD <?= number_format($originalPrice) ?>
                    </span>
                    <?php endif; ?>

                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl sm:text-5xl font-extrabold <?= $discount > 0 ? 'text-green-400' : 'text-white' ?>">
                            <?= number_format($finalPrice) ?>
                        </span>
                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold text-black bg-gradient-to-r from-yellow-500 to-yellow-400 rounded-full shadow-lg">
                            MAD /day
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-auto flex gap-3">
                <a href="edit.php?id=<?= (int)$car['id'] ?>"
                   class="flex-1 text-center bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-orange-500 hover:to-red-500
                          text-black font-bold py-3 rounded-2xl shadow-lg transition-all duration-300 transform hover:scale-105">
                    <i class="bi bi-pencil-fill"></i> Edit
                </a>
                <button type="button" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $car['id'] ?>"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-2xl shadow-lg transition-all duration-300">
                    <i class="bi bi-trash-fill"></i> Delete
                </button>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/* -------------------------------------------------
   7. AJAX RESPONSE
   ------------------------------------------------- */
if (isset($_GET['ajax'])) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $html = '';
    foreach ($cars as $i => $c) {
        $html .= renderAdminCarCard($c, $i);
    }

    header('Content-Type: application/json');
    echo json_encode(['html' => $html, 'count' => count($cars)]);
    exit;
}

/* -------------------------------------------------
   8. NORMAL LOAD
   ------------------------------------------------- */
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin – Car Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root { --gold: #FFD700; }
    body { background: #36454F; color: white; font-family: 'Inter', sans-serif; }
    .spinner { width: 40px; height: 40px; border: 4px solid #2C3A44; border-top: 4px solid #FFD700; border-radius: 50%; animation: spin 1s linear infinite; margin: 40px auto; }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>
<body class="min-h-screen">

<!-- Toast -->
<div class="toast-container fixed top-4 right-4 z-50" id="toastContainer"></div>

<!-- Day Mode Toggle -->
<button class="fixed bottom-6 right-6 w-14 h-14 rounded-full bg-yellow-500 text-black text-2xl shadow-2xl z-50 hover:scale-110 transition" id="dayModeToggle">
  <i class="bi bi-sun-fill"></i>
</button>

<!-- Header -->
<div class="bg-[#2C3A44] border-b border-[#4A5A66] shadow-xl">
  <div class="container mx-auto px-6 py-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
      <h2 class="text-2xl font-bold flex items-center gap-3">
        <i class="bi bi-car-front-fill text-yellow-500"></i> Car Management
      </h2>
      <div class="flex gap-3">
        <a href="create.php" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl flex items-center gap-2">
          <i class="bi bi-plus-circle"></i> Add New Car
        </a>
        <a href="currencies.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl flex items-center gap-2">
          <i class="bi bi-currency-exchange"></i> Currencies
        </a>
        <a href="change_password.php" class="bg-yellow-600 hover:bg-yellow-700 text-black font-bold py-3 px-6 rounded-xl">
          <i class="bi bi-shield-lock"></i> Password
        </a>
        <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl">
          <i class="bi bi-box-arrow-right"></i> Logout
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container mx-auto px-6 py-10 max-w-7xl">
  <!-- Filters -->
  <div data-aos="fade-up" class="bg-[#2C3A44] p-6 rounded-2xl shadow-2xl border border-[#4A5A66] mb-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <select id="search" class="p-4 bg-[#36454F] border border-[#4A5A66] text-white rounded-xl focus:ring-2 focus:ring-yellow-500 cursor-pointer" style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23ffffff' d='M6 9L1 4h10z'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 12px; padding-right: 2.5rem;">
        <option value="">All Cars</option>
        <?php foreach ($allCars as $carOption): ?>
          <option value="<?= htmlspecialchars($carOption['name']) ?>" <?= $search === $carOption['name'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($carOption['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <select id="gear" class="p-4 bg-[#36454F] border border-[#4A5A66] text-white rounded-xl focus:ring-2 focus:ring-yellow-500">
        <option value="">All Transmission</option>
        <option value="Manual" <?= $gear==='Manual'?'selected':'' ?>>Manual</option>
        <option value="Automatic" <?= $gear==='Automatic'?'selected':'' ?>>Automatic</option>
      </select>
      <select id="fuel" class="p-4 bg-[#36454F] border border-[#4A5A66] text-white rounded-xl focus:ring-2 focus:ring-yellow-500">
        <option value="">All Fuel</option>
        <option value="Diesel" <?= $fuel==='Diesel'?'selected':'' ?>>Diesel</option>
        <option value="Petrol" <?= $fuel==='Petrol'?'selected':'' ?>>Petrol</option>
      </select>
      <select id="sort" class="p-4 bg-[#36454F] border border-[#4A5A66] text-white rounded-xl focus:ring-2 focus:ring-yellow-500">
        <option value="low" <?= $sort==='low'?'selected':'' ?>>Price: Low → High</option>
        <option value="high" <?= $sort==='high'?'selected':'' ?>>Price: High → Low</option>
      </select>
      <a href="?" class="bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-500 font-bold py-4 rounded-xl text-center transition-colors">Clear All</a>
    </div>
  </div>

  <p id="results-count" class="text-center text-gray-400 text-lg mb-8"><?= count($cars) ?> cars in total</p>

  <div id="cars-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
    <?php foreach ($cars as $i => $c): ?>
      <?= renderAdminCarCard($c, $i) ?>
      
      <!-- Delete Modal -->
      <div class="modal fade" id="deleteModal<?= $c['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content bg-[#2C3A44] border border-[#4A5A66] text-white">
            <div class="modal-header border-0">
              <h5 class="modal-title text-red-500"><i class="bi bi-exclamation-triangle-fill"></i> Confirm Delete</h5>
              <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p>Permanently delete <strong><?= htmlspecialchars($c['name']) ?></strong>?</p>
            </div>
            <div class="modal-footer border-0">
              <form action="delete.php" method="POST">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <input type="hidden" name="csrf" value="<?= $csrf ?>">
                <button type="submit" class="btn btn-danger w-100">Yes, Delete</button>
              </form>
              <button type="button" class="btn btn-secondary w-100 mt-2" data-bs-dismiss="modal">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  AOS.init({ once: true, duration: 800 });

  // Live AJAX Filter
  const els = {
    search: document.getElementById('search'),
    gear: document.getElementById('gear'),
    fuel: document.getElementById('fuel'),
    sort: document.getElementById('sort')
  };
  const container = document.getElementById('cars-container');
  const countEl = document.getElementById('results-count');
  let debounce;

  const fetchCars = () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
      const params = new URLSearchParams({
        search: els.search.value.trim(),
        gear: els.gear.value,
        fuel: els.fuel.value,
        sort: els.sort.value,
        ajax: 1
      });

      container.innerHTML = '<div class="col-span-full flex justify-center"><div class="spinner"></div></div>';

      fetch(`?${params}`)
        .then(r => r.json())
        .then(data => {
          container.innerHTML = data.html || '<p class="col-span-full text-center text-gray-400 text-2xl">No cars found.</p>';
          countEl.textContent = `${data.count} cars in total`;
          AOS.refreshHard();
        });
    }, 400);
  };

  els.search.addEventListener('change', fetchCars);
  els.gear.addEventListener('change', fetchCars);
  els.fuel.addEventListener('change', fetchCars);
  els.sort.addEventListener('change', fetchCars);

  // Toast Alert
  <?php if ($alert): ?>
  (function() {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${$alert['type'] === 'success' ? 'success' : ($alert['type'] === 'danger' ? 'danger' : 'warning')} border-0`;
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body"><?= htmlspecialchars($alert['msg']) ?></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>`;
    document.getElementById('toastContainer').appendChild(toast);
    new bootstrap.Toast(toast, { delay: 5000 }).show();
  })();
  <?php endif; ?>

  // Day/Night Toggle
  const toggle = document.getElementById('dayModeToggle');
  if (localStorage.getItem('dayMode') === 'true') document.body.classList.add('day-mode');
  toggle.addEventListener('click', () => {
    document.body.classList.toggle('day-mode');
    localStorage.setItem('dayMode', document.body.classList.contains('day-mode'));
    toggle.querySelector('i').classList.toggle('bi-sun-fill');
    toggle.querySelector('i').classList.toggle('bi-moon-fill');
  });
</script>
</body>
</html>