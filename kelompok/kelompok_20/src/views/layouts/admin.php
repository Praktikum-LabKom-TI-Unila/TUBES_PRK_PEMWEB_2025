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
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

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
            src="assets\images\iconlost&found.png" 
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
            href="<?= base_url('index.php?page=admin&action=dashboard') ?>"
            data-ajax="1"
            id="nav-dashboard"
            class="active-nav group flex items-center px-4 py-3 text-sm font-medium rounded-r-md hover:bg-gray-800 hover:text-cyan-400 transition"
          >
            <i class="fa-solid fa-gauge mr-3 text-lg"></i>
            Dashboard
          </a>

          <a
            href="<?= base_url('index.php?page=admin&action=items') ?>"
            data-ajax="1"
            id="nav-items"
            class="group flex items-center px-4 py-3 text-sm font-medium rounded-r-md hover:bg-gray-800 hover:text-cyan-400 transition"
          >
            <i class="fa-solid fa-box-open mr-3 text-lg"></i>
            Data Barang
          </a>

          <a
            href="<?= base_url('index.php?page=admin&action=users') ?>"
            data-ajax="1"
            id="nav-users"
            class="group flex items-center px-4 py-3 text-sm font-medium rounded-r-md hover:bg-gray-800 hover:text-cyan-400 transition"
          >
            <i class="fa-solid fa-users mr-3 text-lg"></i>
            Data User
          </a>

        </nav>

        <div class="p-4 border-t border-gray-700 bg-slate-900 space-y-2">
          <a
            href="<?= base_url('index.php?page=home') ?>"
            class="flex items-center text-sm font-medium text-gray-400 hover:text-cyan-400 transition px-4 py-2 rounded-md hover:bg-gray-800"
          >
            <i class="fa-solid fa-arrow-right-from-bracket mr-3"></i>
            Ke Mode User
          </a>
          <a
            href="<?= base_url('index.php?page=auth&action=logout') ?>"
            class="flex items-center text-sm font-medium text-gray-400 hover:text-red-400 transition px-4 py-2 rounded-md hover:bg-gray-800"
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
    <script>
    // Admin sidebar: intercept clicks on links with data-ajax to load fragment content
    (function(){
      const mainEl = document.querySelector('main');
      const navLinks = document.querySelectorAll('aside nav a[data-ajax]');

      function setActive(id) {
        navLinks.forEach(a => a.classList.remove('active-nav'));
        const el = document.getElementById(id);
        if (el) el.classList.add('active-nav');
      }

      async function loadFragment(url, push = true) {
        try {
          const fetchUrl = url + (url.includes('?') ? '&' : '?') + 'ajax=1';
          const res = await fetch(fetchUrl, { credentials: 'same-origin' });
          if (!res.ok) throw new Error('Network response was not ok');
          const html = await res.text();
          mainEl.innerHTML = html;
          if (push) history.pushState({ fragment: url }, '', url);
        } catch (err) {
          console.error('Failed to load fragment', err);
          // Fallback: full navigation
          window.location.href = url;
        }
      }

      navLinks.forEach(a => {
        a.addEventListener('click', function(e){
          e.preventDefault();
          const url = this.getAttribute('href');
          loadFragment(url);
          // update active class
          const id = this.id;
          setActive(id);
          const titleText = this.textContent.trim();
          const pageTitle = document.getElementById('page-title');
          if (pageTitle) pageTitle.textContent = titleText;
        });
      });

      // handle back/forward
      window.addEventListener('popstate', function(e){
        const state = e.state;
        if (state && state.fragment) {
          loadFragment(state.fragment, false);
        } else {
          // reload full page
          window.location.reload();
        }
      });

      // ADMIN ACTIONS: delegate delete/ban buttons inside main content
      document.querySelector('main').addEventListener('click', async function(e){
        const target = e.target.closest('[data-admin-action]');
        if (!target) return;
        const action = target.getAttribute('data-admin-action');
        const id = target.getAttribute('data-id');

        if (!action || !id) return;

        if (!confirm(target.getAttribute('data-confirm') || 'Yakin?')) return;

        try {
          const form = new FormData();
          form.append('id', id);

          const url = `<?= base_url('index.php?page=admin&action=') ?>${action}`;
          const res = await fetch(url, {
            method: 'POST',
            body: form,
            credentials: 'same-origin',
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          const json = await res.json();
          if (json.success) {
            const row = target.closest('tr');

            if (action === 'toggle_active') {
              if (row) {
                const uid = json.user_id;
                const badge = row.querySelector('#status-badge-' + uid);
                if (badge) {
                  const isActive = Number(json.is_active);
                  if (isActive === 1) {
                    badge.textContent = 'Active';
                    badge.className = 'px-2.5 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700';
                  } else {
                    badge.textContent = 'Inactive';
                    badge.className = 'px-2.5 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700';
                  }
                }

                // update the button label and classes
                if (target) {
                  if (Number(json.is_active) === 1) {
                    target.innerHTML = '<i class="fa-solid fa-ban mr-1"></i> Ban User';
                    target.className = 'text-amber-600 hover:text-white hover:bg-amber-500 border border-amber-200 px-3 py-1.5 rounded-md transition text-xs font-medium shadow-sm w-24';
                  } else {
                    target.innerHTML = '<i class="fa-solid fa-check mr-1"></i> Unban';
                    target.className = 'text-green-600 hover:text-white hover:bg-green-500 border border-green-200 px-3 py-1.5 rounded-md transition text-xs font-medium shadow-sm w-24';
                  }
                }
              }
            } else if (action === 'delete_item') {
              if (row) row.remove();
            } else if (action === 'delete_user') {
              // legacy: remove row
              if (row) row.remove();
            }

            alert(json.message || 'Berhasil');
          } else {
            alert(json.message || 'Gagal melakukan aksi');
          }
        } catch (err) {
          console.error('Admin action error', err);
          alert('Terjadi kesalahan. Cek console.');
        }
      });
    })();
    </script>
  </body>
</html>
