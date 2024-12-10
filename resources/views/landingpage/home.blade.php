@extends('landingpage.layout.app')
@section('content')
    <section id="hero" class="hero section">

        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-6 order-lg-last hero-img" data-aos="zoom-out" data-aos-delay="100">
                    <img src="{{ asset('landingpage/assets/img/hero-img.png') }}" class="img-fluid animated"
                        alt="Dangling App">
                </div>
                <div class="col-lg-6 d-flex flex-column justify-content-center text-center text-md-start" data-aos="fade-in"
                    style="margin-top: -20px; padding-top: 0;">
                    <h2>DangLing</h2>
                    <p class="mt-2">Temukan dan panggil pedagang lokal di sekitar Anda dengan mudah melalui aplikasi
                        DangLing, dukung usaha kecil, dan nikmati kemudahan berbelanja langsung dari genggaman Anda.</p>
                    <div class="d-flex mt-3 justify-content-center justify-content-md-start">
                        <a href="#" class="download-btn"><i class="bi bi-google-play"></i> <span>Google
                                Play</span></a>
                    </div>
                </div>

            </div>
        </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section light-background">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>About</h2>
            <p>Menghubungkan Anda dengan pedagang lokal, lebih mudah dan cepat</p>
        </div><!-- End Section Title -->

        <div class="container">

            <div class="row gy-4">

                <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
                    <p>
                        Dangling adalah platform yang memungkinkan Anda untuk menemukan dan memanggil pedagang keliling di
                        sekitar Anda dengan mudah. Kami percaya bahwa mendukung pedagang lokal membantu komunitas tumbuh
                        lebih kuat dan membawa kenyamanan bagi semua.
                    </p>
                    <ul>
                        <li><i class="bi bi-check2-circle"></i> <span>Akses cepat ke berbagai pedagang di sekitar
                                Anda.</span></li>
                        <li><i class="bi bi-check2-circle"></i> <span>Membantu pedagang lokal mendapatkan lebih banyak
                                pelanggan.</span></li>
                        <li><i class="bi bi-check2-circle"></i> <span>Kemudahan memesan produk dan jasa langsung dari
                                perangkat Anda.</span></li>
                    </ul>
                </div>

                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                    <p>Dengan Dangling, kami tidak hanya menghubungkan Anda dengan pedagang lokal tetapi juga memberikan
                        kesempatan bagi para pedagang untuk lebih berkembang dan menjangkau lebih banyak pelanggan. Ayo
                        dukung pedagang sekitar Anda dan nikmati layanan langsung ke lokasi Anda!</p>
                </div>

            </div>

        </div>

    </section><!-- /About Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Features</h2>
            <p>Berikut adalah fitu fitur yang ada pada DangLing</p>
        </div><!-- End Section Title -->

        <div class="container">

            <div class="row gy-5">

                <div class="col-xl-5 d-flex align-items-center" data-aos="fade-up" data-aos-delay="100">
                    <img src="{{ asset('landingpage/assets/img/features.svg') }}" class="img-fluid" alt="">
                </div>

                <div class="col-xl-7 d-flex" data-aos="fade-up" data-aos-delay="200">

                    <div class="row align-self-center gy-5">

                        <div class="col-md-6 icon-box">
                            <i class="bi bi-award"></i>
                            <div>
                                <h4>Fitur Pelacak Pedagang</h4>
                                <p>Fitur ini berfungsi untuk melacak pedagang dalam radius 2 km di Kota Batu</p>
                            </div>
                        </div><!-- End Feature Item -->

                        <div class="col-md-6 icon-box">
                            <i class="bi bi-card-checklist"></i>
                            <div>
                                <h4>Fitur Pemanggil Pedagang</h4>
                                <p>Fitur ini bisa memanggil pedagang dalam radius 10 km seperti fitur sebelumnya</p>
                            </div>
                        </div><!-- End Feature Item -->

                        <div class="col-md-6 icon-box">
                            <i class="bi bi-dribbble"></i>
                            <div>
                                <h4>Multiple akun</h4>
                                <p>maksud dari fitur ini adalah seseorang dapat menjadi pembeli dan pedagang</p>
                            </div>
                        </div><!-- End Feature Item -->

                        <div class="col-md-6 icon-box">
                            <i class="bi bi-filter-circle"></i>
                            <div>
                                <h4>Fitur Message </h4>
                                <p>Fitur mmessage berfungsi sebagai pembantu antara pembeli dan pedagang dalam melakukan
                                    transaksi</p>
                            </div>
                        </div><!-- End Feature Item -->

                        <div class="col-md-6 icon-box">
                            <i class="bi bi-lightning-charge"></i>
                            <div0>
                                <h4>Fitur Mystore</h4>
                                <p>Fitur ini khusus untuk pedagag, jadi pedagang bisa merubah status pedagang dan menambah
                                    produk yang akan dijual</p>
                            </div0>
                        </div><!-- End Feature Item -->

                        <div class="col-md-6 icon-box">
                            <i class="bi bi-patch-check"></i>
                            <div>
                                <h4>Fitur Profile</h4>
                                <p>Fitur ini menampilkan informasi seputar pembeli atau pedagang itu sendiri. </p>
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
                    <img src="{{ asset('landingpage/assets/img/features-1.png') }}" class="img-fluid" alt="">
                </div>
                <div class="col-md-7" data-aos="fade-up" data-aos-delay="100">
                    <h3>Metode Pembayaran Biaya Admin</h3>
                    <p>
                        Biaya admin di Dangling itu hanya Rp 1000,- dan Metode Pembayaran yang digunakan aplikasi dangling
                        hanya ada 2 cara yaitu :
                    </p>
                    <ul>
                        <li><i class="bi bi-check"></i><span>Transfer Dana sesuai nomor yang ada di aplikasi
                                Dangling.</span></li>
                        <li><i class="bi bi-check"></i> <span>Pembayaran Cash melalui pedagang.</span></li>
                    </ul>
                </div>
            </div><!-- Features Item -->

            <div class="row gy-4 align-items-center features-item">
                <div class="col-md-5 order-1 order-md-2 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="200">
                    <img src="{{ asset('landingpage/assets/img/features-2.png') }}" class="img-fluid" alt="">
                </div>
                <div class="col-md-7 order-2 order-md-1" data-aos="fade-up" data-aos-delay="200">
                    <h3>Kemudahan dan Keamanan dalam Bertransaksi dengan Pedagang Lokal</h3>
                    <p class="fst-italic">
                        Dengan *Dangling*, berbelanja dari pedagang lokal menjadi lebih cepat, aman, dan praktis, langsung
                        dari kenyamanan rumah Anda.
                    </p>
                    <p>
                        Aplikasi kami memastikan bahwa Anda dapat memesan dengan tenang, tanpa khawatir dengan kualitas
                        layanan. Kami bekerja sama dengan para pedagang terpercaya di sekitar Anda, memberikan pengalaman
                        transaksi yang nyaman dan dapat diandalkan. Mari dukung pedagang lokal dan tingkatkan kenyamanan
                        hidup Anda!
                    </p>
                </div>
            </div><!-- Features Item -->

            <div class="row gy-4 align-items-center features-item">
                <div class="col-md-5 d-flex align-items-center" data-aos="zoom-out">
                    <img src="{{ asset('landingpage/assets/img/features-3.png') }}" class="img-fluid" alt="">
                </div>
                <div class="col-md-7" data-aos="fade-up">
                    <h3>Kemudahan Akses ke Pedagang Lokal dengan Sekali Sentuhan</h3>
                    <p>Dengan *Dangling*, Anda dapat menemukan pedagang lokal yang ada di sekitar Anda secara real-time. Tak
                        perlu lagi mencari jauh-jauh, cukup buka aplikasi dan panggil pedagang yang Anda butuhkan.</p>
                    <ul>
                        <li><i class="bi bi-check"></i> <span>Akses instan ke berbagai layanan dan produk dari pedagang
                                lokal.</span></li>
                        <li><i class="bi bi-check"></i><span> Pesan dan beli produk dengan mudah dan nyaman dari rumah
                                Anda.</span></li>
                        <li><i class="bi bi-check"></i> <span>Dukung pertumbuhan usaha kecil di lingkungan Anda.</span></li>
                    </ul>
                </div>
            </div><!-- Features Item -->

        </div>

    </section><!-- /Feature Details Section -->

    <!-- Gallery Section -->
    <section id="gallery" class="gallery section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Gallery</h2>
            <p>Berikut adalah tampilan dari aplikasi dangling</p>
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
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full"
                            href="{{ asset('landingpage/assets/img/app-gallery/1.jpg') }}')}}"><img
                                src="{{ asset('landingpage/assets/img/app-gallery/1.jpg') }}" class="img-fluid"
                                alt=""></a></div>
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full"
                            href="{{ asset('landingpage/assets/img/app-gallery/2.jpg') }}')}}"><img
                                src="{{ asset('landingpage/assets/img/app-gallery/2.jpg') }}" class="img-fluid"
                                alt=""></a></div>
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full"
                            href="{{ asset('landingpage/assets/img/app-gallery/3.jpg') }}')}}"><img
                                src="{{ asset('landingpage/assets/img/app-gallery/3.jpg') }}" class="img-fluid"
                                alt=""></a></div>
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full"
                            href="{{ asset('landingpage/assets/img/app-gallery/8.png') }}')}}"><img
                                src="{{ asset('landingpage/assets/img/app-gallery/8.png') }}" class="img-fluid"
                                alt=""></a></div>
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full"
                            href="{{ asset('landingpage/assets/img/app-gallery/4.jpg') }}')}}"><img
                                src="{{ asset('landingpage/assets/img/app-gallery/4.jpg') }}" class="img-fluid"
                                alt=""></a></div>
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full"
                            href="{{ asset('landingpage/assets/img/app-gallery/5.jpg') }}')}}"><img
                                src="{{ asset('landingpage/assets/img/app-gallery/5.jpg') }}" class="img-fluid"
                                alt=""></a></div>
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full"
                            href="{{ asset('landingpage/assets/img/app-gallery/6.jpg') }}')}}"><img
                                src="{{ asset('landingpage/assets/img/app-gallery/6.jpg') }}" class="img-fluid"
                                alt=""></a></div>
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery-full"
                            href="{{ asset('landingpage/assets/img/app-gallery/7.jpg') }}')}}"><img
                                src="{{ asset('landingpage/assets/img/app-gallery/7.jpg') }}" class="img-fluid"
                                alt=""></a></div>

                </div>
                <div class="swiper-pagination"></div>
            </div>

        </div>

    </section><!-- /Gallery Section -->

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section light-background">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Testimonials</h2>
            <p>Dibawah ini adalah testimoni dari pelanggan setelah menggunakan aplikasi DangLing</p>
        </div><!-- End Section Title -->

        <div class="container" data-aos="fade-up" data-aos-delay="100">

            <div class="swiper init-swiper">
                <script type="application/json" class="swiper-config">
          {
            "loop": true,
            "speed": 600,
            "autoplay": {
              "delay": 5000
            },
            "slidesPerView": "auto",
            "pagination": {
              "el": ".swiper-pagination",
              "type": "bullets",
              "clickable": true
            },
            "breakpoints": {
              "320": {
                "slidesPerView": 1,
                "spaceBetween": 40
              },
              "1200": {
                "slidesPerView": 2,
                "spaceBetween": 20
              }
            }
          }
        </script>
                <div class="swiper-wrapper">

                    <div class="swiper-slide">
                        <div class="testimonial-wrap">
                            <div class="testimonial-item">
                                <h3>Siti Rahma</h3>
                                <h4>Pengguna Setia Dangling</h4>
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i>
                                </div>
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    <span>Aplikasi Dangling benar-benar membantu saya dalam memenuhi kebutuhan sehari-hari.
                                        Saya bisa memesan dari pedagang keliling dengan mudah, dan mereka bisa datang
                                        langsung ke lokasi saya. Sangat berguna, terutama saat cuaca tidak mendukung untuk
                                        keluar rumah!</span>
                                    <i class="bi bi-quote quote-icon-right"></i>
                                </p>
                            </div>


                        </div>
                    </div><!-- End testimonial item -->

                    <div class="swiper-slide">
                        <div class="testimonial-wrap">
                            <div class="testimonial-item">
                                <h3>Rian Setiawan</h3>
                                <h4>Pengguna Dangling</h4>
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i>
                                </div>
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    <span>Dangling sangat memudahkan saya untuk menemukan pedagang keliling di sekitar
                                        rumah! Dengan sekali klik, saya bisa tahu pedagang mana yang sedang beroperasi dekat
                                        lokasi saya, jadi tidak perlu keluar jauh-jauh untuk mencari kebutuhan harian.
                                        Sangat praktis dan hemat waktu!</span>
                                    <i class="bi bi-quote quote-icon-right"></i>
                                </p>
                            </div>

                        </div>
                    </div><!-- End testimonial item -->

                    <div class="swiper-slide">
                        <div class="testimonial-wrap">
                            <div class="testimonial-item">
                                <h3>Andi Saputra</h3>
                                <h4>Pedagang Keliling</h4>
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i>
                                </div>
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    <span>Sejak bergabung dengan aplikasi Dangling, pelanggan saya semakin bertambah dan
                                        lebih mudah ditemukan oleh mereka yang membutuhkan. Tidak hanya itu, saya bisa
                                        menjangkau lebih banyak wilayah tanpa khawatir tidak ada pembeli. Aplikasi ini
                                        sangat membantu dalam meningkatkan pendapatan saya!</span>
                                    <i class="bi bi-quote quote-icon-right"></i>
                                </p>
                            </div>

                        </div>
                    </div><!-- End testimonial item -->

                    <div class="swiper-slide">
                        <div class="testimonial-wrap">
                            <div class="testimonial-item">
                                <h3>Lina Kartika</h3>
                                <h4>Penjual Makanan Keliling</h4>
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i>
                                </div>
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    <span>Dangling sangat membantu saya sebagai pedagang makanan keliling. Dengan aplikasi
                                        ini, saya dapat lebih mudah terhubung dengan pelanggan di sekitar dan mereka bisa
                                        memesan langsung. Pendapatan saya semakin stabil, dan saya tidak perlu keliling
                                        terlalu jauh. Terima kasih, Dangling!</span>
                                    <i class="bi bi-quote quote-icon-right"></i>
                                </p>
                            </div>

                        </div>
                    </div><!-- End testimonial item -->

                </div>
                <div class="swiper-pagination"></div>
            </div>

        </div>

    </section><!-- /Testimonials Section -->

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>
@endsection
