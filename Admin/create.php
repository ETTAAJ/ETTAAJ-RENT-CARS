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
$success = false;

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

        if (empty($name)) $errors['name'] = "Car name is required.";
        if ($seats < 1) $errors['seats'] = "Seats must be at least 1.";
        if ($bags < 0) $errors['bags'] = "Bags cannot be negative.";
        if (!in_array($gear, ['Manual', 'Automatic'])) $errors['gear'] = "Invalid gear.";
        if (!in_array($fuel, ['Petrol', 'Diesel'])) $errors['fuel'] = "Invalid fuel.";
        if ($price_day <= 0) $errors['price_day'] = "Price per day must be positive.";

        $image = '';
        if (!empty($_FILES['image']['name'])) {
            $file = $_FILES['image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];

            if (!in_array($ext, $allowed)) {
                $errors['image'] = "Only JPG/PNG allowed.";
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $errors['image'] = "Image too large (max 2MB).";
            } elseif (!getimagesize($file['tmp_name'])) {
                $errors['image'] = "Not a valid image.";
            } else {
                $baseName = preg_replace('/[^a-zA-Z0-9\s-]/', '', $name);
                $baseName = trim(preg_replace('/\s+/', ' ', $baseName));
                $fileName = $baseName . '.' . $ext;

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
                    $errors['image'] = "Upload failed.";
                }
            }
        } else {
            $errors['image'] = "Image is required.";
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("
                INSERT INTO cars (name, image, seats, bags, gear, fuel, price_day, price_week, price_month)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $image, $seats, $bags, $gear, $fuel, $price_day, $price_week, $price_month]);
            $success = true;
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
  <title>Add New Car – Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
      --danger: #ef4444;
      --success: #10b981;
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

    .form-card {
      background: white;
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
      border: 1px solid var(--gray-200);
    }

    .form-label {
      font-weight: 600;
      color: var(--gray-700);
      margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
      border-radius: 0.5rem;
      padding: 0.65rem 1rem;
      font-size: 0.95rem;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
    }

    .image-preview {
      width: 100%;
      height: 200px;
      border: 2px dashed var(--gray-300);
      border-radius: 0.75rem;
      overflow: hidden;
      background: var(--gray-50);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      margin-top: 0.5rem;
    }

    .image-preview img {
      max-width: 100%;
      max-height: 100%;
      object-fit: cover;
    }

    .image-preview .placeholder {
      color: var(--gray-500);
      font-size: 0.9rem;
      text-align: center;
    }

    .btn-submit {
      background: var(--gold);
      color: #000;
      font-weight: 600;
      padding: 0.75rem 2rem;
      border-radius: 0.75rem;
      border: none;
      transition: all 0.2s;
    }

    .btn-submit:hover {
      background: var(--gold-dark);
      transform: translateY(-1px);
    }

    .error-text {
      color: var(--danger);
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }

    .back-btn {
      color: var(--gray-600);
      text-decoration: none;
      font-weight: 500;
    }

    .back-btn:hover {
      color: var(--gray-900);
    }

    @media (max-width: 768px) {
      .form-card { padding: 1.5rem; }
    }
  </style>
</head>
<body>

<!-- Header -->
<div class="page-header">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h1 class="h4 mb-0 fw-bold d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle text-gold"></i>
        Add New Car
      </h1>
      <a href="index.php" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
      </a>
    </div>
  </div>
