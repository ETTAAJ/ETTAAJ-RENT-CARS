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
   2. REGENERATE SESSION ID every 10 min
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
    $alert = ['type' => 'warning', 'msg' => 'An error occurred. Please try again.'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin – Car Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    :root {
      --gold: #FFD700;
      --gold-dark: #e6c200;
      --gray-50: #f9fafb;
      --gray-100: #f3f4f6;
      --gray-200: #e5e7eb;
      --gray-600: #4b5563;
      --gray-700: #374151;
      --gray-900: #111827;
      --success: #10b981;
      --danger: #ef4444;
      --warning: #f59e0b;
    }

    * { font-family: 'Inter', sans-serif; }
    body { background: var(--gray-50); color: var(--gray-900); }

    .page-header {
      background: white;
      padding: 1.5rem 0;
      border-bottom: 1px solid var(--gray-200);
      margin-bottom: 2rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .car-card {
      background: white;
      border-radius: 1rem;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
      transition: all 0.25s ease;
      border: 1px solid var(--gray-200);
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .car-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 30px rgba(0,0,0,0.12);
      border-color: var(--gold);
    }

    .car-image {
      position: relative;
      overflow: hidden;
      background: var(--gray-100);
    }

    .car-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .car-card:hover .car-image img {
      transform: scale(1.05);
    }

    .car-info {
      padding: 1.25rem;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }

    .car-name {
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: var(--gray-900);
      display: -webkit-box;
      -webkit-line-clamp: 1;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .meta-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-bottom: 0.75rem;
      font-size: 0.875rem;
      color: var(--gray-600);
    }

    .tag {
      display: flex;
      align-items: center;
      gap: 0.25rem;
      background: var(--gray-100);
      padding: 0.25rem 0.5rem;
      border-radius: 0.5rem;
      font-weight: 500;
    }

    .price-section {
      margin-top: auto;
      padding-top: 1rem;
      border-top: 1px dashed var(--gray-200);
    }

    .price-main {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--gold);
      margin-bottom: 0.25rem;
    }

    .price-sub {
      font-size: 0.8rem;
      color: var(--gray-600);
    }

    .action-buttons {
      display: flex;
      gap: 0.5rem;
      margin-top: 1rem;
    }

    .btn-edit {
      background: #3b82f6;
      color: white;
      border: none;
      flex: 1;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .btn-delete {
      background: #ef4444;
      color: white;
      border: none;
      flex: 1;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .btn-edit:hover { background: #2563eb; }
    .btn-delete:hover { background: #dc2626; }

    .empty-state {
      text-align: center;
      padding: 4rem 1rem;
      color: var(--gray-600);
    }

    .empty-state i {
      font-size: 3rem;
      color: var(--gray-300);
      margin-bottom: 1rem;
    }

    /* === TOAST ALERTS === */
    .toast-container {
      position: fixed;
      top: 1rem;
      right: 1rem;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .toast {
      min-width: 300px;
      max-width: 400px;
      background: white;
      border-radius: 0.75rem;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      overflow: hidden;
      border-left: 5px solid;
      animation: slideIn 0.4s ease, fadeOut 0.5s ease 4.5s forwards;
      opacity: 0;
    }

    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    @keyframes fadeOut {
      to { transform: translateX(120%); opacity: 0; }
    }

    .toast.success { border-left-color: var(--success); }
    .toast.danger { border-left-color: var(--danger); }
    .toast.warning { border-left-color: var(--warning); }

    .toast-header {
      padding: 0.75rem 1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 600;
      font-size: 0.95rem;
    }

    .toast.success .toast-header { color: var(--success); }
    .toast.danger .toast-header { color: var(--danger); }
    .toast.warning .toast-header { color: var(--warning); }

    .toast-body {
      padding: 0 1rem 1rem 1rem;
      font-size: 0.875rem;
      color: var(--gray-700);
    }

    .toast-close {
      margin-left: auto;
      background: none;
      border: none;
      font-size: 1.2rem;
      color: var(--gray-500);
      cursor: pointer;
      padding: 0;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      transition: all 0.2s;
    }

    .toast-close:hover {
      background: var(--gray-100);
      color: var(--gray-700);
    }

    @media (max-width: 576px) {
      .toast-container { top: 0.5rem; right: 0.5rem; left: 0.5rem; }
      .toast { min-width: auto; }
    }
  </style>
</head>
<body>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Header -->
<div class="page-header">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <h2 class="h4 mb-0 fw-bold text-gray-900 d-flex align-items-center gap-2">
        <i class="bi bi-car-front-fill" style="color: var(--gold);"></i>
        Car Management
      </h2>
      <div class="d-flex gap-2 flex-wrap">
        <a href="create.php" class="btn btn-success btn-sm">
          <i class="bi bi-plus-circle"></i> Add New Car
        </a>
        <a href="change_password.php" class="btn btn-warning btn-sm text-white">
          <i class="bi bi-shield-lock"></i> Password
        </a>
        <a href="logout.php" class="btn btn-outline-danger btn-sm">
          <i class="bi bi-box-arrow-right"></i> Logout
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container pb-5">
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php
    function getCarImage(string $filename): string {
        if (empty($filename)) return '';
        $safe = basename($filename);
        $path = '../uploads/' . $safe;
        $full = __DIR__ . '/' . $path;
        if (file_exists($full)) {
            return $path . '?v=' . filemtime($full);
        }
        return '';
    }

    $stmt = $pdo->query("SELECT * FROM cars ORDER BY id DESC");
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cars)): ?>
      <div class="col-12">
        <div class="empty-state">
          <i class="bi bi-inbox"></i>
          <h5>No cars added yet</h5>
          <p class="text-muted">Click "Add New Car" to get started.</p>
        </div>
      </div>
    <?php else: ?>
      <?php foreach ($cars as $row):
        $img = getCarImage($row['image']);
        $placeholder = 'https://via.placeholder.com/400x300/e5e7eb/9ca3af?text=' . urlencode($row['name']);
        $src = $img ?: $placeholder;
      ?>
        <div class="col">
          <div data-aos="fade-up" class="car-card">
            <!-- Image -->
            <div class="car-image ratio ratio-4x3">
              <img src="<?= htmlspecialchars($src, ENT_QUOTES) ?>"
                   alt="<?= htmlspecialchars($row['name']) ?>"
                   onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300/dddddd/666666?text=No+Image';">
            </div>

            <!-- Info -->
            <div class="car-info">
              <h3 class="car-name"><?= htmlspecialchars($row['name']) ?></h3>

              <div class="meta-tags">
                <span class="tag">
                  <i class="bi bi-person"></i> <?= (int)$row['seats'] ?> Seats
                </span>
                <span class="tag">
                  <i class="bi bi-briefcase"></i> <?= (int)$row['bags'] ?> Bags
                </span>
              </div>

              <div class="d-flex justify-content-between text-muted small mb-3">
                <span class="tag">
                  <i class="bi bi-gear"></i> <?= htmlspecialchars($row['gear']) ?>
                </span>
                <span class="tag">
                  <i class="bi bi-fuel-pump"></i> <?= htmlspecialchars($row['fuel']) ?>
                </span>
              </div>

              <!-- Price -->
              <div class="price-section">
                <div class="price-main">$<?= number_format((float)$row['price_day']) ?><span class="text-muted small">/day</span></div>
                <div class="price-sub">
                  Week: <strong>$<?= number_format((float)$row['price_week']) ?></strong> |
                  Month: <strong>$<?= number_format((float)$row['price_month']) ?></strong>
                </div>
              </div>

              <!-- Actions -->
              <div class="action-buttons">
                <a href="edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-edit btn-sm">
                  <i class="bi bi-pencil"></i> Edit
                </a>
                <form action="delete.php" method="POST" class="d-inline w-100">
                  <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                  <input type="hidden" name="csrf" value="<?= $csrf ?>">
                  <button type="submit" class="btn btn-delete btn-sm w-100"
                          onclick="return confirm('Delete this car permanently?')">
                    <i class="bi bi-trash"></i> Delete
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once: true, duration: 600, easing: 'ease-out-quart' });

  // Image loading shimmer
  document.addEventListener('DOMContentLoaded', () => {
    const images = document.querySelectorAll('.car-image img');
    images.forEach(img => {
      img.onload = () => img.closest('.car-image').style.background = 'transparent';
    });
  });

  // === TOAST ALERT SYSTEM ===
  <?php if ($alert): ?>
  (function() {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast <?= $alert['type'] ?>`;
    toast.innerHTML = `
      <div class="toast-header">
        <i class="bi ${getIcon(<?= json_encode($alert['type']) ?>)}"></i>
        <span>${getTitle(<?= json_encode($alert['type']) ?>)}</span>
        <button type="button" class="toast-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
      </div>
      <div class="toast-body">
        <?= htmlspecialchars($alert['msg']) ?>
      </div>
    `;
    container.appendChild(toast);

    // Auto-remove after 5s
    setTimeout(() => {
      if (toast.parentElement) toast.remove();
    }, 5000);

    function getIcon(type) {
      return type === 'success' ? 'bi-check-circle-fill' :
             type === 'danger' ? 'bi-x-circle-fill' :
             'bi-exclamation-triangle-fill';
    }

    function getTitle(type) {
      return type === 'success' ? 'Success' :
             type === 'danger' ? 'Deleted' :
             'Warning';
    }
  })();
  <?php endif; ?>
</script>
</body>
</html>