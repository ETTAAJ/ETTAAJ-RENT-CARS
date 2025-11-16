<?php
require_once 'config.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrf, $_POST['csrf'] ?? '')) {
        $errors[] = "Invalid request.";
    } else {
        $name = trim($_POST['name'] ?? '');
        $seats = (int)($_POST['seats'] ?? 0);
        $bags = (int)($_POST['bags'] ?? 0);
        $gear = $_POST['gear'] ?? '';
        $fuel = $_POST['fuel'] ?? '';
        $price_day = (float)($_POST['price_day'] ?? 0);
        $price_week = (float)($_POST['price_week'] ?? 0);
        $price_month = (float)($_POST['price_month'] ?? 0);

        if (empty($name)) $errors[] = "Car name is required.";
        if ($seats < 1) $errors[] = "Seats must be at least 1.";
        if ($bags < 0) $errors[] = "Bags cannot be negative.";
        if (!in_array($gear, ['Manual', 'Automatic'])) $errors[] = "Invalid gear.";
        if (!in_array($fuel, ['Petrol', 'Diesel'])) $errors[] = "Invalid fuel.";
        if ($price_day <= 0) $errors[] = "Price per day must be positive.";

        $image = '';
        if (!empty($_FILES['image']['name'])) {
            $file = $_FILES['image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];

            if (!in_array($ext, $allowed)) {
                $errors[] = "Only JPG/PNG allowed.";
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $errors[] = "Image too large (max 2MB).";
            } elseif (!getimagesize($file['tmp_name'])) {
                $errors[] = "Not a valid image.";
            } else {
                // Sanitize car name for filename
                $baseName = preg_replace('/[^a-zA-Z0-9\s-]/', '', $name);
                $baseName = trim(preg_replace('/\s+/', ' ', $baseName));
                $fileName = $baseName . '.' . $ext;

                // Prevent overwrite: add number if exists
                $counter = 1;
                $original = $fileName;
                $targetPath = __DIR__ . '/../uploads/' . $fileName;
                while (file_exists($targetPath)) {
                    $fileName = $baseName . " ($counter)." . $ext;
                    $targetPath = __DIR__ . '/../uploads/' . $fileName;
                    $counter++;
                }

                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $image = $fileName;
                } else {
                    $errors[] = "Upload failed.";
                }
            }
        } else {
            $errors[] = "Image is required.";
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("
                INSERT INTO cars (name, image, seats, bags, gear, fuel, price_day, price_week, price_month)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $image, $seats, $bags, $gear, $fuel, $price_day, $price_week, $price_month]);
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
  <title>Add New Car</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2>Add New Car <a href="index.php" class="btn btn-secondary float-end">Back</a></h2>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
    <input type="hidden" name="csrf" value="<?= $csrf ?>">

    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">Car Name *</label>
          <input type="text" name="name" class="form-control" required>
          <small class="text-muted">This will be used as image filename.</small>
        </div>

        <div class="mb-3">
          <label class="form-label">Image (JPG/PNG, max 2MB) *</label>
          <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Seats *</label>
          <input type="number" name="seats" class="form-control" min="1" value="4" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Bags *</label>
          <input type="number" name="bags" class="form-control" min="0" value="2" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">Gear *</label>
          <select name="gear" class="form-select" required>
            <option value="Manual">Manual</option>
            <option value="Automatic">Automatic</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Fuel *</label>
          <select name="fuel" class="form-select" required>
            <option value="Petrol">Petrol</option>
            <option value="Diesel">Diesel</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Price per Day ($)*</label>
          <input type="number" step="0.01" name="price_day" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Price per Week ($)*</label>
          <input type="number" step="0.01" name="price_week" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Price per Month ($)*</label>
          <input type="number" step="0.01" name="price_month" class="form-control" required>
        </div>
      </div>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-success btn-lg px-5">Add Car</button>
    </div>
  </form>
</div>
</body>
</html>