</div>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="form-card">

        <?php if (!empty($errors) && !$success): ?>
          <div class="alert alert-danger">
            <strong>Fix the following errors:</strong>
            <ul class="mb-0 mt-2">
              <?php foreach ($errors as $field => $msg): ?>
                <li><?= htmlspecialchars($e) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="carForm">
          <input type="hidden" name="csrf" value="<?= $csrf ?>">

          <div class="row g-4">
            <!-- Left Column -->
            <div class="col-md-6">
              <div>
                <label class="form-label">Car Name *</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                <?php if (isset($errors['name'])): ?>
                  <div class="error-text"><?= htmlspecialchars($errors['name']) ?></div>
                <?php endif; ?>
                <small class="text-muted">Used for image filename (auto-sanitized).</small>
              </div>

              <div class="mt-4">
                <label class="form-label">Image (JPG/PNG, max 2MB) *</label>
                <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" required onchange="previewImage(this)">
                <?php if (isset($errors['image'])): ?>
                  <div class="error-text"><?= htmlspecialchars($errors['image']) ?></div>
                <?php endif; ?>
                <div class="image-preview" id="imagePreview">
                  <div class="placeholder">
                    <i class="bi bi-image fs-3"></i><br>
                    No image selected
                  </div>
                </div>
              </div>

              <div class="mt-4">
                <label class="form-label">Seats *</label>
                <input type="number" name="seats" class="form-control" min="1" value="<?= $_POST['seats'] ?? '4' ?>" required>
                <?php if (isset($errors['seats'])): ?>
                  <div class="error-text"><?= htmlspecialchars($errors['seats']) ?></div>
                <?php endif; ?>
              </div>

              <div class="mt-4">
                <label class="form-label">Bags *</label>
                <input type="number" name="bags" class="form-control" min="0" value="<?= $_POST['bags'] ?? '2' ?>" required>
                <?php if (isset($errors['bags'])): ?>
                  <div class="error-text"><?= htmlspecialchars($errors['bags']) ?></div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
              <div>
                <label class="form-label">Gear *</label>
                <select name="gear" class="form-select" required>
                  <option value="">-- Select --</option>
                  <option value="Manual" <?= ($_POST['gear'] ?? '') === 'Manual' ? 'selected' : '' ?>>Manual</option>
                  <option value="Automatic" <?= ($_POST['gear'] ?? '') === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                </select>
                <?php if (isset($errors['gear'])): ?>
                  <div class="error-text"><?= htmlspecialchars($errors['gear']) ?></div>
                <?php endif; ?>
              </div>

              <div class="mt-4">
                <label class="form-label">Fuel *</label>
                <select name="fuel" class="form-select" required>
                  <option value="">-- Select --</option>
                  <option value="Petrol" <?= ($_POST['fuel'] ?? '') === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
                  <option value="Diesel" <?= ($_POST['fuel'] ?? '') === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                </select>
                <?php if (isset($errors['fuel'])): ?>
                  <div class="error-text"><?= htmlspecialchars($errors['fuel']) ?></div>
                <?php endif; ?>
              </div>

              <div class="mt-4">
                <label class="form-label">Price per Day ($)*</label>
                <input type="number" step="0.01" name="price_day" class="form-control" value="<?= $_POST['price_day'] ?? '' ?>" required>
                <?php if (isset($errors['price_day'])): ?>
                  <div class="error-text"><?= htmlspecialchars($errors['price_day']) ?></div>
                <?php endif; ?>
              </div>

              <div class="mt-4">
                <label class="form-label">Price per Week ($)*</label>
                <input type="number" step="0.01" name="price_week" class="form-control" value="<?= $_POST['price_week'] ?? '' ?>" required>
              </div>

              <div class="mt-4">
                <label class="form-label">Price per Month ($)*</label>
                <input type="number" step="0.01" name="price_month" class="form-control" value="<?= $_POST['price_month'] ?? '' ?>" required>
              </div>
            </div>
          </div>

          <div class="text-center mt-5">
            <button type="submit" class="btn btn-submit btn-lg">
              <i class="bi bi-car-front"></i> Add Car to Inventory
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function previewImage(input) {
  const preview = document.getElementById('imagePreview');
  const file = input.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
    };
    reader.readAsDataURL(file);
  } else {
    preview.innerHTML = `
      <div class="placeholder">
        <i class="bi bi-image fs-3"></i><br>
        No image selected
      </div>
    `;
  }
}

// Auto-focus first field
document.addEventListener('DOMContentLoaded', () => {
  document.querySelector('input[name="name"]').focus();
});
</script>
</body>
</html>