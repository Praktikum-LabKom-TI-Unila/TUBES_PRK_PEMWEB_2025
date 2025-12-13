<div id="section-dashboard" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-cyan-500 flex items-center hover:shadow-md transition">
                <div class="p-3 rounded-full bg-cyan-50 text-cyan-600 mr-4">
                  <i class="fa-solid fa-box text-xl"></i>
                </div>
                <div>
                  <p class="text-gray-500 text-xs font-semibold uppercase">Total Postingan</p>
                  <p class="text-2xl font-bold text-slate-800"><?= $stats['total_items'] ?? 0 ?></p>
                </div>
              </div>

              <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500 flex items-center hover:shadow-md transition">
                <div class="p-3 rounded-full bg-green-50 text-green-600 mr-4">
                  <i class="fa-solid fa-check-circle text-xl"></i>
                </div>
                <div>
                  <p class="text-gray-500 text-xs font-semibold uppercase">Barang Ditemukan</p>
                  <p class="text-2xl font-bold text-slate-800"><?= $stats['total_found'] ?? 0 ?></p>
                </div>
              </div>

              <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500 flex items-center hover:shadow-md transition">
                <div class="p-3 rounded-full bg-purple-50 text-purple-600 mr-4">
                  <i class="fa-solid fa-users text-xl"></i>
                </div>
                <div>
                  <p class="text-gray-500 text-xs font-semibold uppercase">Total User</p>
                  <p class="text-2xl font-bold text-slate-800"><?= $stats['total_users'] ?? 0 ?></p>
                </div>
              </div>

              <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500 flex items-center hover:shadow-md transition">
                <div class="p-3 rounded-full bg-yellow-50 text-yellow-600 mr-4">
                  <i class="fa-solid fa-handshake text-xl"></i>
                </div>
                <div>
                  <p class="text-gray-500 text-xs font-semibold uppercase">Klaim Terverifikasi</p>
                  <p class="text-2xl font-bold text-slate-800">
                    <?= $stats['total_verified_claims'] ?? 0 ?>
                  </p>
                </div>
              </div>
            </div>

            <div
              class="rounded-xl shadow-lg p-8 text-white relative overflow-hidden"
            >
              <div
                class="absolute inset-0 bg-gradient-to-r from-cyan-600 to-blue-700"
              ></div>
              <div class="relative z-10 flex justify-between items-center">
                <div>
                  <h3 class="text-3xl font-bold mb-2">
                    Selamat Datang, Admin!
                  </h3>
                  <p class="text-cyan-100 max-w-xl">
                    Kelola data kehilangan dan penemuan barang di lingkungan
                    Universitas Lampung dengan mudah melalui panel ini.
                  </p>
                </div>
                <i
                  class="fa-solid fa-shield-halved text-8xl text-white opacity-10 hidden md:block absolute right-10 -bottom-6"
                ></i>
              </div>
            </div>

          </div>
