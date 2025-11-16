<?php include 'header.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-16 text-center bg-[#36454F]">
  <!-- Animated Hero Section -->
  <section class="relative min-h-[80vh] flex items-center justify-center overflow-hidden rounded-3xl mb-16 bg-gradient-to-br from-[#1e293b] via-[#36454F] to-[#2C3A44] shadow-2xl"
           style="background-image: url('https://images.unsplash.com/photo-1519641471654-1ce4c62f78d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
                  background-size: cover; background-position: center;"
           data-aos="fade" data-aos-duration="1500">

    <!-- Dark Overlay -->
    <div class="absolute inset-0 bg-black/70"></div>

    <!-- Floating Gold Particles -->
    <div class="absolute inset-0 pointer-events-none">
      <div class="particle"></div>
      <div class="particle delay-1"></div>
      <div class="particle delay-2"></div>
      <div class="particle delay-3"></div>
      <div class="particle delay-4"></div>
    </div>

    <div class="relative z-10 max-w-5xl mx-auto px-4">
      <!-- Logo + Name -->
      <div data-aos="fade-down" data-aos-delay="400" class="mb-8">
        <div class="flex justify-center items-center gap-3">
          <img src="pub_img/GoldCar.png" alt="Gold Cars Logo" class="w-16 h-16 rounded-full ring-4 ring-gold/60 shadow-2xl">
          <h1 class="text-5xl md:text-7xl font-extrabold bg-gradient-to-r from-gold via-yellow-400 to-gold bg-clip-text text-transparent drop-shadow-2xl">
            Gold Cars
          </h1>
        </div>
      </div>

      <!-- Title -->
      <h1 data-aos="zoom-in" data-aos-delay="700" data-aos-duration="1000"
          class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
        Driven by <span class="text-gold animate-pulse">Excellence</span>
      </h1>

      <!-- Subtitle -->
      <p data-aos="fade-up" data-aos-delay="1000" 
         class="text-lg md:text-xl text-gray-300 mb-10 max-w-3xl mx-auto leading-relaxed">
        At Gold Cars, we believe every journey should be exceptional. Founded with a passion for excellence, 
        we deliver premium car rental experiences that combine luxury, reliability, and unmatched customer care.
      </p>

      <!-- CTA -->
      <div data-aos="fade-up" data-aos-delay="1300">
        <a href="#mission" class="inline-flex items-center gap-2 bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-400 
               text-white font-bold text-lg py-4 px-10 rounded-full shadow-2xl transform transition-all duration-300 
               hover:scale-110 hover:shadow-gold/50 active:scale-95">
          Discover Our Story
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
          </svg>
        </a>
      </div>
    </div>
  </section>

  <!-- Mission & Vision -->
  <section id="mission" class="grid md:grid-cols-2 gap-8 mb-16">
    <!-- Our Mission -->
    <div data-aos="fade-right" data-aos-delay="100" data-aos-duration="800"
         class="bg-[#36454F]/90 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-[#4A5A66] hover:shadow-gold/20 transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gradient-to-br from-gold to-yellow-500 rounded-full mx-auto mb-6 flex items-center justify-center shadow-lg animate-pulse">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
      </div>
      <h3 class="font-bold text-2xl mb-4 text-white">Our Mission</h3>
      <p class="text-gray-300 leading-relaxed">
        To provide seamless, safe, and stylish transportation solutions that empower your travels — 
        whether for business, leisure, or adventure.
      </p>
    </div>

    <!-- Our Vision -->
    <div data-aos="fade-left" data-aos-delay="300" data-aos-duration="800"
         class="bg-[#36454F]/90 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-[#4A5A66] hover:shadow-gold/20 transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gradient-to-br from-gold to-yellow-500 rounded-full mx-auto mb-6 flex items-center justify-center shadow-lg animate-pulse">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
      </div>
      <h3 class="font-bold text-2xl mb-4 text-white">Our Vision</h3>
      <p class="text-gray-300 leading-relaxed">
        To redefine car rental by setting the gold standard in service, innovation, and customer satisfaction worldwide.
      </p>
    </div>
  </section>

  <!-- Values Grid (3 Columns) -->
  <section class="grid md:grid-cols-3 gap-8 mb-16">
    <!-- Excellence -->
    <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="700"
         class="bg-[#36454F]/90 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-[#4A5A66] hover:shadow-gold/20 transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gradient-to-br from-gold to-yellow-500 rounded-full mx-auto mb-4 flex items-center justify-center shadow-lg animate-pulse">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
        </svg>
      </div>
      <h3 class="font-bold text-xl mb-2 text-white">Excellence</h3>
      <p class="text-gray-300">We strive for perfection in every detail.</p>
    </div>

    <!-- Integrity -->
    <div data-aos="zoom-in" data-aos-delay="300" data-aos-duration="700"
         class="bg-[#36454F]/90 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-[#4A5A66] hover:shadow-gold/20 transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gradient-to-br from-gold to-yellow-500 rounded-full mx-auto mb-4 flex items-center justify-center shadow-lg animate-pulse">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <h3 class="font-bold text-xl mb-2 text-white">Integrity</h3>
      <p class="text-gray-300">Honesty and transparency in all we do.</p>
    </div>

    <!-- Customer First -->
    <div data-aos="zoom-in" data-aos-delay="500" data-aos-duration="700"
         class="bg-[#36454F]/90 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-[#4A5A66] hover:shadow-gold/20 transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gradient-to-br from-gold to-yellow-500 rounded-full mx-auto mb-4 flex items-center justify-center shadow-lg animate-pulse">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017a2 2 0 01-1.789-1.106l-3.5-7a2 2 0 011.789-2.894H10"/>
        </svg>
      </div>
      <h3 class="font-bold text-xl mb-2 text-white">Customer First</h3>
      <p class="text-gray-300">Your satisfaction is our top priority.</p>
    </div>
  </section>

  <!-- Call to Action -->
  <div data-aos="fade-up" data-aos-delay="700" data-aos-duration="800" class="mt-16">
    <a href="index.php" class="inline-flex items-center gap-3 bg-gradient-to-r from-gold to-yellow-500 hover:from-yellow-500 hover:to-orange-400 
           text-white font-bold text-lg py-4 px-10 rounded-full shadow-2xl transform transition-all duration-300 
           hover:scale-110 hover:shadow-gold/50 active:scale-95">
      Explore Our Fleet
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
      </svg>
    </a>
  </div>
