<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel - myUnila Lost & Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />

    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              unila: {
                dark: "#0f172a",
                primary: "#0ea5e9",
                secondary: "#3b82f6",
              },
            },
          },
        },
      };
    </script>
    <style>
      body {
        font-family: "Inter", sans-serif;
      }

      .active-nav {
        background-color: rgba(255, 255, 255, 0.1);
        border-left: 4px solid #0ea5e9;
        color: #38bdf8;
      }
    </style>
  </head>
  <body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
      <aside
        class="w-64 bg-slate-900 text-white flex-shrink-0 hidden md:flex flex-col shadow-xl"
      >
        <div class="p-6 flex items-center border-b border-gray-700">
          <img
            src="../../assets/images/iconlost&found.png"
            alt="Logo myUnila"
            class="w-10 h-10 rounded-full object-cover border-2 border-cyan-500 mr-3"
          />

          <div>
            <h1 class="text-lg font-bold tracking-wide">myUnila</h1>
            <p class="text-xs text-cyan-400 tracking-wider font-semibold">
              LOST & FOUND
            </p>
          </div>
        </div>

        <nav class="mt-6 flex-1 px-2 space-y-2">
          <a
            href="dashboard.php"
            id="nav-dashboard"
            class="active-nav group flex items-center px-4 py-3 text-sm font-medium rounded-r-md hover:bg-gray-800 hover:text-cyan-400 transition"
          >
            <i class="fa-solid fa-gauge mr-3 text-lg"></i>
            Dashboard
          </a>

          <a
            href="items.php"
            id="nav-items"
            class="group flex items-center px-4 py-3 text-sm font-medium rounded-r-md hover:bg-gray-800 hover:text-cyan-400 transition"
          >
            <i class="fa-solid fa-box-open mr-3 text-lg"></i>
            Data Barang
          </a>

          <a
            href="users.php"
            id="nav-users"
            class="group flex items-center px-4 py-3 text-sm font-medium rounded-r-md hover:bg-gray-800 hover:text-cyan-400 transition"
          >
            <i class="fa-solid fa-users mr-3 text-lg"></i>
            Data User
          </a>

          
        </nav>

        <div class="p-4 border-t border-gray-700 bg-slate-900">
          <a
            href="#"
            class="flex items-center text-sm font-medium text-gray-400 hover:text-red-400 transition"
          >
            <i class="fa-solid fa-right-from-bracket mr-3"></i>
            Logout
          </a>
        </div>
      </aside>

      <div class="flex-1 flex flex-col overflow-hidden relative">
        <header class="bg-white shadow-sm z-10 border-b border-gray-200">
          <div class="flex items-center justify-between px-6 py-4">
            <button class="md:hidden text-gray-500 focus:outline-none">
              <i class="fa-solid fa-bars text-xl"></i>
            </button>
            <h2 class="text-xl font-bold text-slate-800" id="page-title">
              Dashboard Overview
            </h2>
            <div class="flex items-center space-x-4">
              <div class="relative cursor-pointer">
                <i
                  class="fa-regular fa-bell text-gray-500 text-xl hover:text-cyan-600 transition"
                ></i>
                <span
                  class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-500"
                ></span>
              </div>
              <div
                class="flex items-center gap-2 border-l pl-4 border-gray-200"
              >
                <div class="text-right hidden sm:block">
                  <p class="text-sm font-semibold text-slate-800">
                    Admin Unila
                  </p>
                  <p class="text-xs text-gray-500">Super Administrator</p>
                </div>
                <img
                  class="h-9 w-9 rounded-full object-cover border border-gray-300"
                  src="https://ui-avatars.com/api/?name=Admin+Unila&background=0f172a&color=0ea5e9"
                  alt="Admin Profile"
                />
              </div>
            </div>
          </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-6">
          <div id="section-dashboard" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div
                class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-cyan-500 flex items-center hover:shadow-md transition"
              >
                <div class="p-3 rounded-full bg-cyan-50 text-cyan-600 mr-4">
                  <i class="fa-solid fa-box text-xl"></i>
                </div>
                <div>
                  <p class="text-gray-500 text-xs font-semibold uppercase">
                    Total Postingan
                  </p>
                  <p class="text-2xl font-bold text-slate-800">1,240</p>
                </div>
              </div>
              <div
                class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500 flex items-center hover:shadow-md transition"
              >
                <div class="p-3 rounded-full bg-green-50 text-green-600 mr-4">
                  <i class="fa-solid fa-check-circle text-xl"></i>
                </div>
                <div>
                  <p class="text-gray-500 text-xs font-semibold uppercase">
                    Barang Ditemukan
                  </p>
                  <p class="text-2xl font-bold text-slate-800">856</p>
                </div>
              </div>
              <div
                class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500 flex items-center hover:shadow-md transition"
              >
                <div class="p-3 rounded-full bg-purple-50 text-purple-600 mr-4">
                  <i class="fa-solid fa-users text-xl"></i>
                </div>
                <div>
                  <p class="text-gray-500 text-xs font-semibold uppercase">
                    Total User
                  </p>
                  <p class="text-2xl font-bold text-slate-800">3,400</p>
                </div>
              </div>
              <div
                class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500 flex items-center hover:shadow-md transition"
              >
                <div class="p-3 rounded-full bg-red-50 text-red-600 mr-4">
                  <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                </div>
                <div>
                  <p class="text-gray-500 text-xs font-semibold uppercase">
                    Laporan Spam
                  </p>
                  <p class="text-2xl font-bold text-slate-800">12</p>
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

        </main>
      </div>
    </div>

    <script src="../../assets/js/main.js"></script>
  </body>
</html>
