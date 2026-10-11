<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ED.RENT - Dashboard Admin</title>
    <!-- Google Fonts & Tailwind & Bootstrap -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563EB;
            --primary-light: #EFF6FF;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --border-color: #E2E8F0;
            --bg-body: #F8FAFC;
            --sidebar-bg: #FFFFFF;
            --card-bg: #FFFFFF;
            --radius-md: 12px;
            --radius-lg: 16px;
            --font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-body);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Custom scrollbar and sidebar transitions */
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
                height: 100vh;
            }
            .sidebar.active {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between">

    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 px-4 lg:px-12 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- Mobile Hamburger Toggle -->
            <button id="sidebarToggle" class="lg:hidden text-slate-700 focus:outline-none text-xl p-2" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <a href="#" class="text-blue-600 font-bold text-2xl tracking-wider">ED.RENT</a>
        </div>
        <nav class="hidden lg:flex items-center gap-8 font-medium text-sm text-slate-600">
            <a href="#" class="hover:text-blue-600 transition-colors">Home</a>
            <a href="#" class="hover:text-blue-600 transition-colors">Pilih Kendaraan</a>
            <a href="#" class="hover:text-blue-600 transition-colors">Daftar Pesanan</a>
            <a href="https://wa.me/" target="_blank" class="flex items-center gap-2 border border-slate-200 px-4 py-2 rounded-full text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fab fa-whatsapp text-emerald-500 text-lg"></i> Butuh bantuan?
            </a>
            <a href="#" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-sm">Masuk</a>
        </nav>
        <!-- Mobile Right Masuk Button -->
        <div class="flex lg:hidden items-center gap-3">
            <a href="#" class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm font-semibold">Masuk</a>
        </div>
    </header>

    <div class="flex flex-1 relative">
        <!-- Backdrop for mobile sidebar -->
        <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 z-40 hidden lg:hidden"></div>

        <aside id="sidebar" class="sidebar w-64 bg-white border-r border-slate-200 flex flex-col justify-between p-6 shrink-0">
            <div class="flex flex-col gap-6">
                <span class="text-xs font-semibold text-slate-400 tracking-wider uppercase">Main Menu</span>
                <nav class="flex flex-col gap-2">
                    <a href="#" class="flex items-center gap-3 px-4 py-3 bg-blue-600 text-white rounded-xl font-medium shadow-sm">
                        <i class="fas fa-th-large text-lg"></i> Dashboard
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-xl font-medium transition-colors">
                        <i class="fas fa-list-alt text-lg"></i> Daftar Pesanan
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-xl font-medium transition-colors">
                        <i class="fas fa-motorcycle text-lg"></i> Kendaraan
                    </a>
                </nav>
            </div>
            <div>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-red-50 hover:text-red-600 rounded-xl font-medium transition-colors">
                    <i class="fas fa-sign-out-alt text-lg"></i> Log Out
                </a>
            </div>
        </aside>

        <main class="flex-1 p-4 lg:p-10 bg-[#F8FAFC]">
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 mb-6">Dashboard</h1>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Column (Widgets) -->
                <div class="lg:col-span-7 flex flex-col gap-6">
                    
                    <!-- Widget: Top Orderan Rental -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">5 Top Orderan Rental</h3>
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-6 py-4">
                            <!-- Circular Donut Visual Simulation -->
                            <div class="relative w-44 h-44 flex items-center justify-center">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-slate-100" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                    <path class="text-blue-400" stroke-dasharray="60, 100" stroke-width="4" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                    <path class="text-blue-600" stroke-dasharray="35, 100" stroke-dashoffset="-60" stroke-width="4" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                </svg>
                                <div class="absolute flex flex-col items-center justify-center text-center">
                                    <span class="text-2xl font-bold text-slate-900">620</span>
                                    <span class="text-xs text-slate-500 font-medium">Orderan</span>
                                </div>
                            </div>
                            <!-- Legend List -->
                            <div class="flex flex-col gap-3 w-full sm:w-auto flex-1">
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-2 font-medium text-slate-700"><span class="w-3 h-3 rounded-full bg-blue-600 inline-block"></span> Genio</div>
                                    <span class="font-bold text-slate-900">300</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-2 font-medium text-slate-700"><span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span> Supra X 125 FI</div>
                                    <span class="font-bold text-slate-900">150</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-2 font-medium text-slate-700"><span class="w-3 h-3 rounded-full bg-sky-300 inline-block"></span> X-Ride 125</div>
                                    <span class="font-bold text-slate-900">130</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-2 font-medium text-slate-700"><span class="w-3 h-3 rounded-full bg-indigo-300 inline-block"></span> XMAX 250</div>
                                    <span class="font-bold text-slate-900">81</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-2 font-medium text-slate-700"><span class="w-3 h-3 rounded-full bg-slate-300 inline-block"></span> Fino 125</div>
                                    <span class="font-bold text-slate-900">59</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Widget: Jumlah Kendaraan -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-blue-600 inline-block"></span> Jumlah Kendaraan</h3>
                            <span class="text-lg font-bold text-slate-900">30</span>
                        </div>
                        <div class="flex items-center justify-between text-sm py-2 border-b border-slate-100">
                            <span class="text-slate-600 font-medium">Tersedia</span>
                            <span class="font-semibold text-slate-900">10</span>
                        </div>
                        <div class="flex items-center justify-between text-sm py-2 border-b border-slate-100">
                            <span class="text-slate-600 font-medium">Jumlah bookingan</span>
                            <span class="font-semibold text-slate-900">10</span>
                        </div>
                        <div class="flex items-center justify-between text-sm py-2">
                            <span class="text-slate-600 font-medium">Sedang disewa</span>
                            <span class="font-semibold text-slate-900">10</span>
                        </div>
                    </div>

                    <!-- Widget: Pendapatan -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col gap-6">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-blue-600 inline-block"></span> Pendapatan</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-semibold text-slate-500">Tipe Kendaraan</span>
                                <div class="flex items-center justify-between border border-slate-200 px-3 py-2 rounded-lg text-sm bg-white">
                                    <span class="font-medium text-slate-800">Matic</span>
                                    <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-semibold text-slate-500">Awal</span>
                                <div class="flex items-center justify-between border border-slate-200 px-3 py-2 rounded-lg text-sm bg-white">
                                    <span class="font-medium text-slate-800">21 July 2022</span>
                                    <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-semibold text-slate-500">Akhir</span>
                                <div class="flex items-center justify-between border border-slate-200 px-3 py-2 rounded-lg text-sm bg-white">
                                    <span class="font-medium text-slate-800">21 July 2022</span>
                                    <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pt-2 border-t border-slate-100">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-900">Total Pendapatan</span>
                                <span class="text-xs text-slate-500">Seluruh pendapatan rental</span>
                            </div>
                            <span class="text-xl lg:text-2xl font-bold text-slate-900 mt-2 sm:mt-0">Rp100.000.000</span>
                        </div>
                    </div>

                </div>

                <!-- Right Column (Pesanan Terakhir) -->
                <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-slate-900">Pesanan Terakhir</h3>
                        <a href="#" class="text-xs font-semibold text-blue-600 hover:underline">Lihat semua</a>
                    </div>
                    
                    <!-- Pesanan List Items (Repeated exact to Figma) -->
                    <div class="flex flex-col divide-y divide-slate-100">
                        <!-- Item 1 -->
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=120&q=80" alt="Genio" class="w-16 h-12 object-contain bg-slate-50 rounded-lg p-1" onerror="this.src='https://placehold.co/100x70/f1f5f9/64748b?text=Genio'">
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm text-slate-900">Genio</span>
                                    <span class="text-xs text-slate-500">Matic</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-xs text-slate-400">24 Des 2025</span>
                                <span class="text-sm font-bold text-slate-900">Rp75.000</span>
                            </div>
                        </div>
                        <!-- Item 2 -->
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=120&q=80" alt="Genio" class="w-16 h-12 object-contain bg-slate-50 rounded-lg p-1" onerror="this.src='https://placehold.co/100x70/f1f5f9/64748b?text=Genio'">
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm text-slate-900">Genio</span>
                                    <span class="text-xs text-slate-500">Matic</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-xs text-slate-400">24 Des 2025</span>
                                <span class="text-sm font-bold text-slate-900">Rp75.000</span>
                            </div>
                        </div>
                        <!-- Item 3 -->
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=120&q=80" alt="Genio" class="w-16 h-12 object-contain bg-slate-50 rounded-lg p-1" onerror="this.src='https://placehold.co/100x70/f1f5f9/64748b?text=Genio'">
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm text-slate-900">Genio</span>
                                    <span class="text-xs text-slate-500">Matic</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-xs text-slate-400">24 Des 2025</span>
                                <span class="text-sm font-bold text-slate-900">Rp75.000</span>
                            </div>
                        </div>
                        <!-- Item 4 -->
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=120&q=80" alt="Genio" class="w-16 h-12 object-contain bg-slate-50 rounded-lg p-1" onerror="this.src='https://placehold.co/100x70/f1f5f9/64748b?text=Genio'">
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm text-slate-900">Genio</span>
                                    <span class="text-xs text-slate-500">Matic</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-xs text-slate-400">24 Des 2025</span>
                                <span class="text-sm font-bold text-slate-900">Rp75.000</span>
                            </div>
                        </div>
                        <!-- Item 5 -->
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=120&q=80" alt="Genio" class="w-16 h-12 object-contain bg-slate-50 rounded-lg p-1" onerror="this.src='https://placehold.co/100x70/f1f5f9/64748b?text=Genio'">
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm text-slate-900">Genio</span>
                                    <span class="text-xs text-slate-500">Matic</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-xs text-slate-400">24 Des 2025</span>
                                <span class="text-sm font-bold text-slate-900">Rp75.000</span>
                            </div>
                        </div>
                        <!-- Item 6 -->
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=120&q=80" alt="Genio" class="w-16 h-12 object-contain bg-slate-50 rounded-lg p-1" onerror="this.src='https://placehold.co/100x70/f1f5f9/64748b?text=Genio'">
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm text-slate-900">Genio</span>
                                    <span class="text-xs text-slate-500">Matic</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-xs text-slate-400">24 Des 2025</span>
                                <span class="text-sm font-bold text-slate-900">Rp75.000</span>
                            </div>
                        </div>
                        <!-- Item 7 -->
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=120&q=80" alt="Genio" class="w-16 h-12 object-contain bg-slate-50 rounded-lg p-1" onerror="this.src='https://placehold.co/100x70/f1f5f9/64748b?text=Genio'">
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm text-slate-900">Genio</span>
                                    <span class="text-xs text-slate-500">Matic</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-xs text-slate-400">24 Des 2025</span>
                                <span class="text-sm font-bold text-slate-900">Rp75.000</span>
                            </div>
                        </div>
                        <!-- Item 8 -->
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=120&q=80" alt="Genio" class="w-16 h-12 object-contain bg-slate-50 rounded-lg p-1" onerror="this.src='https://placehold.co/100x70/f1f5f9/64748b?text=Genio'">
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm text-slate-900">Genio</span>
                                    <span class="text-xs text-slate-500">Matic</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-xs text-slate-400">24 Des 2025</span>
                                <span class="text-sm font-bold text-slate-900">Rp75.000</span>
                            </div>
                        </div>
                        <!-- Item 9 -->
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <img src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=120&q=80" alt="Genio" class="w-16 h-12 object-contain bg-slate-50 rounded-lg p-1" onerror="this.src='https://placehold.co/100x70/f1f5f9/64748b?text=Genio'">
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm text-slate-900">Genio</span>
                                    <span class="text-xs text-slate-500">Matic</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-xs text-slate-400">24 Des 2025</span>
                                <span class="text-sm font-bold text-slate-900">Rp75.000</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <footer class="bg-white border-t border-slate-200 px-6 lg:px-16 py-12 mt-auto">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-8 mb-12">
            <!-- Col 1: Brand Info -->
            <div class="md:col-span-2 flex flex-col gap-4">
                <span class="text-blue-600 font-bold text-2xl tracking-wider">ED.RENT</span>
                <p class="text-slate-500 text-sm max-w-sm leading-relaxed">
                    Pilihan terlengkap, harga transparan, dan layanan 24 jam. Siap menemani setiap petualangan Anda.
                </p>
            </div>
            <!-- Col 2: About -->
            <div class="flex flex-col gap-3">
                <span class="font-bold text-slate-900 text-sm">About</span>
                <a href="#" class="text-slate-500 hover:text-slate-900 text-sm">How it works</a>
                <a href="#" class="text-slate-500 hover:text-slate-900 text-sm">Featured</a>
                <a href="#" class="text-slate-500 hover:text-slate-900 text-sm">Partnership</a>
                <a href="#" class="text-slate-500 hover:text-slate-900 text-sm">Bussiness Relation</a>
            </div>
            <!-- Col 3: Community -->
            <div class="flex flex-col gap-3">
                <span class="font-bold text-slate-900 text-sm">Community</span>
                <a href="#" class="text-slate-500 hover:text-slate-900 text-sm">Events</a>
                <a href="#" class="text-slate-500 hover:text-slate-900 text-sm">Blog</a>
                <a href="#" class="text-slate-500 hover:text-slate-900 text-sm">Podcast</a>
                <a href="#" class="text-slate-500 hover:text-slate-900 text-sm">Invite a friend</a>
            </div>
            <!-- Col 4: Socials -->
            <div class="flex flex-col gap-3">
                <span class="font-bold text-slate-900 text-sm">Socials</span>
                <a href="#" class="text-slate-500 hover:text-slate-900 text-sm">Discord</a>
                <a href="#" class="text-slate-500 hover:text-slate-900 text-sm">Instagram</a>
                <a href="#" class="text-slate-500 hover:text-slate-900 text-sm">Twitter</a>
                <a href="#" class="text-slate-500 hover:text-slate-900 text-sm">Facebook</a>
            </div>
        </div>
        <div class="border-t border-slate-200 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4">
            <span>&copy;2025 ED.RENT. All rights reserved</span>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-slate-700">Privacy & Policy</a>
                <a href="#" class="hover:text-slate-700">Terms & Condition</a>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');

            function toggleSidebar() {
                const isOpen = sidebar.classList.toggle('active');
                sidebarBackdrop.classList.toggle('hidden');
                sidebarToggle.setAttribute('aria-expanded', isOpen);
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }
            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', toggleSidebar);
            }
        });
    </script>
</body>
</html>