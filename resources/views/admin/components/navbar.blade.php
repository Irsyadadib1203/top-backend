<!-- Navbar -->
<header class="z-10 py-4 bg-white shadow-md dark:bg-gray-800">
  <div
    class="container flex items-center justify-between h-full px-6 mx-auto text-blue-600 dark:text-blue-300"
  >
    <!-- Left section -->
    <div class="flex items-center">
      <!-- Hamburger -->
      <button
        class="p-1 mr-5 -ml-1 rounded-md md:hidden focus:outline-none focus:shadow-outline-blue"
        @click="toggleSideMenu"
        aria-label="Menu"
      >
        <svg
          class="w-6 h-6"
          fill="currentColor"
          viewBox="0 0 20 20"
          aria-hidden="true"
        >
          <path
            fill-rule="evenodd"
            d="M3 5h14a1 1 0 010 2H3a1 1 0 010-2zm0 4h14a1 1 0 010 2H3a1 1 0 010-2zm0 4h14a1 1 0 010 2H3a1 1 0 010-2z"
            clip-rule="evenodd"
          ></path>
        </svg>
      </button>

      <!-- Informasi singkat -->
      <span class="hidden text-sm font-medium text-gray-600 dark:text-gray-300 md:block">
        Selamat datang di <span class="font-semibold text-blue-600 dark:text-blue-400">Dashboard {{ Auth::user()->role }}</span>
      </span>
    </div>

    <!-- Right section -->
    <div class="flex items-center space-x-4">
      <!-- Theme toggler -->
      <li class="flex">
        <button class="rounded-md focus:outline-none focus:shadow-outline-blue" @click="toggleTheme" aria-label="Toggle color mode">
          <template x-if="!dark">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 2a1 1 0 000 2 6 6 0 016 6 1 1 0 002 0 8 8 0 00-8-8zM10 18a8 8 0 008-8 1 1 0 00-2 0 6 6 0 01-6 6 1 1 0 000 2z"/>
            </svg>
          </template>
          <template x-if="dark">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
            </svg>
          </template>
        </button>
      </li>
      <!-- Tombol Logout -->
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button
          type="submit"
          class="flex items-center px-3 py-2 text-sm font-medium text-white transition-colors duration-150 bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5m0 0a9 9 0 100 18 9 9 0 000-18z" />
          </svg>
          Logout
        </button>
      </form>
    </div>
  </div>
</header>
