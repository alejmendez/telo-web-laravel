<section class="relative overflow-hidden">
  <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">
    <div class="grid lg:grid-cols-2 gap-12 items-center">
      <div class="space-y-6 lg:space-y-8">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight">
          Tu hogar perfecto está a un
          <span class="ms-3 bg-gradient-to-r from-primary to-[hsl(210_95%_50%)] bg-clip-text text-transparent">
            click
          </span>
        </h1>
        <p class="text-lg sm:text-xl text-muted-foreground max-w-xl">
          Conectamos tus necesidades del hogar con profesionales verificados — rápido, simple y 100% digital.
        </p>
        <button
          class="inline-flex items-center justify-center gap-2 bg-primary text-primary-foreground hover:bg-primary/90 shadow-(--shadow-soft) hover:shadow-[0_8px_32px_-6px_hsl(213_100%_41%_/_0.3)] transition-(--transition-smooth) text-base sm:text-lg px-8 sm:px-12 h-14 rounded-full font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
          onclick="handleWhatsAppClick()"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-circle-icon lucide-message-circle w-5 h-5"><path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"/></svg>
          Contáctanos por WhatsApp
        </button>
      </div>
      <div class="relative">
        <div class="absolute -inset-4 bg-(--gradient-hero) opacity-20 rounded-3xl blur-3xl"></div>
        <img
          src="{{ asset('assets/img/hero-image.jpg') }}"
          alt="Pareja joven revisando teléfono con profesional trabajando en casa moderna chilena"
          class="relative rounded-3xl shadow-(--shadow-soft) w-full h-auto"
        />
      </div>
    </div>
  </div>
</section>
