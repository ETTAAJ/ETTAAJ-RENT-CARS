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
   2. CSRF TOKEN
   ------------------------------------------------- */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

/* -------------------------------------------------
   3. GET CAR BY ID
   ------------------------------------------------- */
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) {
    die("Car not found.");
}

/* -------------------------------------------------
   4. HANDLE FORM SUBMISSION
   ------------------------------------------------- */
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!hash_equals($csrf, $_POST['csrf'] ?? '')) {
        $errors[] = "Invalid request. Please try again.";
    } else {
        $name        = trim($_POST['name'] ?? '');
        $seats       = (int)($_POST['seats'] ?? 0);
        $bags        = (int)($_POST['bags'] ?? 0);
        $gear        = $_POST['gear'] ?? '';
        $fuel        = $_POST['fuel'] ?? '';
        $price_day   = (float)($_POST['price_day'] ?? 0);
        $price_week  = (float)($_POST['price_week'] ?? 0);
        $price_month = (float)($_POST['price_month'] ?? 0);

        // Validation
        if (empty($name)) $errors[] = "Car name is required.";
        if ($seats < 1) $errors[] = "Seats must be at least 1.";
        if ($bags < 0) $errors[] = "Bags cannot be negative.";
        if (!in_array($gear, ['Manual', 'Automatic'])) $errors[] = "Invalid gear type.";
        if (!in_array($fuel, ['Petrol', 'Diesel'])) $errors[] = "Invalid fuel type.";
        if ($price_day <= 0) $errors[] = "Price per day must be positive.";

        $image = $car['image']; // Default: keep old

        // === NEW IMAGE UPLOAD ===
        if (!empty($_FILES['image']['name'])) {
            $file = $_FILES['image'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];

            if (!in_array($ext, $allowed)) {
                $errors[] = "Only JPG, JPEG, PNG allowed.";
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $errors[] = "Image must be under 2 MB.";
            } elseif (!getimagesize($file['tmp_name'])) {
                $errors[] = "Invalid image file.";
            } else {
                // Delete old image
                if ($car['image']) {
                    $oldPath = __DIR__ . '/../uploads/' . $car['image'];
                    if (file_exists($oldPath)) @unlink($oldPath);
                }

                // === NAME IMAGE AFTER CAR ===
                $baseName = preg_replace('/[^a-zA-Z0-9\s-]/', '', $name);
                $baseName = trim(preg_replace('/\s+/', ' ', $baseName));
                $fileName = $baseName . '.' . $ext;

                // Prevent overwrite
                $counter = 1;
                $targetPath = __DIR__ . '/../uploads/' . $fileName;
                while (file_exists($targetPath)) {
                    $fileName = $baseName . " ($counter)." . $ext;
                    $targetPath = __DIR__ . '/../uploads/' . $fileName;
                    $counter++;
                }

                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $image = $fileName;
                } else {
                    $errors[] = "Failed to upload image.";
                }
            }
        }
        // === NO NEW IMAGE → RENAME OLD ONE TO MATCH NAME ===
        else {
            if ($car['image']) {
                $oldPath = __DIR__ . '/../uploads/' . $car['image'];
                if (file_exists($oldPath)) {
                    $ext = pathinfo($car['image'], PATHINFO_EXTENSION);
                    $baseName = preg_replace('/[^a-zA-Z0-9\s-]/', '', $name);
                    $baseName = trim(preg_replace('/\s+/', ' ', $baseName));
                    $newName = $baseName . '.' . $ext;

                    $counter = 1;
                    $newPath = __DIR__ . '/../uploads/' . $newName;
                    while (file_exists($newPath)) {
                        $newName = $baseName . " ($counter)." . $ext;
                        $newPath = __DIR__ . '/../uploads/' . $newName;
                        $counter++;
                    }

                    if (rename($oldPath, $newPath)) {
                        $image = $newName;
                    }
                }
            }
        }

        // === SAVE TO DB IF NO ERRORS ===
        if (empty($errors)) {
            $stmt = $pdo->prepare("
                UPDATE cars SET 
                    name = ?, image = ?, seats = ?, bags = ?, gear = ?, fuel = ?,
                    price_day = ?, price_week = ?, price_month = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $name, $image, $seats, $bags, $gear, $fuel,
                $price_day, $price_week, $price_month, $id
            ]);
            header("Location: index.php?success=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Car</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .current-img { max-width: 150px; border-radius: 8px; }
    .alert { margin-top: 1rem; }
  </style>
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Car</h2>
    <a href="index.php" class="btn btn-secondary">Back to List</a>
  </div>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
    <input type="hidden" name="csrf" value="<?= $csrf ?>">

    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">Car Name *</label>
          <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($car['name']) ?>" required>
          <small class="text-muted">Image will be renamed to match this name.</small>
        </div>

        <div class="mb-3">
          <label class="form-label">Current Image</label><br>
          <?php if ($car['image']): ?>
            <img src="../uploads/<?= htmlspecialchars($car['image']) ?>?v=<?= time() ?>"
                 alt="Current" class="current-img img-thumbnail">
            <p class="small text-muted mt-1">File: <strong><?= htmlspecialchars($car['image']) ?></strong></p>
          <?php else: ?>
            <p class="text-muted">No image</p>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label class="form-label">Replace Image (optional)</label>
          <input type="file" name="image" class="form-control" accept="image/jpeg,image/png">
          <small class="text-muted">JPG/PNG only, max 2MB</small>
        </div>

        <div class="mb-3">
          <label class="form-label">Seats *</label>
          <input type="number" name="seats" class="form-control" value="<?= $car['seats'] ?>" min="1" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Bags *</label>
          <input type="number" name="bags" class="form-control" value="<?= $car['bags'] ?>" min="0" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">Gear *</label>
          <select name="gear" class="form-select" required>
            <option value="Manual"   <?= $car['gear'] == 'Manual' ? 'selected' : '' ?>>Manual</option>
            <option value="Automatic" <?= $car['gear'] == 'Automatic' ? 'selected' : '' ?>>Automatic</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Fuel *</label>
          <select name="fuel" class="form-select" required>
            <option value="Petrol" <?= $car['fuel'] == 'Petrol' ? 'selected' : '' ?>>Petrol</option>
            <option value="Diesel" <?= $car['fuel'] == 'Diesel' ? 'selected' : '' ?>>Diesel</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Price per Day ($)*</label>
          <input type="number" step="0.01" name="price_day" class="form-control" value="<?= $car['price_day'] ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Price per Week ($)*</label>
          <input type="number" step="0.01" name="price_week" class="form-control" value="<?= $car['price_week'] ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Price per Month ($)*</label>
          <input type="number" step="0.01" name="price_month" class="form-control" value="<?= $car['price_month'] ?>" required>
        </div>
      </div>
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary btn-lg px-5">Update Car</button>
    </div>
  </form>
</div>
</body>
</html>