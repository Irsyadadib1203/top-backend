@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<main class="h-full pb-16 overflow-y-auto"
x-data="{
    showEdit: false,
    showDelete: false,
    showAdd: false,
    pengguna: {},
    csrf: '{{ csrf_token() }}',

    openEditModal(id, nama, username, role, status) {
        this.pengguna = {
            id_pengguna: id,
            nm_pengguna: nama,
            username: username,
            role: role,
            status: status
        };
        this.showEdit = true;
    },

    openDeleteModal(id, nama) {
        this.pengguna = {
            id_pengguna: id,
            nm_pengguna: nama,
        };
        this.showDelete = true;
    }
}">

    <div class="flex items-center justify-between my-6">
      <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
        Manajemen Pengguna
      </h2>
      <button
        @click="showAdd = true"
        class="flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none transition"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Pengguna
      </button>
    </div>

    <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs">
      <div class="w-full overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
          <thead>
            <tr
              class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800"
            >
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">Username</th>
              <th class="px-4 py-3">Role</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
            @forelse($pengguna as $item)
              <tr class="text-gray-700 dark:text-gray-400">
                <td class="px-4 py-3">
                  <div class="flex items-center text-sm">
                    <div class="relative hidden w-8 h-8 mr-3 rounded-full md:block">
                      <img
                        class="object-cover w-full h-full rounded-full"
                        src="https://ui-avatars.com/api/?name={{ urlencode($item->nm_pengguna) }}&background=random"
                        alt="{{ $item->nm_pengguna }}"
                      />
                      <div class="absolute inset-0 rounded-full shadow-inner" aria-hidden="true"></div>
                    </div>
                    <div>
                      <p class="font-semibold">{{ $item->nm_pengguna }}</p>
                      <p class="text-xs text-gray-600 dark:text-gray-400">{{ $item->username }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-sm">{{ $item->username }}</td>
                <td class="px-4 py-3 text-sm capitalize">{{ $item->role }}</td>
                <td class="px-4 py-3 text-xs">
                  <span
                    class="px-2 py-1 font-semibold leading-tight rounded-full
                      {{ $item->status == 'aktif' ? 'text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100' : 'text-blue-700 bg-blue-100 dark:bg-blue-700 dark:text-blue-100' }}">
                    {{ ucfirst($item->status) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm">
                  <div class="flex items-center space-x-3">
                    <!-- Tombol Edit -->
                    <button 
                      @click="openEditModal({{ $item->id_pengguna }}, '{{ $item->nm_pengguna }}', '{{ $item->username }}', '{{ $item->role }}', '{{ $item->status }}')" 
                      class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-blue-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                      title="Edit Pengguna">
                       <svg
                              class="w-5 h-5"
                              aria-hidden="true"
                              fill="currentColor"
                              viewBox="0 0 20 20"
                            >
                              <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                              ></path>
                    </button>

                    <!-- Tombol Hapus -->
                    <button 
                      @click="openDeleteModal({{ $item->id_pengguna }}, '{{ $item->nm_pengguna }}')" 
                      class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-blue-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                      title="Hapus Pengguna">
                     <svg
                              class="w-5 h-5"
                              aria-hidden="true"
                              fill="currentColor"
                              viewBox="0 0 20 20"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd"
                              ></path>
                            </svg>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                  Tidak ada data pengguna.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

  <!-- BACKDROP -->
  <div 
    x-show="showEdit || showDelete || showAdd"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30"
    x-transition.opacity
    x-cloak
  >

  <!-- MODAL TAMBAH PENGGUNA -->
<div 
  x-show="showAdd"
  x-transition:enter="transition ease-out duration-150"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-150"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  class="fixed inset-0 z-30 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
>
  <div x-show="showAdd"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 transform translate-y-1/2"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 transform translate-y-1/2"
        @click.away="showAddModal = false"
        @keydown.escape="showAddModal = false"
        class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl rounded-xl w-full shadow-xl max-h-[90vh] overflow-y-auto"
  >
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 text-center mb-2">
      Tambah Pengguna
    </h3>

    <form action="{{ route('pengguna.store') }}" method="POST" class="space-y-4">
      @csrf

      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Nama Pengguna</label>
        <input type="text" name="nm_pengguna"
               class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500"
               required>
      </div>

      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Username</label>
        <input type="text" name="username"
               class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500"
               required>
      </div>

      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Password</label>
        <input type="password" name="password"
               class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500"
               required>
      </div>

      <div class="flex space-x-3">
        <div class="w-1/2 space-y-1">
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Role</label>
          <select name="role"
                  class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
            <option value="superadmin">Superadmin</option>
            <option value="admin">Admin</option>
            <option value="operator">Operator</option>
          </select>
        </div>

        <div class="w-1/2 space-y-1">
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Status</label>
          <select name="status"
                  class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>
      </div>

      <div class="flex justify-end space-x-3 pt-3">
        <button type="button" @click="showAdd = false"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
          Batal
        </button>

        <button type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
          Simpan
        </button>
      </div>
    </form>
  </div>
</div>

    <!-- Modal Edit Pengguna -->
<div 
  x-show="showEdit"
  x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-30 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
    x-cloak
  @click.away="showEdit = false"
>
  <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-96 p-6 space-y-4 transform transition-all" style="border-radius: 20px;">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 text-center mb-2">
      Edit Pengguna
    </h3>

    <form :action="`/pengguna/${pengguna.id_pengguna}/update`" method="POST" class="space-y-4">
      @csrf
      @method('PUT')

      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Nama Pengguna</label>
        <input type="text" name="nm_pengguna" x-model="pengguna.nm_pengguna"
               class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-gray-700 dark:text-gray-100">
      </div>

      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Username</label>
        <input type="text" name="username" x-model="pengguna.username"
               class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-gray-700 dark:text-gray-100">
      </div>

      <div class="flex space-x-3">
        <div class="w-1/2 space-y-1">
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Role</label>
          <select name="role" x-model="pengguna.role"
                  class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
            <option value="superadmin">Superadmin</option>
            <option value="admin">Admin</option>
            <option value="operator">Operator</option>
          </select>
        </div>
        <div class="w-1/2 space-y-1">
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Status</label>
          <select name="status" x-model="pengguna.status"
                  class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>
      </div>
        <div class="h-8"></div>

      <div class="flex justify-end space-x-3 pt-3">
        <button type="button" @click="showEdit = false"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
          Batal
        </button>
        <button type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
          Simpan
        </button>
      </div>
    </form>
  </div>
</div>


     <!-- 🗑️ MODAL HAPUS -->
<div 
  x-show="showDelete"
 x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-30 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
    x-cloak
  @click.away="showDelete = false"
>
  <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-96 p-6  max-w-4xl transform transition-all" style="border-radius: 20px;">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 text-center">
      Konfirmasi Hapus
    </h3>

    <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
      Apakah kamu yakin ingin menghapus pengguna 
      <span class="font-semibold text-blue-600" x-text="pengguna.nm_pengguna"></span>?
    </p>

    <div class="h-4"></div>

    <form :action="`/pengguna/${pengguna.id_pengguna}`" method="POST" class="flex justify-end space-x-3 pt-3">
      @csrf
      @method('DELETE')

      <button type="button" @click="showDelete = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
        Batal
      </button>

      <button type="submit"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition">
        Hapus
      </button>
    </form>
  </div>
</div>


      </div>
    </div>
  </div>
</main>

<script>
  function openEditModal(id, nm_pengguna, username, role, status) {
    window.dispatchEvent(new CustomEvent('open-edit-modal', {
      detail: { id_pengguna: id, nm_pengguna, username, role, status }
    }));
  }

  function openDeleteModal(id, nm_pengguna) {
    window.dispatchEvent(new CustomEvent('open-delete-modal', {
      detail: { id_pengguna: id, nm_pengguna }
    }));
  }
</script>


@endsection
