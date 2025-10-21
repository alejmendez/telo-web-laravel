<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Telo - Conecta tu hogar con profesionales verificados</title>
  <meta name="description" content="Telo conecta tus necesidades del hogar con profesionales verificados en Santiago. Reparaciones, remodelación y mantención — rápido, simple y 100% digital." />
  <meta name="author" content="Telo" />

  <meta property="og:title" content="Telo - Tu hogar perfecto está a un click" />
  <meta property="og:description" content="Conectamos tus necesidades del hogar con profesionales verificados en Vitacura, Las Condes, La Reina y Lo Barnechea." />
  <meta property="og:type" content="website" />
  <meta property="og:image" content="{{ asset('assets/hero-image.jpg') }}" />

  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:site" content="{{ '@telo' }}" />
  <meta name="twitter:image" content="{{ asset('assets/hero-image.jpg') }}" />

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
    window.open("https://wa.me/56912345678?text=Hola%20Telo,%20necesito%20ayuda%20con%20mi%20hogar", "_blank");
};
</script>
</body>
</html>
