<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kendaraan - ED.RENT</title>
    <!-- Bootstrap CSS v5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <header class="navbar-ed">
        <div class="container">
            <nav class="navbar navbar-expand-lg p-0">
                <div class="container-fluid p-0">
                    <a class="navbar-ed__brand" href="#">ED.RENT</a>
                    
                    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarEdContent" aria-controls="navbarEdContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse justify-content-end align-items-center gap-3" id="navbarEdContent">
                        <ul class="navbar-nav align-items-lg-center gap-lg-2 mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="navbar-ed__nav-link" href="#">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="navbar-ed__nav-link active" href="#">Pilih Kendaraan</a>
                            </li>
                            <li class="nav-item">
                                <a class="navbar-ed__nav-link" href="#">Daftar Pesanan</a>
                            </li>
                        </ul>
                        <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2 mt-3 mt-lg-0">
                            <a href="https://whatsapp.com" target="_blank" class="navbar-ed__btn-whatsapp">
                                <i class="fa-brands fa-whatsapp text-success fs-5"></i> Butuh bantuan?
                            </a>
                            <a href="#" class="navbar-ed__btn-login text-center">Masuk</a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main class="container my-4">
        
        <!-- Page Title -->
        <div class="mb-4">
            <h1 class="h3 fw-bold text-dark mb-1">Daftar Kendaraan</h1>
            <p class="text-muted" style="font-size: 14px;">Pilih kendaraan yang paling sesuai dengan gaya perjalanan dan budget Anda. Semua motor terawat, siap jalan!</p>
        </div>

        <!-- Filter Bar -->
        <div class="filter-section">
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <div class="filter-group__label">Tipe kendaraan</div>
                    <div class="filter-group__control">
                        <span><i class="fa-solid fa-motorcycle text-muted me-2"></i> Semua</span>
                        <i class="fa-solid fa-chevron-down text-muted fs-7"></i>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="filter-group__label">Tanggal & jam sewa</div>
                    <div class="filter-group__control">
                        <span><i class="fa-regular fa-calendar text-muted me-2"></i> Min, 23 Des &nbsp; <i class="fa-regular fa-clock text-muted ms-1 me-1"></i> 15:00</span>
                        <i class="fa-solid fa-chevron-down text-muted fs-7"></i>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="filter-group__label">Durasi sewa</div>
                    <div class="filter-group__control">
                        <span><i class="fa-regular fa-clock text-muted me-2"></i> 1 Hari</span>
                        <i class="fa-solid fa-chevron-down text-muted fs-7"></i>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="filter-group__label">Cari kendaraan</div>
                    <div class="filter-group__control">
                        <span class="text-muted"><i class="fa-solid fa-magnifying-glass me-2"></i> Nama motor</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Grid (12 Items matching Figma design) -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            
            <!-- Item 1 -->
            <div class="col">
                <div class="vehicle-card">
                    <div>
                        <div class="vehicle-card__title">Vega Force</div>
                        <div class="vehicle-card__subtitle">Matic</div>
                        <div class="vehicle-card__img-container">
                            <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80" alt="Vega Force" class="vehicle-card__img">
                        </div>
                        <div class="vehicle-card__meta">
                            <span class="text-secondary"><i class="fa-regular fa-id-card me-1"></i> AB 9001 DE</span>
                            <span class="vehicle-card__badge-status">Tersedia</span>
                        </div>
                    </div>
                    <div class="vehicle-card__footer">
                        <div class="vehicle-card__price">Rp75.000<span>/hari</span></div>
                        <a href="#" class="vehicle-card__btn-booking">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="col">
                <div class="vehicle-card">
                    <div>
                        <div class="vehicle-card__title">Vega Force</div>
                        <div class="vehicle-card__subtitle">Matic</div>
                        <div class="vehicle-card__img-container">
                            <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80" alt="Vega Force" class="vehicle-card__img">
                        </div>
                        <div class="vehicle-card__meta">
                            <span class="text-secondary"><i class="fa-regular fa-id-card me-1"></i> AB 9001 DE</span>
                            <span class="vehicle-card__badge-status">Tersedia</span>
                        </div>
                    </div>
                    <div class="vehicle-card__footer">
                        <div class="vehicle-card__price">Rp75.000<span>/hari</span></div>
                        <a href="#" class="vehicle-card__btn-booking">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Item 3 -->
            <div class="col">
                <div class="vehicle-card">
                    <div>
                        <div class="vehicle-card__title">Vega Force</div>
                        <div class="vehicle-card__subtitle">Matic</div>
                        <div class="vehicle-card__img-container">
                            <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80" alt="Vega Force" class="vehicle-card__img">
                        </div>
                        <div class="vehicle-card__meta">
                            <span class="text-secondary"><i class="fa-regular fa-id-card me-1"></i> AB 9001 DE</span>
                            <span class="vehicle-card__badge-status">Tersedia</span>
                        </div>
                    </div>
                    <div class="vehicle-card__footer">
                        <div class="vehicle-card__price">Rp75.000<span>/hari</span></div>
                        <a href="#" class="vehicle-card__btn-booking">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Item 4 -->
            <div class="col">
                <div class="vehicle-card">
                    <div>
                        <div class="vehicle-card__title">Vega Force</div>
                        <div class="vehicle-card__subtitle">Matic</div>
                        <div class="vehicle-card__img-container">
                            <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80" alt="Vega Force" class="vehicle-card__img">
                        </div>
                        <div class="vehicle-card__meta">
                            <span class="text-secondary"><i class="fa-regular fa-id-card me-1"></i> AB 9001 DE</span>
                            <span class="vehicle-card__badge-status">Tersedia</span>
                        </div>
                    </div>
                    <div class="vehicle-card__footer">
                        <div class="vehicle-card__price">Rp75.000<span>/hari</span></div>
                        <a href="#" class="vehicle-card__btn-booking">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Item 5 -->
            <div class="col">
                <div class="vehicle-card">
                    <div>
                        <div class="vehicle-card__title">Vega Force</div>
                        <div class="vehicle-card__subtitle">Matic</div>
                        <div class="vehicle-card__img-container">
                            <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80" alt="Vega Force" class="vehicle-card__img">
                        </div>
                        <div class="vehicle-card__meta">
                            <span class="text-secondary"><i class="fa-regular fa-id-card me-1"></i> AB 9001 DE</span>
                            <span class="vehicle-card__badge-status">Tersedia</span>
                        </div>
                    </div>
                    <div class="vehicle-card__footer">
                        <div class="vehicle-card__price">Rp75.000<span>/hari</span></div>
                        <a href="#" class="vehicle-card__btn-booking">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Item 6 -->
            <div class="col">
                <div class="vehicle-card">
                    <div>
                        <div class="vehicle-card__title">Vega Force</div>
                        <div class="vehicle-card__subtitle">Matic</div>
                        <div class="vehicle-card__img-container">
                            <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80" alt="Vega Force" class="vehicle-card__img">
                        </div>
                        <div class="vehicle-card__meta">
                            <span class="text-secondary"><i class="fa-regular fa-id-card me-1"></i> AB 9001 DE</span>
                            <span class="vehicle-card__badge-status">Tersedia</span>
                        </div>
                    </div>
                    <div class="vehicle-card__footer">
                        <div class="vehicle-card__price">Rp75.000<span>/hari</span></div>
                        <a href="#" class="vehicle-card__btn-booking">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Item 7 -->
            <div class="col">
                <div class="vehicle-card">
                    <div>
                        <div class="vehicle-card__title">Vega Force</div>
                        <div class="vehicle-card__subtitle">Matic</div>
                        <div class="vehicle-card__img-container">
                            <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80" alt="Vega Force" class="vehicle-card__img">
                        </div>
                        <div class="vehicle-card__meta">
                            <span class="text-secondary"><i class="fa-regular fa-id-card me-1"></i> AB 9001 DE</span>
                            <span class="vehicle-card__badge-status">Tersedia</span>
                        </div>
                    </div>
                    <div class="vehicle-card__footer">
                        <div class="vehicle-card__price">Rp75.000<span>/hari</span></div>
                        <a href="#" class="vehicle-card__btn-booking">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Item 8 -->
            <div class="col">
                <div class="vehicle-card">
                    <div>
                        <div class="vehicle-card__title">Vega Force</div>
                        <div class="vehicle-card__subtitle">Matic</div>
                        <div class="vehicle-card__img-container">
                            <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80" alt="Vega Force" class="vehicle-card__img">
                        </div>
                        <div class="vehicle-card__meta">
                            <span class="text-secondary"><i class="fa-regular fa-id-card me-1"></i> AB 9001 DE</span>
                            <span class="vehicle-card__badge-status">Tersedia</span>
                        </div>
                    </div>
                    <div class="vehicle-card__footer">
                        <div class="vehicle-card__price">Rp75.000<span>/hari</span></div>
                        <a href="#" class="vehicle-card__btn-booking">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Item 9 -->
            <div class="col">
                <div class="vehicle-card">
                    <div>
                        <div class="vehicle-card__title">Vega Force</div>
                        <div class="vehicle-card__subtitle">Matic</div>
                        <div class="vehicle-card__img-container">
                            <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80" alt="Vega Force" class="vehicle-card__img">
                        </div>
                        <div class="vehicle-card__meta">
                            <span class="text-secondary"><i class="fa-regular fa-id-card me-1"></i> AB 9001 DE</span>
                            <span class="vehicle-card__badge-status">Tersedia</span>
                        </div>
                    </div>
                    <div class="vehicle-card__footer">
                        <div class="vehicle-card__price">Rp75.000<span>/hari</span></div>
                        <a href="#" class="vehicle-card__btn-booking">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Item 10 -->
            <div class="col">
                <div class="vehicle-card">
                    <div>
                        <div class="vehicle-card__title">Vega Force</div>
                        <div class="vehicle-card__subtitle">Matic</div>
                        <div class="vehicle-card__img-container">
                            <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80" alt="Vega Force" class="vehicle-card__img">
                        </div>
                        <div class="vehicle-card__meta">
                            <span class="text-secondary"><i class="fa-regular fa-id-card me-1"></i> AB 9001 DE</span>
                            <span class="vehicle-card__badge-status">Tersedia</span>
                        </div>
                    </div>
                    <div class="vehicle-card__footer">
                        <div class="vehicle-card__price">Rp75.000<span>/hari</span></div>
                        <a href="#" class="vehicle-card__btn-booking">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Item 11 -->
            <div class="col">
                <div class="vehicle-card">
                    <div>
                        <div class="vehicle-card__title">Vega Force</div>
                        <div class="vehicle-card__subtitle">Matic</div>
                        <div class="vehicle-card__img-container">
                            <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80" alt="Vega Force" class="vehicle-card__img">
                        </div>
                        <div class="vehicle-card__meta">
                            <span class="text-secondary"><i class="fa-regular fa-id-card me-1"></i> AB 9001 DE</span>
                            <span class="vehicle-card__badge-status">Tersedia</span>
                        </div>
                    </div>
                    <div class="vehicle-card__footer">
                        <div class="vehicle-card__price">Rp75.000<span>/hari</span></div>
                        <a href="#" class="vehicle-card__btn-booking">Booking</a>
                    </div>
                </div>
            </div>

            <!-- Item 12 -->
            <div class="col">
                <div class="vehicle-card">
                    <div>
                        <div class="vehicle-card__title">Vega Force</div>
                        <div class="vehicle-card__subtitle">Matic</div>
                        <div class="vehicle-card__img-container">
                            <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80" alt="Vega Force" class="vehicle-card__img">
                        </div>
                        <div class="vehicle-card__meta">
                            <span class="text-secondary"><i class="fa-regular fa-id-card me-1"></i> AB 9001 DE</span>
                            <span class="vehicle-card__badge-status">Tersedia</span>
                        </div>
                    </div>
                    <div class="vehicle-card__footer">
                        <div class="vehicle-card__price">Rp75.000<span>/hari</span></div>
                        <a href="#" class="vehicle-card__btn-booking">Booking</a>
                    </div>
                </div>
            </div>

        </div>

        <section class="terms-section">
            <h2 class="terms-section__title">Syarat & Ketentuan Rental Motor</h2>
            <p class="terms-section__desc">Transparansi adalah kunci. Pastikan Anda memenuhi beberapa persyaratan dasar sebelum melakukan pemesanan.</p>
            <ol class="terms-list">
                <li>Lakukan pemesanan minimal <strong>1x24 jam</strong> sebelum waktu penggunaan. Setelah pesanan dikonfirmasi, penyewa wajib melakukan konfirmasi pengambilan motor melalui nomor whatsapp atau datang ke lokasi ED.RENT.</li>
                <li>Membawa <strong>2 kartu identitas</strong> (SIM A, KK, KTP, ID Kerja atau ID Pelajar).</li>
                <li>Menyertakan akun sosial media dan <strong>nomor whatsapp</strong> yang aktif.</li>
                <li>Pada saat serah terima kendaraan, pihak penyewa rental akan melakukan <strong>pengecekan keaslian data</strong> dan kondisi kendaraan.</li>
                <li>Pihak penyedia rental <strong>berhak membatalkan</strong> apabila data tidak sesuai dan syarat sewa tidak dapat dilengkapi.</li>
                <li>Apabila pembayaran telah selesai dan kendaraan yang dipesan tidak tersedia, penyewa <strong>berhak memilih kendaraan lain</strong> yang tersedia tanpa dikenakan biaya tambahan.</li>
            </ol>
        </section>

    </main>

    <footer class="footer-ed">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <a class="navbar-ed__brand" href="#">ED.RENT</a>
                    <p class="footer-ed__brand-desc">Pilihan terlengkap, harga transparan, dan layanan 24 jam. Siap menemani setiap petualangan Anda.</p>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="footer-ed__heading">About</div>
                    <a href="#" class="footer-ed__link">Chat Admin</a>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="footer-ed__heading">Socials</div>
                    <div class="row">
                        <div class="col-6">
                            <a href="#" class="footer-ed__link">Discord</a>
                            <a href="#" class="footer-ed__link">Instagram</a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="footer-ed__link">Twitter</a>
                            <a href="#" class="footer-ed__link">Facebook</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-ed__bottom d-flex flex-column flex-sm-row justify-content-between align-items-center">
                <span>©2025 ED.RENT. All rights reserved</span>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle v5.3 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Custom script initialization if needed
        document.addEventListener('DOMContentLoaded', () => {
            console.log('ED.RENT Vehicle List initialized successfully.');
        });
    </script>
</body>
</html>