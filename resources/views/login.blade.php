<!DOCTYPE html>
<html lang="en" :class="{ 'theme-dark': dark }" x-data="data()">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Desa Digital</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="{{ asset('assets/css/tailwind.output.css') }}" />
    <script
      src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"
      defer
    ></script>
    <script src="{{ asset('assets/js/init-alpine.js') }}"></script>
  </head>
  <body>
    <div class="flex items-center min-h-screen p-6 bg-gray-50 dark:bg-gray-900">
      <div class="flex-1 h-full max-w-4xl mx-auto overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800">
        <div class="flex flex-col overflow-y-auto md:flex-row">
          <div class="h-32 md:h-auto md:w-1/2">
            <img aria-hidden="true" class="object-cover w-full h-full dark:hidden" src="{{ asset('assets/images/logo.png') }}" alt="Office" />
            <img aria-hidden="true" class="hidden object-cover w-full h-full dark:block" src="{{ asset('assets/images/logo.png') }}" alt="Office" />
          </div>

          <div class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
            <div class="w-full">
              <h1 class="mb-4 text-xl font-semibold text-gray-700 dark:text-gray-200">Login</h1>

              @if ($errors->any())
                <div class="mb-4 text-sm text-blue-600 bg-blue-100 border border-blue-300 rounded-lg p-2">
                  {{ $errors->first() }}
                </div>
              @endif

              <form method="POST" action="{{ route('login.process') }}">
                @csrf

                <label class="block text-sm">
                  <span class="text-gray-700 dark:text-gray-400">Email</span>
                  <input
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="block w-full mt-1 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-400 focus:outline-none focus:shadow-outline-blue form-input"
                    placeholder="Masukkan email"
                    autocomplete="off"
                  />
                </label>

                <label class="block mt-4 text-sm">
                  <span class="text-gray-700 dark:text-gray-400">Password</span>
                  <input
                    type="password"
                    name="password"
                    required
                    class="block w-full mt-1 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-400 focus:outline-none focus:shadow-outline-blue form-input"
                    placeholder="**********"
                    autocomplete="off"
                  />
                </label>

                <button
                  type="submit"
                  class="block w-full px-4 py-2 mt-6 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-blue-600 rounded-lg active:bg-blue-700 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue"
                >
                  Masuk
                </button>
              </form>

              <p class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
                Desa Digital &copy; {{ date('Y') }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
