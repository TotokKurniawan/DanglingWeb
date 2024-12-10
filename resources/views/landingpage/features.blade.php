@extends('landingpage.layout.app')
@section('content')

<section id="hero" class="hero section">

    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-6 order-lg-last hero-img" data-aos="zoom-out" data-aos-delay="100">
          <img src="{{ asset('landingpage/assets/img/hero-img.png')}}" class="img-fluid animated" alt="">
        </div>
        <div class="col-lg-6  d-flex flex-column justify-content-center text-center text-md-start" data-aos="fade-in">
          <h2>Features</h2>
          <p>fitur-fitur yang ada pada aplikasi ini</p>
          {{-- <div class="d-flex mt-4 justify-content-center justify-content-md-start">
            <a href="#" class="download-btn"><i class="bi bi-google-play"></i> <span>Google Play</span></a>
            <a href="#" class="download-btn"><i class="bi bi-apple"></i> <span>App Store</span></a>
          </div> --}}
        </div>
      </div>
    </div>

  </section><!-- /Hero Section -->

  <!-- Features Section -->
  <section id="features" class="features section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
      <h2>Features</h2>
      <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
    </div><!-- End Section Title -->

    <div class="container">

      <div class="row gy-5">

        <div class="col-xl-5 d-flex align-items-center" data-aos="fade-up" data-aos-delay="100">
          <img src="{{ asset('landingpage/assets/img/features.svg')}}" class="img-fluid" alt="">
        </div>

        <div class="col-xl-7 d-flex" data-aos="fade-up" data-aos-delay="200">

          <div class="row align-self-center gy-5">

            <div class="col-md-6 icon-box">
              <i class="bi bi-award"></i>
              <div>
                <h4>Corporis voluptates sit</h4>
                <p>Consequuntur sunt aut quasi enim aliquam quae harum pariatur laboris nisi ut aliquip</p>
              </div>
            </div><!-- End Feature Item -->

            <div class="col-md-6 icon-box">
              <i class="bi bi-card-checklist"></i>
              <div>
                <h4>Ullamco laboris nisi</h4>
                <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt</p>
              </div>
            </div><!-- End Feature Item -->

            <div class="col-md-6 icon-box">
              <i class="bi bi-dribbble"></i>
              <div>
                <h4>Labore consequatur</h4>
                <p>Aut suscipit aut cum nemo deleniti aut omnis. Doloribus ut maiores omnis facere</p>
              </div>
            </div><!-- End Feature Item -->

            <div class="col-md-6 icon-box">
              <i class="bi bi-filter-circle"></i>
              <div>
                <h4>Beatae veritatis</h4>
                <p>Expedita veritatis consequuntur nihil tempore laudantium vitae denat pacta</p>
              </div>
            </div><!-- End Feature Item -->

            <div class="col-md-6 icon-box">
              <i class="bi bi-lightning-charge"></i>
              <div>
                <h4>Molestiae dolor</h4>
                <p>Et fuga et deserunt et enim. Dolorem architecto ratione tensa raptor marte</p>
              </div>
            </div><!-- End Feature Item -->

            <div class="col-md-6 icon-box">
              <i class="bi bi-patch-check"></i>
              <div>
                <h4>Explicabo consectetur</h4>
                <p>Est autem dicta beatae suscipit. Sint veritatis et sit quasi ab aut inventore</p>
              </div>
            </div><!-- End Feature Item -->

          </div>

        </div>

      </div>

    </div>

  </section><!-- /Features Section -->

  <!-- Feature Details Section -->
  <section id="feature-details" class="feature-details section">

    <div class="container">

      <div class="row gy-4 align-items-center features-item">
        <div class="col-md-5 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="100">
          <img src="{{ asset('landingpage/assets/img/features-1.png')}}" class="img-fluid" alt="">
        </div>
        <div class="col-md-7" data-aos="fade-up" data-aos-delay="100">
          <h3>Voluptatem dignissimos provident quasi corporis voluptates sit assumenda.</h3>
          <p class="fst-italic">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
            magna aliqua.
          </p>
          <ul>
            <li><i class="bi bi-check"></i><span> Ullamco laboris nisi ut aliquip ex ea commodo consequat.</span></li>
            <li><i class="bi bi-check"></i> <span>Duis aute irure dolor in reprehenderit in voluptate velit.</span></li>
            <li><i class="bi bi-check"></i> <span>Ullam est qui quos consequatur eos accusamus.</span></li>
          </ul>
        </div>
      </div><!-- Features Item -->

      <div class="row gy-4 align-items-center features-item">
        <div class="col-md-5 order-1 order-md-2 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="200">
          <img src="{{ asset('landingpage/assets/img/features-2.png')}}" class="img-fluid" alt="">
        </div>
        <div class="col-md-7 order-2 order-md-1" data-aos="fade-up" data-aos-delay="200">
          <h3>Corporis temporibus maiores provident</h3>
          <p class="fst-italic">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
            magna aliqua.
          </p>
          <p>
            Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
            velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
            culpa qui officia deserunt mollit anim id est laborum
          </p>
        </div>
      </div><!-- Features Item -->

      <div class="row gy-4 align-items-center features-item">
        <div class="col-md-5 d-flex align-items-center" data-aos="zoom-out">
          <img src="{{ asset('landingpage/assets/img/features-3.png')}}" class="img-fluid" alt="">
        </div>
        <div class="col-md-7" data-aos="fade-up">
          <h3>Sunt consequatur ad ut est nulla consectetur reiciendis animi voluptas</h3>
          <p>Cupiditate placeat cupiditate placeat est ipsam culpa. Delectus quia minima quod. Sunt saepe odit aut quia voluptatem hic voluptas dolor doloremque.</p>
          <ul>
            <li><i class="bi bi-check"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat.</span></li>
            <li><i class="bi bi-check"></i><span> Duis aute irure dolor in reprehenderit in voluptate velit.</span></li>
            <li><i class="bi bi-check"></i> <span>Facilis ut et voluptatem aperiam. Autem soluta ad fugiat</span>.</li>
          </ul>
        </div>
      </div><!-- Features Item -->

      {{-- <div class="row gy-4 align-items-center features-item">
        <div class="col-md-5 order-1 order-md-2 d-flex align-items-center" data-aos="zoom-out">
          <img src="{{ asset('landingpage/assets/img/features-4.png')}}" class="img-fluid" alt="">
        </div>
        <div class="col-md-7 order-2 order-md-1" data-aos="fade-up">
          <h3>Quas et necessitatibus eaque impedit ipsum animi consequatur incidunt in</h3>
          <p class="fst-italic">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
            magna aliqua.
          </p>
          <p>
            Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
            velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
            culpa qui officia deserunt mollit anim id est laborum
          </p>
        </div>
      </div><!-- Features Item --> --}}

    </div>

  </section><!-- /Feature Details Section -->

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
