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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin – Car Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <style>
    :root { --gold: #FFD700; --gold-dark: #e6c200; }
    .car-card { 
      background:#fff; border-radius:1rem; box-shadow:0 4px 6px rgba(0,0,0,.07);
      overflow:hidden; transition:.2s;
    }
    .car-card:hover { transform:translateY(-4px); box-shadow:0 12px 24px rgba(0,0,0,.12); }
    .text-gold { color:var(--gold); }
  </style>
</head>
<body class="bg-gray-50">

<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 text-2xl font-bold">Car Management</h2>
    <div>
      <a href="create.php" class="btn btn-success me-2">Add New Car</a>
      <a href="change_password.php" class="btn btn-warning me-2">
        Change Password
      </a>
      <a href="logout.php" class="btn btn-outline-danger">Logout</a>
    </div>
  </div>

  <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4">
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

    $stmt = $pdo->query("SELECT * FROM cars ORDER BY id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $img = getCarImage($row['image']);
        $placeholder = 'https://via.placeholder.com/300x200/e9ecef/6c757d?text=' . urlencode($row['name']);
        $src = $img ?: $placeholder;
    ?>
    <div class="col">
      <div data-aos="fade-up" class="car-card h-100 d-flex flex-column">
        <div class="ratio ratio-4x3 bg-light">
          <img src="<?= htmlspecialchars($src, ENT_QUOTES) ?>"
               alt="<?= htmlspecialchars($row['name']) ?>"
               class="w-100 h-100 object-fit-cover rounded-top"
               onerror="this.onerror=null; this.src='https://via.placeholder.com/300x200/cccccc/999999?text=No+Image';">
        </div>

        <div class="p-4 flex-grow-1 d-flex flex-column">
          <h5 class="mb-2 fw-semibold"><?= htmlspecialchars($row['name']) ?></h5>

          <div class="d-flex flex-wrap gap-2 text-muted small mb-3">
            <span class="d-flex align-items-center">
              <svg class="me-1" width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
              <?= (int)$row['seats'] ?> Seats
            </span>
            <span class="d-flex align-items-center">
              <svg class="me-1" width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
              <?= (int)$row['bags'] ?> Bags
            </span>
          </div>

          <div class="d-flex justify-content-between text-muted small mb-3">
            <span><?= htmlspecialchars($row['gear']) ?></span>
            <span><?= htmlspecialchars($row['fuel']) ?></span>
          </div>

          <div class="border-top pt-3 mt-auto">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div>
                <span class="fs-4 fw-bold text-gold">$<?= number_format((float)$row['price_day']) ?></span>
                <span class="text-muted small">/day</span>
              </div>
            </div>
            <div class="text-muted small mb-3">
              Week: $<?= number_format((float)$row['price_week']) ?> |
              Month: $<?= number_format((float)$row['price_month']) ?>
            </div>

            <div class="d-flex gap-2">
              <a href="edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-primary flex-fill">Edit</a>
              <form action="delete.php" method="POST" class="d-inline flex-fill">
                <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                <input type="hidden" name="csrf" value="<?= $csrf ?>">
                <button type="submit" class="btn btn-sm btn-danger w-100"
                        onclick="return confirm('Delete this car?')">
                  Delete
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</div>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init({once:true, duration:800});</script>
</body>
</html>