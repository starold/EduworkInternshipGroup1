<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar Kendaraan | ED.RENT</title>
  <meta name="description" content="Pilih kendaraan yang paling sesuai dengan gaya perjalanan dan budget Anda. Semua motor terawat, siap jalan!">

  <!-- Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 (collapse/navbar toggle) + Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- Tailwind (prefix "tw-", preflight dimatikan agar tidak bentrok dengan Bootstrap) -->
  <script src="js/tailwind.config.js"></script>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>

  <!-- Style proyek -->
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body data-assets-base="https://assets.ed-rent.example.com">

  <!-- ===== HEADER / NAVBAR ===== -->
  <header class="site-header">
    <nav class="navbar-ed" aria-label="Navigasi utama">
      <div class="navbar-ed__inner">
        <a class="navbar-ed__brand" href="/" aria-label="ED.RENT - Beranda">ED.RENT</a>

        <button class="navbar-ed__toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarMenu"
                aria-controls="navbarMenu" aria-expanded="false" aria-label="Buka atau tutup menu navigasi">
          <i class="bi bi-list" aria-hidden="true"></i>
        </button>

        <div class="navbar-ed__menu collapse" id="navbarMenu">
          <ul class="navbar-ed__list">
            <li class="navbar-ed__item"><a class="navbar-ed__link" href="/">Home</a></li>
            <li class="navbar-ed__item"><a class="navbar-ed__link navbar-ed__link--active" href="/kendaraan" aria-current="page">Pilih Kendaraan</a></li>
            <li class="navbar-ed__item"><a class="navbar-ed__link" href="/pesanan">Daftar Pesanan</a></li>
          </ul>
          <div class="navbar-ed__actions">
            <a class="btn-ed btn-ed--outline" href="#">
              <i class="bi bi-whatsapp btn-ed__icon btn-ed__icon--wa" aria-hidden="true"></i>
              Butuh bantuan?
            </a>
            <a class="btn-ed btn-ed--primary" href="#">Masuk</a>
          </div>
        </div>
      </div>
    </nav>
  </header>

  <!-- ===== MAIN ===== -->
  <main class="page">
    <div class="container-ed">

      <!-- Judul -->
      <section class="page-head" aria-labelledby="judul-halaman">
        <h1 class="page-head__title" id="judul-halaman">Daftar Kendaraan</h1>
        <p class="page-head__desc">Pilih kendaraan yang paling sesuai dengan gaya perjalanan dan budget Anda. Semua motor terawat, siap jalan!</p>
      </section>

      <!-- Filter -->
      <section class="filter" aria-label="Filter kendaraan">
        <form class="filter__form" action="#" method="get">
          <div class="filter__group filter__group--type">
            <label class="filter__label" for="tipe">Tipe kendaraan</label>
            <div class="filter__control tw-flex tw-items-center">
              <i class="bi bi-person-walking filter__icon" aria-hidden="true"></i>
              <select class="filter__input filter__input--select" id="tipe" name="tipe">
                <option value="" selected>Semua</option>
                <option value="matic">Matic</option>
                <option value="manual">Manual</option>
              </select>
            </div>
          </div>

          <div class="filter__group filter__group--date">
            <label class="filter__label" for="tanggal">Tanggal &amp; jam sewa</label>
            <div class="filter__control tw-flex tw-items-center">
              <i class="bi bi-calendar3 filter__icon" aria-hidden="true"></i>
              <input class="filter__input" id="tanggal" name="tanggal" type="text" value="Min, 23 Des" autocomplete="off">
              <span class="filter__divider" aria-hidden="true"></span>
              <i class="bi bi-stopwatch-fill filter__icon filter__icon--accent" aria-hidden="true"></i>
              <input class="filter__input filter__input--time" id="jam" name="jam" type="text" value="15:00" aria-label="Jam sewa" autocomplete="off">
            </div>
          </div>

          <div class="filter__group filter__group--duration">
            <label class="filter__label" for="durasi">Durasi sewa</label>
            <div class="filter__control tw-flex tw-items-center">
              <i class="bi bi-calendar-date-fill filter__icon" aria-hidden="true"></i>
              <input class="filter__input" id="durasi" name="durasi" type="text" value="1 hari" autocomplete="off">
              <span class="filter__hint">Durasi sewa 24 jam/hari</span>
            </div>
          </div>

          <div class="filter__group filter__group--search">
            <label class="filter__label" for="cari">Cari kendaraan</label>
            <div class="filter__control tw-flex tw-items-center">
              <i class="bi bi-search filter__icon" aria-hidden="true"></i>
              <input class="filter__input" id="cari" name="cari" type="search" placeholder="Nama motor" autocomplete="off">
            </div>
          </div>
        </form>
      </section>

      <!-- Daftar kendaraan (dirender oleh js/main.js dari template di bawah) -->
      <section class="vehicles" aria-label="Daftar kendaraan">
        <ul class="vehicles__grid" id="vehicleGrid"></ul>
      </section>

      <template id="vehicleCardTemplate">
        <li class="vehicle-card">
          <article class="vehicle-card__body">
            <header class="vehicle-card__head">
              <h2 class="vehicle-card__name"></h2>
              <p class="vehicle-card__type"></p>
            </header>
            <div class="vehicle-card__media">
              <img class="vehicle-card__img" src="" alt="" loading="lazy" width="208" height="198">
            </div>
            <div class="vehicle-card__meta tw-flex tw-items-center">
              <span class="vehicle-card__plate"><i class="bi bi-card-heading" aria-hidden="true"></i><span class="vehicle-card__plate-text"></span></span>
              <span class="vehicle-card__status"></span>
            </div>
            <div class="vehicle-card__foot tw-flex tw-items-center">
              <p class="vehicle-card__price"><span class="vehicle-card__price-value"></span><span class="vehicle-card__price-unit">hari</span></p>
              <button class="btn-ed btn-ed--primary" type="button">Booking</button>
            </div>
          </article>
        </li>
      </template>

      <!-- Syarat & ketentuan -->
      <section class="terms" aria-labelledby="judul-syarat">
        <h2 class="terms__title" id="judul-syarat">Syarat &amp; Ketentuan Rental Motor</h2>
        <p class="terms__lead">Transparansi adalah kunci. Pastikan Anda memenuhi beberapa persyaratan dasar sebelum melakukan pemesanan.</p>
        <ol class="terms__list">
          <li class="terms__item">Lakukan pemesanan minimal <strong>1x24 jam</strong> sebelum waktu penggunaan. Setelah pesanan dikonfirmasi, penyewa wajib melakukan konfirmasi pengambilan motor melalui nomor whatsapp atau datang ke lokasi ED.RENT.</li>
          <li class="terms__item">Membawa <strong>2 kartu identitas</strong> (SIM A, KK, KTP, ID Kerja atau ID Pelajar)</li>
          <li class="terms__item">Menyertakan akun sosial media dan <strong>nomor whatsapp</strong> yang aktif.</li>
          <li class="terms__item">Pada saat serah terima kendaraan, pihak penyedia rental akan melakukan <strong>pengecekan keaslian data</strong> dan kondisi kendaraan.</li>
          <li class="terms__item">Pihak penyedia rental <strong>berhak membatalkan</strong> apabila data tidak sesuai dan syarat sewa tidak dapat dilengkapi.</li>
          <li class="terms__item">Apabila pembayaran telah selesai dan kendaraan yang dipesan tidak tersedia, penyewa <strong>berhak memilih kendaraan lain</strong> yang tersedia tanpa dikenakan biaya tambahan.</li>
        </ol>
      </section>

    </div>
  </main>

  <!-- ===== FOOTER ===== -->
  <footer class="site-footer">
    <div class="container-ed">
      <div class="site-footer__top">
        <div class="site-footer__brand">
          <a class="site-footer__logo" href="/" aria-label="ED.RENT - Beranda">ED.RENT</a>
          <p class="site-footer__desc">Pilihan terlengkap, harga transparan, dan layanan 24 jam. Siap menemani setiap petualangan Anda.</p>
        </div>

        <nav class="site-footer__nav" aria-label="Tautan footer">
          <div class="site-footer__col site-footer__col--about">
            <h2 class="site-footer__heading">About</h2>
            <ul class="site-footer__links">
              <li><a class="site-footer__link" href="#">Chat Admin</a></li>
            </ul>
          </div>
          <div class="site-footer__col site-footer__col--socials">
            <h2 class="site-footer__heading">Socials</h2>
            <ul class="site-footer__links">
              <li><a class="site-footer__link" href="#">Discord</a></li>
              <li><a class="site-footer__link" href="#">Instagram</a></li>
              <li><a class="site-footer__link" href="#">Twitter</a></li>
              <li><a class="site-footer__link" href="#">Facebook</a></li>
            </ul>
          </div>
        </nav>
      </div>

      <div class="site-footer__bottom">
        <p class="site-footer__copy">&copy;2025 ED.RENT. All rights reserved</p>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>