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
   3. HANDLE FORM SUBMISSIONS
   ------------------------------------------------- */
$alert = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $csrf) {
        $alert = ['type' => 'danger', 'msg' => 'Invalid security token.'];
    } else {
        $action = $_POST['action'];
        
        if ($action === 'update') {
            $id = (int)$_POST['id'];
            $rate = (float)$_POST['rate_to_mad'];
            
            if ($rate > 0) {
                $stmt = $pdo->prepare("UPDATE currencies SET rate_to_mad = ? WHERE id = ?");
                $stmt->execute([$rate, $id]);
                $alert = ['type' => 'success', 'msg' => 'Currency rate updated successfully!'];
            } else {
                $alert = ['type' => 'danger', 'msg' => 'Rate must be greater than 0.'];
            }
        } elseif ($action === 'toggle') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE currencies SET is_active = NOT is_active WHERE id = ?");
            $stmt->execute([$id]);
            $alert = ['type' => 'success', 'msg' => 'Currency status updated!'];
        }
    }
}

/* -------------------------------------------------
   4. FETCH ALL CURRENCIES
   ------------------------------------------------- */
$stmt = $pdo->query("SELECT * FROM currencies ORDER BY code ASC");
$currencies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Management - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-color: #FFB22C;
            --bg-dark: #1a1a1a;
            --card: #2a2a2a;
            --border: #3a3a3a;
        }
        body {
            background: var(--bg-dark);
            color: #fff;
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-900 text-white">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-yellow-400">
                <i class="bi bi-currency-exchange"></i> Currency Management
            </h1>
            <a href="index.php" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition">
                <i class="bi bi-arrow-left"></i> Back to Cars
            </a>
        </div>

        <!-- Alert -->
        <?php if ($alert): ?>
        <div class="mb-6 p-4 rounded-lg <?= $alert['type'] === 'success' ? 'bg-green-600' : 'bg-red-600' ?>">
            <?= htmlspecialchars($alert['msg']) ?>
        </div>
        <?php endif; ?>

        <!-- Info Box -->
        <div class="mb-6 p-4 bg-blue-900/50 border border-blue-500 rounded-lg">
            <p class="text-sm">
                <i class="bi bi-info-circle"></i> 
                <strong>Note:</strong> All rates are relative to MAD (Moroccan Dirham). 
                For example, if USD rate is 10.0, it means 1 USD = 10 MAD.
            </p>
        </div>

        <!-- Currencies Table -->
        <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left">Code</th>
                        <th class="px-6 py-4 text-left">Name</th>
                        <th class="px-6 py-4 text-left">Symbol</th>
                        <th class="px-6 py-4 text-left">Rate to MAD</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($currencies as $currency): ?>
                    <tr class="border-t border-gray-700 hover:bg-gray-750">
                        <td class="px-6 py-4">
                            <span class="font-bold text-yellow-400"><?= htmlspecialchars($currency['code']) ?></span>
                        </td>
                        <td class="px-6 py-4"><?= htmlspecialchars($currency['name']) ?></td>
                        <td class="px-6 py-4 text-xl"><?= htmlspecialchars($currency['symbol']) ?></td>
                        <td class="px-6 py-4">
                            <form method="POST" class="inline-flex items-center gap-2">
                                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $currency['id'] ?>">
                                <input type="number" 
                                       name="rate_to_mad" 
                                       value="<?= htmlspecialchars($currency['rate_to_mad']) ?>" 
                                       step="0.0001"
                                       min="0.0001"
                                       class="w-32 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:border-yellow-400"
                                       required>
                                <button type="submit" 
                                        class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-black font-bold rounded transition">
                                    <i class="bi bi-check-lg"></i> Update
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?= $currency['is_active'] ? 'bg-green-600' : 'bg-gray-600' ?>">
                                <?= $currency['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $currency['id'] ?>">
                                <button type="submit" 
                                        class="px-4 py-2 <?= $currency['is_active'] ? 'bg-gray-600 hover:bg-gray-500' : 'bg-green-600 hover:bg-green-500' ?> rounded transition">
                                    <i class="bi bi-<?= $currency['is_active'] ? 'pause' : 'play' ?>-fill"></i>
                                    <?= $currency['is_active'] ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Example Conversion -->
        <div class="mt-8 p-6 bg-gray-800 rounded-lg">
            <h2 class="text-xl font-bold mb-4 text-yellow-400">
                <i class="bi bi-calculator"></i> Example Conversion
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php 
                $exampleAmount = 1000; // 1000 MAD
                foreach ($currencies as $curr): 
                    if (!$curr['is_active']) continue;
                    $converted = $exampleAmount / $curr['rate_to_mad'];
                ?>
                <div class="p-4 bg-gray-700 rounded">
                    <p class="text-sm text-gray-400"><?= $exampleAmount ?> MAD =</p>
                    <p class="text-2xl font-bold text-yellow-400">
                        <?= $curr['symbol'] ?><?= number_format($converted, 2) ?> <?= $curr['code'] ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>