</main>

<?php include 'footer.php'; ?>

<!-- AOS + Custom Animations -->
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<style>
  :root {
    --dark-bg: #36454F;
    --darker-bg: #2C3A44;
    --border: #4A5A66;
  }
  body, main { background-color: var(--dark-bg) !important; color: white !important; }
  .bg-white { background-color: var(--dark-bg) !important; }
  .text-gray-900, .text-gray-800, .text-gray-700 { color: white !important; }
  .text-gray-600, .text-gray-500 { color: #D1D5DB !important; }

  /* Gold Gradient Text */
  .bg-gradient-to-r.from-gold.bg-clip-text {
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  /* Floating Particles */
  .particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: #FFD700;
    border-radius: 50%;
    opacity: 0.6;
    animation: float 6s infinite ease-in-out;
  }
  .particle:nth-child(1) { top: 20%; left: 15%; animation-delay: 0s; }
  .particle:nth-child(2) { top: 60%; left: 80%; animation-delay: 1s; width: 6px; height: 6px; }
  .particle:nth-child(3) { top: 40%; left: 50%; animation-delay: 2s; }
  .particle:nth-child(4) { top: 80%; left: 30%; animation-delay: 3s; width: 5px; height: 5px; }
  .particle:nth-child(5) { top: 30%; left: 70%; animation-delay: 4s; }

  @keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.6; }
    50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
  }

  .animate-pulse { animation: pulse 2s infinite; }
  @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }

  .hover\:shadow-gold\/20:hover { box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2); }
  .hover\:shadow-gold\/50:hover { box-shadow: 0 0 30px rgba(255, 215, 0, 0.5); }
</style>

<script>
  AOS.init({
    once: true,
    duration: 1000,
    easing: 'ease-out-quart'
  });
</script>