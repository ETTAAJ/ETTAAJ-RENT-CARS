<?php 
require_once 'init.php';
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" class="scroll-smooth" <?= $lang === 'ar' ? 'dir="rtl"' : 'dir="ltr"' ?>>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rental Guide – ET TAAJ RENT CARS</title>
  <link rel="icon" href="pub_img/ETTAAJ-RENT-CARS.jpg">

  <!-- Tailwind + Fonts -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: { colors: { gold: '#FFD700' } } }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <style>
    :root {
      --bg: #36454F; --bg-dark: #2C3A44; --card: #36454F; --card-dark: #2C3A44;
      --border: #4A5A66; --primary: #FFFFFF; --muted: #D1D5DB; --gold: #FFD700;
      --text-primary: var(--primary); --text-muted: var(--muted);
    }
    .light {
      --bg: #f8fafc; --bg-dark: #e2e8f0; --card: #ffffff; --card-dark: #f1f5f9;
      --border: #cbd5e1; --primary: #1e293b; --muted: #64748b; --gold: #d97706;
      --text-primary: var(--primary); --text-muted: var(--muted);
    }
    body { background-color: var(--bg); color: var(--primary); font-family: 'Inter', sans-serif; }
    .bg-card { background-color: var(--card); }
    .bg-card-dark { background-color: var(--card-dark); }
    .border-border { border-color: var(--border); }
    .text-primary { color: var(--primary); }
    .text-muted { color: var(--muted); }
    .text-gold { color: var(--gold); }
  </style>
</head>
<body class="min-h-screen">

<?php include 'header.php'; ?>

<!-- HERO SECTION -->
<section class="relative overflow-hidden bg-[var(--bg)] isolate py-16 sm:py-24 lg:py-32">
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-gold/10 via-[var(--bg)] to-yellow-600/5"></div>
    <div class="absolute inset-0 bg-gradient-to-tl from-[var(--bg-dark)]/80 via-transparent to-gold/5"></div>
  </div>

  <div class="relative max-w-6xl mx-auto px-6 lg:px-16 text-center">
    <div data-aos="fade-up">
      <p class="text-sm uppercase tracking-[0.35em] text-gold/80 mb-4">ETTAAJ RENT CARS</p>
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-primary mb-4">Rental Guide – ETTAAJ RENT CARS</h1>
      <p class="text-lg sm:text-xl text-muted max-w-3xl mx-auto">Everything you need to know for a smooth and premium rental experience in Marrakech.</p>
    </div>
  </div>
