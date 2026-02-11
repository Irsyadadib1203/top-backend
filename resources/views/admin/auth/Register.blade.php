<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="{{ asset('assets/css/tailwind.output.css') }}">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
    <h1 class="text-xl font-semibold mb-6 text-center">Buat User Pertama</h1>

    @if ($errors->any())
      <div class="mb-4 bg-red-100 text-red-700 p-2 rounded">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('register.process') }}">
      @csrf

      <div class="mb-4">
        <label class="block text-sm mb-1">Nama</label>
        <input type="text" name="name" required
          class="w-full border rounded p-2">
      </div>

      <div class="mb-4">
        <label class="block text-sm mb-1">email</label>
        <input type="text" name="email" required
          class="w-full border rounded p-2">
      </div>

      <div class="mb-4">
        <label class="block text-sm mb-1">Password</label>
        <input type="password" name="password" required
          class="w-full border rounded p-2">
      </div>

      <div class="mb-6">
        <label class="block text-sm mb-1">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" required
          class="w-full border rounded p-2">
      </div>

      <button
        type="submit"
        class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
        Buat Akun
      </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-4">
      Hanya digunakan sekali untuk setup awal
    </p>
  </div>

</body>
</html>
