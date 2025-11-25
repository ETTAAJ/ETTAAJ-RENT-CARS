<?php 
require_once 'init.php';
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" class="scroll-smooth" dir="<?= getDir() ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <!-- FULLY OPTIMIZED FOR MARRAKECH -->
  <title>Contact ETTAAJ Rent Cars | Car Rental Marrakech Airport - WhatsApp +212 772 331 080</title>
  <meta name="description" content="Contact ETTAAJ Rent Cars - Best car rental in Marrakech Airport. Instant WhatsApp reply 24/7, free airport delivery, no deposit. Call or message +212 772 331 080 now!" />
  <meta name="keywords" content="contact ettaaj rent cars, car rental marrakech airport contact, car rental marrakech whatsapp, car rental in marrakech airport phone number, best car rental marrakech contact, car rental marrakech gueliz office" />
  <meta name="author" content="ETTAAJ Rent Cars" />
  <meta name="robots" content="index, follow" />
  <meta name="geo.region" content="MA" />
  <meta name="geo.placename" content="Marrakech" />
  <meta name="geo.position" content="31.6069;-8.0363" />
  <meta name="ICBM" content="31.6069, -8.0363" />
  <link rel="icon" href="pub_img/ETTAAJ-RENT-CARS.jpg">

  <link rel="canonical" href="https://www.ettaajrentcars.ma/contact.php" />

  <meta property="og:title" content="Contact ETTAAJ Rent Cars | Car Rental Marrakech Airport 24/7" />
  <meta property="og:description" content="Free airport delivery • No deposit • Instant WhatsApp +212 772 331 080" />
  <meta property="og:url" content="https://www.ettaajrentcars.com/contact.php" />
  <meta property="og:image" content="https://www.ettaajrentcars.com/pub_img/contact-og-marrakech.jpg" />
  <meta property="og:type" content="website" />

  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Contact ETTAAJ - Best Car Rental Marrakech Airport" />
  <meta name="twitter:description" content="WhatsApp +212 772 331 080 • 24/7 Support • Free Delivery at Menara Airport" />

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "CarRentalService",
    "name": "ETTAAJ Rent Cars Marrakech",
    "url": "https://www.ettaajrentcars.ma",
    "telephone": "+212772331080",
    "image": "https://www.ettaajrentcars.ma/pub_img/ETTAAJ-RENT-CARS.jpg",
    "description": "Contact the best car rental in Marrakech Airport. 24/7 WhatsApp support, free delivery, no deposit rentals.",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Marrakech Menara Airport & Gueliz Office",
      "addressLocality": "Marrakech",
      "addressCountry": "MA"
    },
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+212772331080",
      "contactType": "Customer Service",
      "areaServed": "MA",
      "availableLanguage": ["English", "French", "Arabic"]
    },
    "openingHours": "Mo-Su 00:00-23:59"
  }
  </script>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: { colors: { gold: '#FFD700' } } }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    :root {
      --bg: #36454F; --bg-dark: #2C3A44; --card: #36454F; --card-dark: #2C3A44;
      --border: #4A5A66; --primary: #FFFFFF; --muted: #D1D5DB; --gold: #FFD700;
      --card-dark-gradient: linear-gradient(135deg, #0B0B0C 0%, #121212 55%, #C6A667 120%);
    }
    body { background-color: var(--bg); color: var(--primary); font-family: 'Inter', sans-serif; }
    .bg-card { background-color: var(--card); }
    .bg-card-dark { background-color: var(--card-dark); }
    .car-card-bg { background: var(--card-dark-gradient); }
    .border-border { border-color: var(--border); }
    .text-primary { color: var(--primary); }
    .text-muted { color: var(--muted); }
    .text-gold { color: var(--gold); }
    /* Phone number always LTR */
    .phone-number { direction: ltr; display: inline-block; }
  </style>
</head>
<body class="min-h-screen">

<?php include 'header.php'; ?>

<main class="max-w-7xl mx-auto px-6 py-16 lg:py-24">

  <div data-aos="fade-up" class="text-center mb-16">
    <h1 class="text-5xl md:text-6xl font-bold mb-4 text-primary">
      <?= $text['contact'] ?> <span class="text-gold">ETTAAJ Rent Cars</span> Marrakech
    </h1>
    <p class="text-xl text-muted max-w-3xl mx-auto">
      <?= $text['contact_subtitle'] ?>
    </p>
  </div>

  <div class="grid lg:grid-cols-2 gap-12 xl:gap-20">

    <!-- Left: Contact Info -->
    <div data-aos="fade-right" class="space-y-10">

      <h2 class="text-3xl font-bold"><?= $text['were_here_24_7'] ?></h2>

      <div class="rounded-2xl overflow-hidden shadow-2xl border border-gray-200 dark:border-gray-800">
        <iframe 
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3398.987465432177!2d-8.003892684897948!3d31.634509981337!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xdafee9a3c2c4d8f%3A0x8e8f8f8f8f8f8f8f!2sETTAAJ%20RENT%20CARS%20Marrakech!5e0!3m2!1sen!2sma!4v1732062800000!5m2!1sen!2sma"
          width="100%" height="380" style="border:0;" allowfullscreen="" loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>

      <div class="space-y-8">
        <div class="flex items-start gap-4">
          <div class="bg-gold/10 dark:bg-gold/20 p-4 rounded-xl border border-gold/30">
            <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <div>
            <p class="font-bold"><?= $text['office_airport'] ?></p>
            <p class="text-gold font-semibold"><?= $text['marrakech_airport'] ?></p>
          </div>
        </div>

        <div class="flex items-start gap-4">
          <div class="bg-gold/10 dark:bg-gold/20 p-4 rounded-xl border border-gold/30">
            <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
          </div>
          <div>
            <p class="font-bold"><?= $text['phone_whatsapp_24_7'] ?></p>
            <a href="https://wa.me/212772331080" class="text-gold text-2xl font-bold hover:underline phone-number">+212 772 331 080</a>
          </div>
        </div>
      </div>

      <div class="flex gap-4">
        <a href="https://instagram.com/ettaaj.rentcars" target="_blank" class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center hover:scale-110 transition">
          <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163C8.74 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98C23.986 15.668 24 15.259 24 12c0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zm0 10.162a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 11-2.88 0 1.44 1.44 0 012.88 0z"/></svg>
        </a>
        <a href="https://facebook.com/profile.php?id=61559816313152" target="_blank" class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center hover:scale-110 transition">
          <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
        </a>
        <a href="https://wa.me/212772331080" target="_blank" class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center hover:scale-110 transition">
          <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.198-.347.223-.644.075-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.297-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.844m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.884 3.488"/></svg>
        </a>
      </div>
    </div>

    <!-- Right: Contact Form -->
    <div data-aos="fade-left">
      <form action="contact-process.php" method="POST" class="bg-card/95 backdrop-blur-xl border border-border rounded-3xl p-8 lg:p-12 shadow-2xl">

        <div class="relative mb-8">
          <input type="text" name="name" id="name" required placeholder=" "
                 class="peer w-full px-5 py-5 bg-white/10 border border-border rounded-2xl text-primary text-lg focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition placeholder-transparent">
            <label for="name" class="absolute <?= $lang === 'ar' ? 'right-5' : 'left-5' ?> -top-3 bg-[var(--card)] px-3 text-sm font-medium text-gold transition-all peer-placeholder-shown:top-5 peer-placeholder-shown:text-lg peer-placeholder-shown:text-muted peer-focus:-top-3 peer-focus:text-gold peer-focus:text-sm">
            <?= $text['your_name'] ?>
          </label>
        </div>

        <div class="relative mb-8">
          <input type="email" name="email" id="email" required placeholder=" "
                 class="peer w-full px-5 py-5 bg-white/10 border border-border rounded-2xl text-primary text-lg focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition placeholder-transparent">
            <label for="email" class="absolute <?= $lang === 'ar' ? 'right-5' : 'left-5' ?> -top-3 bg-[var(--card)] px-3 text-sm font-medium text-gold transition-all peer-placeholder-shown:top-5 peer-placeholder-shown:text-lg peer-placeholder-shown:text-muted peer-focus:-top-3 peer-focus:text-gold peer-focus:text-sm">
            <?= $text['your_email'] ?>
          </label>
        </div>

        <!-- FIXED & PERFECT "Your Message" LABEL -->
        <div class="relative mb-10">
          <textarea 
            name="message" 
            id="message" 
            rows="6" 
            required 
            placeholder=" "
            class="peer w-full px-5 pt-8 pb-5 bg-white/10 border border-border rounded-2xl text-white text-lg focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition resize-none placeholder-transparent">
          </textarea>
          <label 
            for="message" 
            class="absolute <?= $lang === 'ar' ? 'right-5' : 'left-5' ?> top-4 origin-<?= $lang === 'ar' ? 'right' : 'left' ?> transform -translate-y-2 scale-75 
                    bg-[var(--card)] px-3 text-sm font-medium text-gold 
                   transition-all duration-200 
                   peer-placeholder-shown:top-1/2 peer-placeholder-shown:-translate-y-1/2 
                   peer-placeholder-shown:scale-100 peer-placeholder-shown:text-gray-400 
                   peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-gold">
            <?= $text['your_message'] ?>
          </label>
        </div>

        <button type="submit" class="w-full bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-500 text-black font-bold text-lg py-5 rounded-2xl shadow-xl transition-all transform hover:scale-[1.02] active:scale-98">
          <?= $text['send_message'] ?>
        </button>
      </form>

      <div class="text-center mt-10">
        <p class="text-gray-200 mb-5 text-lg"><?= $text['fastest_way'] ?></p>
        <a href="https://wa.me/212772331080?text=Hi%20ETTAAJ%20RENT%20CARS!%0AI%20just%20landed%20at%20Marrakech%20Airport%20and%20need%20a%20car!" 
           target="_blank"
           class="inline-flex items-center gap-3 bg-green-500 hover:bg-green-600 text-white font-bold py-5 px-10 rounded-2xl shadow-xl transition transform hover:scale-105">
          <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.198-.347.223-.644.075-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.297-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.844m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.884 3.488"/></svg>
          <?= $text['chat_whatsapp'] ?>
        </a>
      </div>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once: true, duration: 900 });
</script>
</body>
</html>