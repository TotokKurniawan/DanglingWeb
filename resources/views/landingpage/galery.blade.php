@extends('landingpage.layout.app')
@section('content')

<section id="hero" class="hero section">

    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-6 order-lg-last hero-img" data-aos="zoom-out" data-aos-delay="100">
          <img src="{{ asset('landingpage/assets/img/hero-img.png')}}" class="img-fluid animated" alt="">
        </div>
        <div class="col-lg-6  d-flex flex-column justify-content-center text-center text-md-start" data-aos="fade-in">
          <h2>Gallery</h2>
          <p>fitur-fitur yang ada pada aplikasi ini</p>
          {{-- <div class="d-flex mt-4 justify-content-center justify-content-md-start">
            <a href="#" class="download-btn"><i class="bi bi-google-play"></i> <span>Google Play</span></a>
            <a href="#" class="download-btn"><i class="bi bi-apple"></i> <span>App Store</span></a>
          </div> --}}
        </div>
      </div>
    </div>

  </section><!-- /Hero Section -->

  <!-- Gallery Section -->
  <section id="gallery" class="gallery section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
      <h2>Gallery</h2>
      <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
    </div><!-- End Section Title -->

    <div class="container-fluid" data-aos="fade-up" data-aos-delay="100">

      <div class="swiper init-swiper">
        <script type="application/json" class="swiper-config">
          {
            "loop": true,
            "speed": 600,
            "autoplay": {
              "delay": 5000
            },
            "slidesPerView": "auto",
            "centeredSlides": true,
            "pagination": {
              "el": ".swiper-pagination",
              "type": "bullets",
              "clickable": true
            },
            "breakpoints": {
              "320": {
                "slidesPerView": 1,
                "spaceBetween": 0
              },
              "768": {
                "slidesPerView": 3,
                "spaceBetween": 30
              },
              "992": {
                "slidesPerView": 5,
                "spaceBetween": 30
              },
              "1200": {
                "slidesPerView": 7,
                "spaceBetween": 30
              }
            }
          }
        </script>
        <div class="swiper-wrapper align-items-center">
          <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full" href="{{ asset('landingpage/assets/img/app-gallery/app-gallery-1.png')}}')}}"><img src="{{ asset('landingpage/assets/img/app-gallery/app-gallery-1.png')}}" class="img-fluid" alt=""></a></div>
          <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full" href="{{ asset('landingpage/assets/img/app-gallery/app-gallery-3.png')}}')}}"><img src="{{ asset('landingpage/assets/img/app-gallery/app-gallery-3.png')}}" class="img-fluid" alt=""></a></div>
          <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full" href="{{ asset('landingpage/assets/img/app-gallery/app-gallery-2.png')}}')}}"><img src="{{ asset('landingpage/assets/img/app-gallery/app-gallery-2.png')}}" class="img-fluid" alt=""></a></div>
          <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full" href="{{ asset('landingpage/assets/img/app-gallery/app-gallery-4.png')}}')}}"><img src="{{ asset('landingpage/assets/img/app-gallery/app-gallery-4.png')}}" class="img-fluid" alt=""></a></div>
          <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full" href="{{ asset('landingpage/assets/img/app-gallery/app-gallery-5.png')}}')}}"><img src="{{ asset('landingpage/assets/img/app-gallery/app-gallery-5.png')}}" class="img-fluid" alt=""></a></div>
          <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full" href="{{ asset('landingpage/assets/img/app-gallery/app-gallery-6.png')}}')}}"><img src="{{ asset('landingpage/assets/img/app-gallery/app-gallery-6.png')}}" class="img-fluid" alt=""></a></div>
          <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full" href="{{ asset('landingpage/assets/img/app-gallery/app-gallery-7.png')}}')}}"><img src="{{ asset('landingpage/assets/img/app-gallery/app-gallery-7.png')}}" class="img-fluid" alt=""></a></div>
          <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full" href="{{ asset('landingpage/assets/img/app-gallery/app-gallery-8.png')}}')}}"><img src="{{ asset('landingpage/assets/img/app-gallery/app-gallery-8.png')}}" class="img-fluid" alt=""></a></div>
          <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full" href="{{ asset('landingpage/assets/img/app-gallery/app-gallery-9.png')}}')}}"><img src="{{ asset('landingpage/assets/img/app-gallery/app-gallery-9.png')}}" class="img-fluid" alt=""></a></div>
          <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full" href="{{ asset('landingpage/assets/img/app-gallery/app-gallery-10.png')}}')}}"><img src="{{ asset('landingpage/assets/img/app-gallery/app-gallery-10.png')}}" class="img-fluid" alt=""></a></div>
          <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full" href="{{ asset('landingpage/assets/img/app-gallery/app-gallery-11.png')}}')}}"><img src="{{ asset('landingpage/assets/img/app-gallery/app-gallery-11.png')}}" class="img-fluid" alt=""></a></div>
          <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full" href="{{ asset('landingpage/assets/img/app-gallery/app-gallery-12.png')}}')}}"><img src="{{ asset('landingpage/assets/img/app-gallery/app-gallery-12.png')}}" class="img-fluid" alt=""></a></div>
        </div>
        <div class="swiper-pagination"></div>
      </div>

    </div>

  </section><!-- /Gallery Section -->

  
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

@endsection
