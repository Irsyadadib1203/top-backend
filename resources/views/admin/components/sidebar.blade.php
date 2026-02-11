<!-- Sidebar -->
<aside class="z-20 hidden w-64 overflow-y-auto overflow-x-visible bg-white dark:bg-gray-800 md:block flex-shrink-0">

  <div class="py-4 text-gray-500 dark:text-gray-400">
    <a class="ml-6 text-lg font-bold text-gray-800 dark:text-gray-200" href="#">
      IRXPlay Admin
    </a>
    <ul class="mt-6">
<li class="relative px-6 py-3">
  <a href="{{ route('admin.dashboard') }}"
     class="inline-flex items-center w-full text-sm font-semibold
            px-2 py-2 rounded-lg transition-all duration-200
            {{ request()->routeIs('admin.dashboard')
                ? 'bg-blue-600 text-white pl-8 hover:bg-blue-700 focus:bg-blue-700'
                : 'hover:bg-gray-100 dark:hover:bg-gray-700 
                   hover:text-gray-800 dark:hover:text-gray-200 
                   hover:pl-8' }}">
            
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10h3m10-11l2 2v9h-3v-4h-6v4H8"/>
    </svg>

    <span class="ml-4">Dashboard</span>
  </a>
</li>
<!-- Manajemen Pengguna -->
      @if(auth()->check() && auth()->user()->isSuperadmin())
      <li class="relative px-6 py-3">
        <a href="{{ route('pengguna') }}"
            class="inline-flex items-center w-full text-sm font-semibold
            px-2 py-2 rounded-lg transition-all duration-200
            {{ request()->routeIs('pengguna')
                ? 'bg-blue-600 text-white pl-8 hover:bg-blue-700 focus:bg-blue-700'
                : 'hover:bg-gray-100 dark:hover:bg-gray-700 
                   hover:text-gray-800 dark:hover:text-gray-200 
                   hover:pl-8' }}">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
          </svg>
          <span class="ml-4">Manajemen Pengguna</span>
        </a>
      </li>
      @endif
      
      @if(auth()->check() && auth()->user()->isAdmin())
      <li class="relative px-6 py-3">
        <a href="{{ route('transaction') }}"
            class="inline-flex items-center w-full text-sm font-semibold
            px-2 py-2 rounded-lg transition-all duration-200
            {{ request()->routeIs('transaction')
                ? 'bg-blue-600 text-white pl-8 hover:bg-blue-700 focus:bg-blue-700'
                : 'hover:bg-gray-100 dark:hover:bg-gray-700 
                   hover:text-gray-800 dark:hover:text-gray-200 
                   hover:pl-8' }}">
        <i class="bi bi-images"></i>
    <span class="ml-4"> Transaction</span>
    </a>
      </li>
     
      <li class="relative px-6 py-3" x-data="{ open: {{ request()->routeIs(
            'game', 'nominal'
          ) ? 'true' : 'false' }} }">

      <button
        @click="open = !open"
        class="inline-flex items-center w-full text-sm font-semibold
              px-2 py-2 rounded-lg transition-all duration-200
              hover:bg-gray-100 dark:hover:bg-gray-700
              hover:text-gray-800 dark:hover:text-gray-200
              hover:pl-8
              {{ request()->routeIs('lembaga', 'rt', 'pkk', 'game', 'nominal')
              ? 'bg-blue-600 text-white pl-8 hover:bg-blue-700 focus:bg-blue-700'    
              : '' }}">
          <span class="inline-flex items-center">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 21h18M4 10h16M10 21V6h4v15M7 10V6h10v4" />
        </svg>
          <span class="ml-4">Produk</span>
          </span>
          <svg class="w-4 h-4 transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
              clip-rule="evenodd"></path>
          </svg>
        </button>
  <!-- SUBMENU -->
  <ul x-show="open" class="mt-2 pl-8 space-y-1 text-sm">
   
    <li>
      <a href="{{ route('game') }}"
        class="block px-2 py-2 rounded-md text-gray-600 dark:text-gray-400
               hover:bg-gray-100 dark:hover:bg-gray-700
               hover:text-gray-800 dark:hover:text-gray-200">
        Game
      </a>
    </li>
<li>
      <a href="{{ route('nominal') }}"
        class="block px-2 py-2 rounded-md text-gray-600 dark:text-gray-400
               hover:bg-gray-100 dark:hover:bg-gray-700
               hover:text-gray-800 dark:hover:text-gray-200">
        Nominal
      </a>
    </li>
    </ul>
</li>
    </ul>
</li>


      @endif

      <!-- Tambahkan menu lain di sini -->
    </ul>
  </div>
</aside>

<!-- Sidebar Mobile -->
<div
  x-show="isSideMenuOpen"
  x-transition:enter="transition ease-in-out duration-150"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in-out duration-150"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  @click="closeSideMenu"
  class="fixed inset-0 z-10 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center md:hidden"
></div>

<aside
  class="fixed inset-y-0 z-20 w-64 mt-16 overflow-y-auto bg-white dark:bg-gray-800 md:hidden flex-shrink-0"
  x-show="isSideMenuOpen"
  x-transition:enter="transition ease-in-out duration-150"
  x-transition:enter-start="opacity-0 transform -translate-x-20"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in-out duration-150"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0 transform -translate-x-20"
  @click.away="closeSideMenu"
  @keydown.escape="closeSideMenu"
>
  <div class="py-4 text-gray-500 dark:text-gray-400">
    <a class="ml-6 text-lg font-bold text-gray-800 dark:text-gray-200" href="#">
      IRXPlay Admin
    </a>
    <ul class="mt-6">
      <li class="relative px-6 py-3">
        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10h3m10-11l2 2v9h-3v-4h-6v4H8"/>
          </svg>
          <span class="ml-4">Dashboard</span>
        </a>
      </li>

      <li class="relative px-6 py-3">
        <a href="{{ route('transaction') }}"
           class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
          <i class="bi bi-images"></i>
          <span class="ml-4">Transaction</span>
        </a>
      </li>
     

      <!-- Lembaga -->
      <li class="relative px-6 py-3">
        <a href="{{ route('transaction') }}"
           class="inline-flex items-center w-full text-sm font-semibold hover:text-gray-800 dark:hover:text-gray-200">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M9 5h6v14H9z"></path>
          </svg>
          <span class="ml-4">Transaksi</span>
        </a>
      </li>

      <!-- Manajemen Pengguna -->
      @if(auth()->check() && auth()->user()->role == 'superadmin')
      <li class="relative px-6 py-3">
        <a href="{{ route('pengguna') }}"
           class="inline-flex items-center w-full text-sm font-semibold hover:text-gray-800 dark:hover:text-gray-200">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
          </svg>
          <span class="ml-4">Manajemen Pengguna</span>
        </a>
      </li>
      @endif

      

      <!-- Bansos -->
      <li class="relative px-6 py-3">
        <a href="{{ route('nominal') }}"
           class="inline-flex items-center w-full text-sm font-semibold hover:text-gray-800 dark:hover:text-gray-200">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M4 6h16M4 10h16M4 14h16"></path>
          </svg>
          <span class="ml-4">Nominal</span>
        </a>
      </li>
      
    </ul>
  </div>
</aside>