<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Telo - Servicios de Hogar y Profesionales Verificados en Chile</title>
  <meta name="description" content="¿Buscas gasfiter, electricista o maestros de confianza? Telo conecta tu hogar con profesionales verificados en Las Condes, Vitacura, Lo Barnechea y más. ¡Cotiza gratis!" />
  <meta name="keywords" content="servicios hogar, profesionales verificados, gasfiteria, electricidad, pintura, maestros chile, remodelacion, limpieza, telo app" />
  <meta name="author" content="Telo Chile" />
  <meta name="robots" content="index, follow" />
  <link rel="canonical" href="{{ url()->current() }}" />

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ url()->current() }}" />
  <meta property="og:title" content="Telo - Profesionales de Confianza para tu Hogar" />
  <meta property="og:description" content="Encuentra gasfitería, electricidad, pintura y más. Profesionales 100% verificados y garantizados para servicios en tu hogar. ¡Soluciona hoy mismo!" />
  <meta property="og:image" content="{{ asset('assets/img/hero-image.jpg') }}" />
  <meta property="og:image:alt" content="Profesional de Telo trabajando en un hogar moderno" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:site" content="@telochile" />
  <meta name="twitter:title" content="Telo - Servicios de Hogar Rápidos y Seguros" />
  <meta name="twitter:description" content="¿Reparaciones en casa? Conecta al instante con expertos certificados en Santiago. Calidad y seguridad garantizada." />
  <meta name="twitter:image" content="{{ asset('assets/img/hero-image.jpg') }}" />

  @vite([
    'resources/css/website.css',
  ])
</head>
<body>
  @include('website::partials.hero_section')
  @include('website::partials.how_it_works')
  @include('website::partials.popular_categories')
  @include('website::partials.why_choose_telo')
  @include('website::partials.final_cta')
  @include('website::partials.footer')
<script>
const handleWhatsAppClick = () => {
  window.open("https://wa.me/56954105279?text=Hola%20Telo!%20Me%20gustar%C3%ADa%20cotizar%20un%20servicio%20para%20mi%20hogar%20con%20un%20profesional%20verificado.", "_blank");
};
</script>
</body>
</html>