</section>

    <main class="relative z-10 px-4 sm:px-6 lg:px-8 py-16 space-y-20 max-w-6xl mx-auto">
      <section id="rental-guidelines">
        <div class="inline-flex items-center gap-3 mb-6">
          <span class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-gold/15 text-gold shadow-gold">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m3 5H6a2 2 0 01-2-2V5a2 2 0 012-2h8l6 6v10a2 2 0 01-2 2z" />
            </svg>
          </span>
          <div>
            <p class="text-xs uppercase tracking-[0.3em] text-muted"><?= $text['section_01'] ?></p>
            <h2 class="text-2xl sm:text-3xl font-bold text-gold"><?= $text['rental_guidelines'] ?></h2>
          </div>
        </div>

        <!-- cards -->
        <div class="grid gap-6 md:grid-cols-2">
          <article class="p-6 bg-card rounded-3xl border border-border shadow-lg hover:shadow-gold/40 transition">
            <div class="flex items-center gap-3 mb-5">
              <span class="flex items-center justify-center w-12 h-12 rounded-full bg-gold/10 text-gold border border-gold/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 7V3m8 4V3m-9 8h10m-6 4h6m5-6H5a2 2 0 00-2 2v7a2 2 0 002 2h5" />
                </svg>
              </span>
              <div>
                <h3 class="text-xl font-semibold text-primary"><?= $text['pickup_guidelines'] ?></h3>
                <p class="text-muted text-sm"><?= $text['grace_period_docs'] ?></p>
              </div>
            </div>

            <ul class="space-y-3 text-primary">
              <li class="flex items-start gap-3">
                <span class="text-gold mt-1">•</span>
                <div>
                  <p class="font-medium"><?= $text['required_documents'] ?></p>
                  <p class="text-sm text-muted"><?= $text['required_docs_desc'] ?></p>
                </div>
              </li>

              <li class="flex items-start gap-3">
                <span class="text-gold mt-1">•</span>
                <div>
                  <p class="font-medium"><?= $text['vehicle_inspection'] ?></p>
                  <p class="text-sm text-muted"><?= $text['vehicle_inspection_desc'] ?></p>
                </div>
              </li>

              <li class="flex items-start gap-3">
                <span class="text-gold mt-1">•</span>
                <div>
                  <p class="font-medium"><?= $text['grace_duration'] ?></p>
                  <p class="text-sm text-muted"><?= $text['grace_duration_desc'] ?></p>
                </div>
              </li>
            </ul>
          </article>

          <article class="p-6 bg-card rounded-3xl border border-border shadow-lg hover:shadow-gold/40 transition">
            <div class="flex items-center gap-3 mb-5">
              <span class="flex items-center justify-center w-12 h-12 rounded-full bg-gold/10 text-gold border border-gold/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15 12H9m12 0c0 6-6 9-9 9s-9-3-9-9a9 9 0 1118 0z" />
                </svg>
              </span>
              <div>
                <h3 class="text-xl font-semibold text-primary"><?= $text['return_guidelines'] ?></h3>
                <p class="text-muted text-sm"><?= $text['fuel_inspection_window'] ?></p>
              </div>
            </div>

            <ul class="space-y-3 text-primary">
              <li class="flex items-start gap-3"><span class="text-gold mt-1">•</span><div><p class="font-medium"><?= $text['fuel_requirement'] ?></p><p class="text-sm text-muted"><?= $text['fuel_requirement_desc'] ?></p></div></li>
              <li class="flex items-start gap-3"><span class="text-gold mt-1">•</span><div><p class="font-medium"><?= $text['final_inspection'] ?></p><p class="text-sm text-muted"><?= $text['final_inspection_desc'] ?></p></div></li>
              <li class="flex items-start gap-3"><span class="text-gold mt-1">•</span><div><p class="font-medium"><?= $text['return_window'] ?></p><p class="text-sm text-muted"><?= $text['return_window_desc'] ?></p></div></li>
              <li class="flex items-start gap-3"><span class="text-gold mt-1">•</span><div><p class="font-medium"><?= $text['documentation'] ?></p><p class="text-sm text-muted"><?= $text['documentation_desc'] ?></p></div></li>
            </ul>
          </article>

          <article class="p-6 bg-card rounded-3xl border border-border shadow-lg hover:shadow-gold/40 transition md:col-span-2">
            <div class="flex items-center gap-3 mb-5">
              <span class="flex items-center justify-center w-12 h-12 rounded-full bg-gold/10 text-gold border border-gold/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M17.657 16.657L13.414 12l4.243-4.243m-5.657 8.485H5a2 2 0 01-2-2V8a2 2 0 012-2h7.586a2 2 0 011.414.586l4.414 4.414a2 2 0 010 2.828l-4.414 4.414a2 2 0 01-1.414.586z" />
                </svg>
              </span>
              <div>
                <h3 class="text-xl font-semibold text-primary"><?= $text['extension_policies'] ?></h3>
                <p class="text-muted text-sm"><?= $text['extend_journey'] ?></p>
              </div>
            </div>

            <div class="grid md:grid-cols-3 gap-4 text-primary">
              <div class="space-y-1">
                <p class="font-medium text-gold"><?= $text['requesting_extension'] ?></p>
                <p class="text-sm text-muted"><?= $text['requesting_extension_desc'] ?></p>
              </div>
              <div class="space-y-1">
                <p class="font-medium text-gold"><?= $text['fees'] ?></p>
                <p class="text-sm text-muted"><?= $text['fees_desc'] ?></p>
              </div>
              <div class="space-y-1">
                <p class="font-medium text-gold"><?= $text['conditions'] ?></p>
                <p class="text-sm text-muted"><?= $text['conditions_desc'] ?></p>
              </div>
            </div>
          </article>
        </div>
      </section>

      <!-- pricing & payment -->
      <section id="pricing-payment">
        <div class="inline-flex items-center gap-3 mb-6">
          <span class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-gold/15 text-gold shadow-gold">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-3.866 0-7 2.239-7 5s3.134 5 7 5 7-2.239 7-5-3.134-5-7-5zm0 0V5m0 15v-3" />
            </svg>
          </span>
          <div>
            <p class="text-xs uppercase tracking-[0.3em] text-muted"><?= $text['section_02'] ?></p>
            <h2 class="text-2xl sm:text-3xl font-bold text-gold"><?= $text['pricing_payment'] ?></h2>
          </div>
        </div>

        <div class="bg-card rounded-3xl p-6 lg:p-10 border border-border shadow-lg">
          <div class="grid lg:grid-cols-3 gap-8">
            <div class="space-y-3">
              <h3 class="text-lg font-semibold text-primary"><?= $text['daily_rates'] ?></h3>
              <p class="text-muted text-sm"><?= $text['daily_rates_desc'] ?></p>
              <div class="p-4 rounded-2xl bg-card-dark border border-border text-gold text-sm uppercase tracking-[0.2em] text-center"><?= $text['rate_charts_available'] ?></div>
            </div>

            <div class="space-y-3">
              <h3 class="text-lg font-semibold text-primary"><?= $text['additional_fees'] ?></h3>
              <ul class="text-primary text-sm space-y-2">
                <li>• <?= $text['late_return'] ?></li>
                <li>• <?= $text['refuelling_service'] ?></li>
                <li>• <?= $text['cleaning_beyond'] ?></li>
              </ul>
              <p class="text-xs text-muted"><?= $text['fees_disclosed'] ?></p>
            </div>

            <div class="space-y-3">
              <h3 class="text-lg font-semibold text-primary"><?= $text['long_term_discounts'] ?></h3>
              <p class="text-muted text-sm"><?= $text['long_term_desc'] ?></p>
            </div>
          </div>

          <div class="mt-10 border-t border-border pt-8">
            <h3 class="text-lg font-semibold text-primary mb-4"><?= $text['payment_options'] ?></h3>
            <div class="grid sm:grid-cols-2 gap-5 text-sm text-primary">
              <div class="flex items-center gap-3 py-3 px-4 rounded-2xl border border-border bg-card-dark">
                <span class="text-gold text-xl">•</span>
                <?= $text['visa_mastercard'] ?>
              </div>
              <div class="flex items-center gap-3 py-3 px-4 rounded-2xl border border-border bg-card-dark">
                <span class="text-gold text-xl">•</span>
                <?= $text['cash'] ?>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- emergency procedures -->
      <section id="emergency-procedures">
        <div class="inline-flex items-center gap-3 mb-6">
          <span class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-gold/15 text-gold shadow-gold">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 00-8 16.32L12 22l8-3.68A10 10 0 0012 2z" />
            </svg>
          </span>
          <div>
            <p class="text-xs uppercase tracking-[0.3em] text-muted"><?= $text['section_03'] ?></p>
            <h2 class="text-2xl sm:text-3xl font-bold text-gold"><?= $text['emergency_procedures'] ?></h2>
          </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
          <!-- Accident Procedures -->
          <article class="p-6 md:p-8 rounded-3xl bg-card-dark border border-gold/30 shadow-lg">
            <div class="flex items-center gap-3 mb-5">
              <span class="flex items-center justify-center w-12 h-12 rounded-full bg-gold/15 text-gold border border-gold/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9.75 17L8 10l-2 7h11l-2-7-1.75 7M12 6V4m0 2a2 2 0 100-4 2 2 0 000 4zm5 4V8a5 5 0 00-10 0v2H5l1 8h12l1-8h-2z" />
                </svg>
              </span>
              <div>
                <p class="text-sm uppercase tracking-[0.2em] text-muted"><?= $text['procedure_one'] ?></p>
                <h3 class="text-2xl font-bold text-gold"><?= $text['accident_procedures'] ?></h3>
              </div>
            </div>

            <ol class="space-y-3 text-primary text-sm list-decimal list-inside">
              <li><?= $text['accident_step1'] ?></li>
              <li><?= $text['accident_step2'] ?> <span class="text-gold font-semibold">15</span> <?= $text['accident_step3'] ?> <span class="text-gold font-semibold">177</span> <?= $text['accident_step4'] ?></li>
              <li><?= $text['accident_step5'] ?></li>
              <li><?= $text['accident_step6'] ?></li>
              <li><?= $text['accident_step7'] ?> <span class="text-gold font-semibold">+212 653 330 752</span>.</li>
            </ol>

            <div class="mt-6 p-4 rounded-2xl bg-card border border-border text-center text-sm text-muted">
              <?= $text['required_info'] ?>
            </div>
          </article>

          <!-- Breakdown assistance -->
          <article class="p-6 md:p-8 rounded-3xl bg-card-dark border border-gold/30 shadow-lg">
            <div class="flex items-center gap-3 mb-5">
              <span class="flex items-center justify-center w-12 h-12 rounded-full bg-gold/15 text-gold border border-gold/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M13 6L4 10v7h5v-4h6v4h5v-7l-9-4z" />
                </svg>
              </span>
              <div>
                <p class="text-sm uppercase tracking-[0.2em] text-muted"><?= $text['procedure_two'] ?></p>
                <h3 class="text-2xl font-bold text-gold"><?= $text['breakdown_assistance'] ?></h3>
              </div>
            </div>

            <ol class="space-y-3 text-primary text-sm list-decimal list-inside">
              <li><?= $text['breakdown_step1'] ?></li>
              <li><?= $text['breakdown_step2'] ?></li>
              <li><?= $text['breakdown_step3'] ?></li>
              <li><?= $text['breakdown_step4'] ?></li>
            </ol>

            <div class="mt-6 grid sm:grid-cols-2 gap-4 text-sm">
              <div class="p-4 rounded-2xl border border-gold/30 bg-gold/10 text-gold text-center font-semibold">
                <?= $text['towing_included'] ?>
              </div>
              <div class="p-4 rounded-2xl border border-border bg-card text-primary text-center font-semibold">
                <?= $text['replacement_vehicle'] ?>
              </div>
            </div>
          </article>
        </div>
      </section>

      <!-- insurance coverage -->
      <section id="insurance-coverage">
        <div class="inline-flex items-center gap-3 mb-6">
          <span class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-gold/15 text-gold shadow-gold">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-3.866 0-7 2.239-7 5s3.134 5 7 5 7-2.239 7-5-3.134-5-7-5zm0-6v6m0 10v4" />
            </svg>
          </span>
          <div>
            <p class="text-xs uppercase tracking-[0.3em] text-muted"><?= $text['section_04'] ?></p>
            <h2 class="text-2xl sm:text-3xl font-bold text-gold"><?= $text['insurance_coverage'] ?></h2>
          </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
          <article class="p-6 md:p-8 rounded-3xl bg-card border border-border shadow-lg">
            <div class="flex items-center gap-3 mb-4">
              <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gold/15 text-gold border border-gold/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 8c-3.866 0-7 2.239-7 5s3.134 5 7 5 7-2.239 7-5-3.134-5-7-5zm0-6v6m0 10v4" />
                </svg>
              </span>
              <div>
                <h3 class="text-2xl font-bold text-primary"><?= $text['basic_insurance'] ?></h3>
                <p class="text-sm text-green-400 font-semibold"><?= $text['free'] ?> - <?= $text['standard_coverage'] ?></p>
              </div>
            </div>

            <p class="text-sm text-muted mb-3"><?= $text['deposit'] ?>: <strong class="text-primary">$588.00</strong></p>
            <ul class="space-y-2 text-primary text-sm">
              <li>• <?= $text['third_party_liability'] ?></li>
              <li>• <?= $text['basic_collision'] ?></li>
              <li>• <?= $text['standard_theft'] ?></li>
              <li>• <?= $text['support_24_7'] ?> ET TAAJ assistance</li>
            </ul>
          </article>

          <article class="p-6 md:p-8 rounded-3xl bg-card border border-border shadow-lg">
            <div class="flex items-center gap-3 mb-4">
              <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gold/15 text-gold border border-gold/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </span>
              <div>
                <h3 class="text-2xl font-bold text-primary"><?= $text['smart_insurance'] ?></h3>
                <p class="text-sm text-amber-400 font-semibold">+$8.90/<?= $text['day'] ?></p>
              </div>
            </div>

            <p class="text-sm text-muted mb-3"><?= $text['deposit'] ?>: <strong class="text-primary">$294.00</strong></p>
            <ul class="space-y-2 text-primary text-sm">
              <li>• <?= $text['all_basic_coverage'] ?></li>
              <li>• <?= $text['reduced_excess'] ?></li>
              <li>• <?= $text['window_tire'] ?></li>
              <li>• <?= $text['personal_accident'] ?></li>
            </ul>
          </article>

          <article class="p-6 md:p-8 rounded-3xl bg-gradient-to-br from-gold/20 via-gold/10 to-transparent border border-gold/40 shadow-lg">
            <div class="flex items-center gap-3 mb-4">
              <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gold/20 text-[#67511c] border border-gold/40">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 12v-6m0 0L9 9m3-3l3 3m-3 6v6m0 0l3-3m-3 3l-3-3" />
                </svg>
              </span>
              <div>
                <h3 class="text-2xl font-bold text-primary"><?= $text['premium_insurance'] ?></h3>
                <p class="text-sm text-amber-400 font-semibold">+$14.40/<?= $text['day'] ?></p>
              </div>
            </div>

            <p class="text-sm text-muted mb-3"><?= $text['deposit'] ?>: <strong class="text-primary">$98.00</strong></p>
            <ul class="space-y-2 text-primary text-sm">
              <li>• <?= $text['all_basic_coverage'] ?></li>
              <li>• <?= $text['zero_excess'] ?></li>
              <li>• <?= $text['premium_roadside'] ?></li>
              <li>• <?= $text['personal_effects'] ?></li>
              <li>• <?= $text['extended_liability'] ?></li>
            </ul>
          </article>
        </div>
        
        <div class="mt-6 p-4 rounded-2xl bg-card-dark border border-border text-center">
          <p class="text-sm text-muted">
            <?= $text['insurance_note'] ?>
          </p>
        </div>
      </section>

      <!-- Travel Essentials -->
      <section id="travel-essentials">
        <div class="inline-flex items-center gap-3 mb-6">
          <span class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-gold/15 text-gold shadow-gold">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            </svg>
          </span>
          <div>
            <p class="text-xs uppercase tracking-[0.3em] text-muted"><?= $text['section_05'] ?></p>
            <h2 class="text-2xl sm:text-3xl font-bold text-gold"><?= $text['travel_essentials'] ?></h2>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
          <article class="p-6 rounded-3xl bg-card border border-border shadow-lg hover:shadow-gold/30 transition">
            <div class="flex items-center gap-4 mb-4">
              <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h1m4-4v12m4-8h6m-3-3v6"/>
              </svg>
              <div class="flex-1">
                <h3 class="text-xl font-bold text-primary"><?= $text['premium_fuel_service'] ?></h3>
                <p class="text-sm text-muted"><?= $text['prepaid_full_tank'] ?></p>
              </div>
              <span class="text-lg font-bold text-gold">$110.00/<?= $text['day'] ?></span>
            </div>
            <p class="text-sm text-primary"><?= $text['premium_fuel_desc'] ?></p>
          </article>

          <article class="p-6 rounded-3xl bg-card border border-border shadow-lg hover:shadow-gold/30 transition">
            <div class="flex items-center gap-4 mb-4">
              <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
              <div class="flex-1">
                <h3 class="text-xl font-bold text-primary"><?= $text['unlimited_kilometers'] ?></h3>
                <p class="text-sm text-muted"><?= $text['drive_without_restrictions'] ?></p>
              </div>
              <span class="text-lg font-bold text-gold">$10.50/<?= $text['day'] ?></span>
            </div>
            <p class="text-sm text-primary"><?= $text['unlimited_km_desc'] ?></p>
          </article>

          <article class="p-6 rounded-3xl bg-card border border-border shadow-lg hover:shadow-gold/30 transition">
            <div class="flex items-center gap-4 mb-4">
              <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <div class="flex-1">
                <h3 class="text-xl font-bold text-primary"><?= $text['flexible_cancellation'] ?></h3>
                <p class="text-sm text-muted"><?= $text['free_cancellation_until'] ?></p>
              </div>
              <span class="text-lg font-bold text-gold">$9.50/<?= $text['day'] ?></span>
            </div>
            <p class="text-sm text-primary"><?= $text['flexible_cancel_desc'] ?></p>
          </article>

          <article class="p-6 rounded-3xl bg-card border border-border shadow-lg hover:shadow-gold/30 transition">
            <div class="flex items-center gap-4 mb-4">
              <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H9v-1c-4 0-6-4-6-4v-3h12v3c0 0-2 4-6 4z"/>
              </svg>
              <div class="flex-1">
                <h3 class="text-xl font-bold text-primary"><?= $text['additional_drivers'] ?></h3>
                <p class="text-sm text-muted"><?= $text['add_up_to_2'] ?></p>
              </div>
              <span class="text-lg font-bold text-gold">$2.50/<?= $text['day'] ?></span>
            </div>
            <p class="text-sm text-primary"><?= $text['additional_drivers_desc'] ?></p>
          </article>
        </div>
      </section>

      <section id="insurance-claims" class="pb-10">
        <div class="inline-flex items-center gap-3 mb-6">
          <span class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-gold/15 text-gold shadow-gold">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7" />
            </svg>
          </span>
          <div>
            <p class="text-xs uppercase tracking-[0.3em] text-muted">Section 06</p>
            <h2 class="text-2xl sm:text-3xl font-bold text-gold">Insurance Claims Process</h2>
          </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
          <article class="p-6 rounded-3xl bg-card border border-border shadow-lg hover:shadow-gold/30 transition">
            <h3 class="text-xl font-semibold mb-3 text-gold"><?= $text['how_to_file_claim'] ?></h3>
            <p class="text-sm text-primary"><?= $text['file_claim_desc'] ?></p>
          </article>

          <article class="p-6 rounded-3xl bg-card border border-border shadow-lg hover:shadow-gold/30 transition">
            <h3 class="text-xl font-semibold mb-3 text-gold"><?= $text['required_documents_claims'] ?></h3>
            <ul class="text-sm text-primary space-y-2">
              <li>• <?= $text['rental_agreement'] ?></li>
              <li>• <?= $text['id_license'] ?></li>
              <li>• <?= $text['official_report'] ?></li>
              <li>• <?= $text['photos_damages'] ?></li>
            </ul>
          </article>

          <article class="p-6 rounded-3xl bg-card border border-border shadow-lg hover:shadow-gold/30 transition">
            <h3 class="text-xl font-semibold mb-3 text-gold"><?= $text['coverage_exclusions'] ?></h3>
            <p class="text-sm text-primary"><?= $text['exclusions_desc'] ?></p>
          </article>
        </div>
      </section>
    </main>

<?php include 'footer.php'; ?>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once: true, duration: 800 });
</script>
</body>
</html>