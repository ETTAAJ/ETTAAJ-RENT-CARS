<?php
require 'config.php';
if ($_POST) {
  $car_id = $_POST['car_id'];
  $pickup = $_POST['pickup'];
  $return = $_POST['return'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];

  // In real app: save to `bookings` table
  // For now: show confirmation
}
?>
<?php include 'header.php'; ?>

<div class="max-w-xl mx-auto text-center py-20">
  <h1 class="text-4xl font-bold text-gold mb-4">Booking Confirmed!</h1>
  <p class="text-lg text-gray-700">Thank you, <strong><?php echo htmlspecialchars($name); ?></strong>.</p>
  <p class="mt-4">We'll contact you at <strong><?php echo htmlspecialchars($email); ?></strong> soon.</p>
  <a href="index.php" class="inline-block mt-8 bg-gold hover:bg-gold-dark text-white font-bold py-3 px-8 rounded-full">
    Back to Home
  </a>
</div>

<?php include 'footer.php'; ?>
