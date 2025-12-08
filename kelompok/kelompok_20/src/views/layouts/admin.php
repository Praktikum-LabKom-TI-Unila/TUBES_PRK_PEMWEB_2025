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
            src="iconlost&found.jpg"
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
            href="#"
            onclick="showSection('dashboard')"
            id="nav-dashboard"
            class="active-nav group flex items-center px-4 py-3 text-sm font-medium rounded-r-md hover:bg-gray-800 hover:text-cyan-400 transition"
          >
            <i class="fa-solid fa-gauge mr-3 text-lg"></i>
            Dashboard
          </a>

          <a
            href="#"
            onclick="showSection('items')"
            id="nav-items"
            class="group flex items-center px-4 py-3 text-sm font-medium rounded-r-md hover:bg-gray-800 hover:text-cyan-400 transition"
          >
            <i class="fa-solid fa-box-open mr-3 text-lg"></i>
            Data Barang
          </a>

          <a
            href="#"
            onclick="showSection('users')"
            id="nav-users"
            class="group flex items-center px-4 py-3 text-sm font-medium rounded-r-md hover:bg-gray-800 hover:text-cyan-400 transition"
          >
            <i class="fa-solid fa-users mr-3 text-lg"></i>
            Data User
          </a>

          <a
            href="#"
            onclick="showSection('master')"
            id="nav-master"
            class="group flex items-center px-4 py-3 text-sm font-medium rounded-r-md hover:bg-gray-800 hover:text-cyan-400 transition"
          >
            <i class="fa-solid fa-database mr-3 text-lg"></i>
            Master Data
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
            <?php echo $content; ?>
        </main>
      </div>
    </div>

    <script src="/assets/js/main.js"></script>
  </body>
</html>
